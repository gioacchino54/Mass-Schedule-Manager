<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Script di installazione/aggiornamento
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Component\ComponentHelper;

class Com_MesseInstallerScript
{
    /**
     * Rete di sicurezza per la disinstallazione: elimina le tabelle
     * direttamente via PHP, indipendentemente dall'esecuzione (o meno)
     * del tag <uninstall><sql> del manifest.
     *
     * Le tabelle vengono eliminate SOLO se l'utente ha esplicitamente
     * disattivato l'opzione "Mantieni dati alla disinstallazione" nelle
     * opzioni del componente (attiva/mantieni dati di default, per non
     * perdere la configurazione durante un aggiornamento/reinstallazione).
     */
    public function uninstall(InstallerAdapter $adapter): void
    {
        try {
            $params = ComponentHelper::getParams('com_messe');
        } catch (\Exception $e) {
            $params = null;
        }

        // Default = 1 (mantieni dati): se il parametro non è leggibile per
        // qualsiasi motivo, si sceglie comunque l'opzione più sicura.
        $mantieniDati = $params ? (int) $params->get('mantieni_dati_disinstallazione', 1) : 1;

        if ($mantieniDati) {
            Log::add(
                'com_messe: disinstallazione — dati mantenuti su richiesta (opzione "Mantieni dati alla disinstallazione" attiva)',
                Log::INFO,
                'com_messe'
            );
            return;
        }

        $db     = Factory::getDbo();
        $prefix = $db->getPrefix();

        $tabelle = [
            $prefix . 'messe_eccezioni',
            $prefix . 'messe_orari',
            $prefix . 'messe_periodi',
            $prefix . 'messe_chiese',
        ];

        foreach ($tabelle as $tabella) {
            try {
                $db->setQuery('DROP TABLE IF EXISTS ' . $db->quoteName($tabella));
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore rimozione tabella ' . $tabella . ': ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        if (!in_array($type, ['install', 'update'])) {
            return true;
        }

        $db     = Factory::getDbo();
        $prefix = $db->getPrefix();
        $tables = $db->getTableList();

        // 0. Crea le tabelle di base se mancano (fallback nel caso l'SQL
        //    di installazione del manifest non venga eseguito, ad es. quando
        //    Joomla tratta l'installazione come "update" perché il componente
        //    risultava già registrato da un tentativo precedente)
        $this->createBaseTablesIfMissing($db, $prefix, $tables);
        $tables = $db->getTableList();

        // 1. Aggiunge colonne veglia se mancanti
        $tableChiese = $prefix . 'messe_chiese';
        if (in_array($tableChiese, $tables)) {
            $this->addColumnIfMissing($db, $tableChiese, 'ora_veglia',
                'TINYINT(2) UNSIGNED NOT NULL DEFAULT 21 AFTER `indirizzo`'
            );
            $this->addColumnIfMissing($db, $tableChiese, 'minuti_veglia',
                'TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `ora_veglia`'
            );
            $this->addColumnIfMissing($db, $tableChiese, 'modalita_prefestiva',
                "ENUM('nessuna','vigiliare','dedicato','feriale_serale') NOT NULL DEFAULT 'feriale_serale' AFTER `minuti_veglia`"
            );
            $this->addColumnIfMissing($db, $tableChiese, 'sabato_solennita',
                "ENUM('vigiliare','festivo') NOT NULL DEFAULT 'festivo' AFTER `modalita_prefestiva`"
            );
        }

        // 1b. Estende l'ENUM tipo di messe_orari per includere 'prefestivo'
        //     (idempotente: rieseguirlo non ha effetti collaterali)
        $tableOrari = $prefix . 'messe_orari';
        if (in_array($tableOrari, $tables)) {
            try {
                $db->setQuery(
                    "ALTER TABLE " . $db->quoteName($tableOrari) .
                    " MODIFY `tipo` ENUM('feriale','vigilia','festivo','prefestivo') NOT NULL DEFAULT 'feriale'"
                );
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore estensione enum tipo orari: ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }

        // 2. Crea tabella periodi se non esiste
        $tablePeriodi = $prefix . 'messe_periodi';
        if (!in_array($tablePeriodi, $tables)) {
            try {
                $db->setQuery("CREATE TABLE IF NOT EXISTS `{$tablePeriodi}` (
                    `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `chiesa_id`   INT(11) UNSIGNED NOT NULL,
                    `nome`        VARCHAR(255)     NOT NULL DEFAULT '',
                    `tipo_data`   ENUM('date','mesi') NOT NULL DEFAULT 'date',
                    `data_inizio` CHAR(5)          DEFAULT NULL,
                    `data_fine`   CHAR(5)          DEFAULT NULL,
                    `mesi`        VARCHAR(30)      DEFAULT NULL,
                    `azione`      ENUM('sopprimi','sostituisci') NOT NULL DEFAULT 'sopprimi',
                    `tipo_orario` ENUM('feriale','vigilia','festivo','tutti') NOT NULL DEFAULT 'feriale',
                    `orari_nuovi` TEXT             DEFAULT NULL,
                    `published`   TINYINT(1)       NOT NULL DEFAULT 1,
                    `note`        VARCHAR(255)     DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_chiesa` (`chiesa_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore tabella periodi: ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }

        // 3. FIX CRITICO: allinea l'id del record assets con extension_id
        $this->fixAssetId($db);

        return true;
    }

    /**
     * Allinea l'id del record assets di com_messe con l'extension_id.
     * Joomla cerca il record assets con id = extension_id, quindi devono coincidere.
     */
    private function fixAssetId($db): void
    {
        try {
            // Legge extension_id di com_messe
            $query = $db->getQuery(true)
                ->select('extension_id')
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('com_messe'))
                ->where($db->quoteName('type')    . ' = ' . $db->quote('component'));
            $db->setQuery($query);
            $extensionId = (int) $db->loadResult();

            if (!$extensionId) {
                return;
            }

            // Legge il record assets attuale di com_messe
            $query = $db->getQuery(true)
                ->select(['id', 'rules'])
                ->from($db->quoteName('#__assets'))
                ->where($db->quoteName('name') . ' = ' . $db->quote('com_messe'));
            $db->setQuery($query);
            $asset = $db->loadObject();

            if (!$asset) {
                // Non esiste ancora, lo crea con l'id corretto
                $this->createAsset($db, $extensionId);
                return;
            }

            $currentAssetId = (int) $asset->id;

            if ($currentAssetId === $extensionId) {
                // ID già corretto — verifica solo il formato delle rules
                $this->fixRules($db, $asset);
                return;
            }

            // ID diverso — deve aggiornarlo
            // Prima verifica se extension_id è già usato da un altro record
            $query = $db->getQuery(true)
                ->select('id')
                ->from($db->quoteName('#__assets'))
                ->where($db->quoteName('id') . ' = ' . $extensionId);
            $db->setQuery($query);
            $conflitto = $db->loadResult();

            if ($conflitto) {
                // L'ID è occupato — sposta il record in conflitto a un ID libero
                $query = $db->getQuery(true)
                    ->select('MAX(id)')
                    ->from($db->quoteName('#__assets'));
                $db->setQuery($query);
                $maxId = (int) $db->loadResult();
                $newId = $maxId + 1;

                $db->setQuery(
                    'UPDATE ' . $db->quoteName('#__assets') .
                    ' SET ' . $db->quoteName('id') . ' = ' . $newId .
                    ' WHERE ' . $db->quoteName('id') . ' = ' . $extensionId
                );
                $db->execute();
            }

            // Aggiorna l'ID del record di com_messe
            $rules = json_decode($asset->rules ?? '{}', true);
            if (!is_array($rules) || !isset($rules['core.admin'])) {
                $rules = [
                    'core.admin'      => [],
                    'core.manage'     => [],
                    'core.create'     => [],
                    'core.delete'     => [],
                    'core.edit'       => [],
                    'core.edit.state' => [],
                ];
            }

            $db->setQuery(
                'UPDATE ' . $db->quoteName('#__assets') .
                ' SET ' . $db->quoteName('id')    . ' = ' . $extensionId .
                ', '    . $db->quoteName('rules') . ' = ' . $db->quote(json_encode($rules)) .
                ' WHERE ' . $db->quoteName('name') . ' = ' . $db->quote('com_messe')
            );
            $db->execute();

            // Aggiorna AUTO_INCREMENT per evitare conflitti futuri
            $query = $db->getQuery(true)
                ->select('MAX(id)')
                ->from($db->quoteName('#__assets'));
            $db->setQuery($query);
            $maxId = (int) $db->loadResult();

            $db->setQuery(
                'ALTER TABLE ' . $db->quoteName('#__assets') .
                ' AUTO_INCREMENT = ' . ($maxId + 1)
            );
            $db->execute();

        } catch (\Exception $e) {
            Log::add('com_messe: errore fixAssetId: ' . $e->getMessage(), Log::WARNING, 'com_messe');
        }
    }

    /**
     * Crea il record assets con l'ID corretto
     */
    private function createAsset($db, int $id): void
    {
        try {
            $rules = json_encode([
                'core.admin'      => [],
                'core.manage'     => [],
                'core.create'     => [],
                'core.delete'     => [],
                'core.edit'       => [],
                'core.edit.state' => [],
            ]);

            $db->setQuery(
                'INSERT INTO ' . $db->quoteName('#__assets') .
                ' (' . $db->quoteName('id') . ', ' .
                $db->quoteName('parent_id') . ', ' .
                $db->quoteName('lft') . ', ' .
                $db->quoteName('rgt') . ', ' .
                $db->quoteName('level') . ', ' .
                $db->quoteName('name') . ', ' .
                $db->quoteName('title') . ', ' .
                $db->quoteName('rules') . ')' .
                ' VALUES (' . $id . ', 1, 0, 0, 1, ' .
                $db->quote('com_messe') . ', ' .
                $db->quote('COM_MESSE') . ', ' .
                $db->quote($rules) . ')'
            );
            $db->execute();
        } catch (\Exception $e) {
            Log::add('com_messe: errore createAsset: ' . $e->getMessage(), Log::WARNING, 'com_messe');
        }
    }

    /**
     * Corregge il formato delle rules se sbagliato
     */
    private function fixRules($db, object $asset): void
    {
        try {
            $rules = json_decode($asset->rules ?? '{}', true);
            if (!is_array($rules) || !isset($rules['core.admin'])) {
                $rulesCorrette = json_encode([
                    'core.admin'      => [],
                    'core.manage'     => [],
                    'core.create'     => [],
                    'core.delete'     => [],
                    'core.edit'       => [],
                    'core.edit.state' => [],
                ]);
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__assets'))
                    ->set($db->quoteName('rules') . ' = ' . $db->quote($rulesCorrette))
                    ->where($db->quoteName('name') . ' = ' . $db->quote('com_messe'));
                $db->setQuery($query);
                $db->execute();
            }
        } catch (\Exception $e) {
            Log::add('com_messe: errore fixRules: ' . $e->getMessage(), Log::WARNING, 'com_messe');
        }
    }

    /**
     * Crea le tabelle di base (chiese, orari, eccezioni) se non esistono.
     * Funge da rete di sicurezza indipendente dall'SQL del manifest,
     * eseguendo la stessa logica idempotente già usata per messe_periodi.
     */
    private function createBaseTablesIfMissing($db, string $prefix, array $tables): void
    {
        $tableChiese     = $prefix . 'messe_chiese';
        $tableOrari      = $prefix . 'messe_orari';
        $tableEccezioni  = $prefix . 'messe_eccezioni';

        if (!in_array($tableChiese, $tables)) {
            try {
                $db->setQuery("CREATE TABLE IF NOT EXISTS `{$tableChiese}` (
                    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `nome`          VARCHAR(255)     NOT NULL DEFAULT '',
                    `descrizione`   TEXT,
                    `rito`          ENUM('romano','ambrosiano') NOT NULL DEFAULT 'romano',
                    `indirizzo`     VARCHAR(255)     DEFAULT NULL,
                    `ora_veglia`    TINYINT(2) UNSIGNED NOT NULL DEFAULT 21,
                    `minuti_veglia` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                    `modalita_prefestiva` ENUM('nessuna','vigiliare','dedicato','feriale_serale') NOT NULL DEFAULT 'feriale_serale',
                    `sabato_solennita` ENUM('vigiliare','festivo') NOT NULL DEFAULT 'festivo',
                    `published`     TINYINT(1)       NOT NULL DEFAULT 1,
                    `ordering`      INT(11)          NOT NULL DEFAULT 0,
                    `created`       DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `modified`      DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `created_by`    INT(11)          NOT NULL DEFAULT 0,
                    `modified_by`   INT(11)          NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore tabella chiese: ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }

        if (!in_array($tableOrari, $tables)) {
            try {
                $db->setQuery("CREATE TABLE IF NOT EXISTS `{$tableOrari}` (
                    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `chiesa_id`  INT(11) UNSIGNED NOT NULL,
                    `tipo`       ENUM('feriale','vigilia','festivo','prefestivo') NOT NULL DEFAULT 'feriale',
                    `ora`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                    `minuti`     TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                    `label`      VARCHAR(100) DEFAULT NULL,
                    `giorni`     VARCHAR(20)  DEFAULT NULL,
                    `ordering`   INT(11)      NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`),
                    KEY `idx_chiesa_tipo` (`chiesa_id`, `tipo`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore tabella orari: ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }

        if (!in_array($tableEccezioni, $tables)) {
            try {
                $db->setQuery("CREATE TABLE IF NOT EXISTS `{$tableEccezioni}` (
                    `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `chiesa_id`  INT(11) UNSIGNED NOT NULL,
                    `data_md`    CHAR(5)      NOT NULL DEFAULT '',
                    `ora`        TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                    `minuti`     TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                    `label`      VARCHAR(255) NOT NULL DEFAULT '',
                    `luogo`      VARCHAR(255) DEFAULT NULL,
                    `published`  TINYINT(1)   NOT NULL DEFAULT 1,
                    PRIMARY KEY (`id`),
                    KEY `idx_chiesa_data` (`chiesa_id`, `data_md`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                $db->execute();
            } catch (\Exception $e) {
                Log::add('com_messe: errore tabella eccezioni: ' . $e->getMessage(), Log::WARNING, 'com_messe');
            }
        }
    }

    private function addColumnIfMissing($db, string $table, string $column, string $definition): void
    {
        try {
            $columns = $db->getTableColumns($table);
            if (!array_key_exists($column, $columns)) {
                $db->setQuery(
                    'ALTER TABLE ' . $db->quoteName($table) .
                    ' ADD COLUMN ' . $db->quoteName($column) . ' ' . $definition
                );
                $db->execute();
            }
        } catch (\Exception $e) {
            Log::add('com_messe: errore colonna ' . $column . ': ' . $e->getMessage(), Log::WARNING, 'com_messe');
        }
    }
}

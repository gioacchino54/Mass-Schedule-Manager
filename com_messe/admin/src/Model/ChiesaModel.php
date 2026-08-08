<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Model backend — Chiesa
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

class ChiesaModel extends BaseDatabaseModel
{
    protected $name = 'Chiesa';

    public function getItem(int $pk = 0): object
    {
        $app = Factory::getApplication();
        $pk  = $pk ?: (int) $app->getInput()->getInt('id', 0);
        $db  = $this->getDatabase();

        if ($pk > 0) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__messe_chiese'))
                ->where($db->quoteName('id') . ' = ' . $pk);
            $db->setQuery($query);
            $item = $db->loadObject();
        }

        if (empty($item)) {
            $item = (object) [
                'id'          => 0,
                'nome'        => '',
                'rito'        => 'romano',
                'indirizzo'   => '',
                'descrizione' => '',
                'published'   => 1,
                'modalita_prefestiva' => 'feriale_serale',
                'sabato_solennita' => 'festivo',
            ];
        }

        // Carica orari
        if ($pk > 0) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__messe_orari'))
                ->where($db->quoteName('chiesa_id') . ' = ' . $pk)
                ->order('tipo ASC, ordering ASC');
            $db->setQuery($query);
            $item->orari = $db->loadObjectList();

            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__messe_eccezioni'))
                ->where($db->quoteName('chiesa_id') . ' = ' . $pk)
                ->order('data_md ASC');
            $db->setQuery($query);
            $item->eccezioni = $db->loadObjectList();
        } else {
            $item->orari     = [];
            $item->eccezioni = [];
            $item->periodi   = [];
        }

        if ($pk > 0) {
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__messe_periodi'))
                ->where($db->quoteName('chiesa_id') . ' = ' . $pk)
                ->order('id ASC');
            $db->setQuery($query);
            $item->periodi = $db->loadObjectList();
        }

        return $item;
    }

    public function save(array &$data): bool
    {
        $db  = $this->getDatabase();
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $db->transactionStart();

        try {
            $id = (int) ($data['id'] ?? 0);

            $row = (object) [
                'nome'          => trim($data['nome'] ?? ''),
                'rito'          => in_array($data['rito'] ?? '', ['romano', 'ambrosiano'])
                                      ? $data['rito'] : 'romano',
                'indirizzo'     => trim($data['indirizzo'] ?? ''),
                'descrizione'   => trim($data['descrizione'] ?? ''),
                'published'     => (int) ($data['published'] ?? 1),
                'ora_veglia'    => min(23, max(0, (int) ($data['ora_veglia']    ?? 21))),
                'minuti_veglia' => min(59, max(0, (int) ($data['minuti_veglia'] ?? 0))),
                'modalita_prefestiva' => in_array($data['modalita_prefestiva'] ?? '', ['nessuna', 'vigiliare', 'dedicato', 'feriale_serale'])
                                            ? $data['modalita_prefestiva'] : 'feriale_serale',
                'sabato_solennita' => in_array($data['sabato_solennita'] ?? '', ['vigiliare', 'festivo'])
                                            ? $data['sabato_solennita'] : 'festivo',
                'modified'      => $now,
                'modified_by'   => 0,
            ];

            if ($id > 0) {
                $row->id = $id;
                $db->updateObject('#__messe_chiese', $row, 'id');
            } else {
                $row->created    = $now;
                $row->created_by = 0;
                $row->ordering   = 0;
                $db->insertObject('#__messe_chiese', $row, 'id');
                $id = (int) $db->insertid();
            }

            $this->setState('Chiesa.id', $id); // Chiave fissa per compatibilità controller
            $data['id'] = $id; // Fonte primaria e affidabile per il controller (evita l'overwrite di populateState() su getState())

            // --- Salva orari ---
            $db->setQuery(
                $db->getQuery(true)
                    ->delete($db->quoteName('#__messe_orari'))
                    ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            )->execute();

            foreach (['feriale', 'vigilia', 'festivo', 'prefestivo'] as $tipo) {
                if (empty($data['orari'][$tipo])) {
                    continue;
                }
                foreach ($data['orari'][$tipo] as $ord => $o) {
                    if (!isset($o['ora'])) {
                        continue;
                    }
                    $r = (object) [
                        'chiesa_id' => $id,
                        'tipo'      => $tipo,
                        'ora'       => (int) $o['ora'],
                        'minuti'    => (int) ($o['minuti'] ?? 0),
                        'label'     => !empty($o['label']) ? trim($o['label']) : null,
                        'giorni'    => !empty($o['giorni']) ? trim($o['giorni']) : null,
                        'ordering'  => (int) $ord,
                    ];
                    $db->insertObject('#__messe_orari', $r);
                }
            }

            // --- Salva eccezioni ---
            $db->setQuery(
                $db->getQuery(true)
                    ->delete($db->quoteName('#__messe_eccezioni'))
                    ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            )->execute();

            if (!empty($data['eccezioni'])) {
                foreach ($data['eccezioni'] as $e) {
                    if (empty(trim($e['data_md'] ?? ''))) {
                        continue;
                    }
                    $r = (object) [
                        'chiesa_id' => $id,
                        'data_md'   => substr(trim($e['data_md']), 0, 5),
                        'ora'       => (int) ($e['ora'] ?? 0),
                        'minuti'    => (int) ($e['minuti'] ?? 0),
                        'label'     => trim($e['label'] ?? ''),
                        'luogo'     => !empty($e['luogo']) ? trim($e['luogo']) : null,
                        'published' => 1,
                    ];
                    $db->insertObject('#__messe_eccezioni', $r);
                }
            }

            // --- Salva periodi stagionali ---
            $db->setQuery(
                $db->getQuery(true)
                    ->delete($db->quoteName('#__messe_periodi'))
                    ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            )->execute();

            if (!empty($data['periodi'])) {
                foreach ($data['periodi'] as $p) {
                    if (empty(trim($p['nome'] ?? ''))) continue;

                    // Gestione mesi: array di checkbox -> JSON
                    $mesi = null;
                    if (($p['tipo_data'] ?? '') === 'mesi' && !empty($p['mesi'])) {
                        $mesi = json_encode(array_map('intval', (array) $p['mesi']));
                    }

                    // Gestione orari sostitutivi -> JSON
                    $orariNuovi = null;
                    if (($p['azione'] ?? '') === 'sostituisci' && !empty($p['orari_nuovi'])) {
                        $oa = [];
                        foreach ($p['orari_nuovi'] as $o) {
                            if (!isset($o['h'])) continue;
                            $oa[] = [
                                'h'      => (int) $o['h'],
                                'm'      => (int) ($o['m'] ?? 0),
                                'label'  => trim($o['label'] ?? ''),
                                'giorni' => !empty($o['giorni']) ? array_map('intval', (array)$o['giorni']) : null,
                            ];
                        }
                        $orariNuovi = !empty($oa) ? json_encode($oa) : null;
                    }

                    $r = (object) [
                        'chiesa_id'   => $id,
                        'nome'        => trim($p['nome']),
                        'tipo_data'   => in_array($p['tipo_data'] ?? '', ['date','mesi']) ? $p['tipo_data'] : 'mesi',
                        'data_inizio' => ($p['tipo_data'] ?? '') === 'date' ? substr(trim($p['data_inizio'] ?? ''), 0, 5) : null,
                        'data_fine'   => ($p['tipo_data'] ?? '') === 'date' ? substr(trim($p['data_fine'] ?? ''), 0, 5) : null,
                        'mesi'        => $mesi,
                        'azione'      => in_array($p['azione'] ?? '', ['sopprimi','sostituisci']) ? $p['azione'] : 'sopprimi',
                        'tipo_orario' => in_array($p['tipo_orario'] ?? '', ['feriale','vigilia','festivo','tutti']) ? $p['tipo_orario'] : 'feriale',
                        'orari_nuovi' => $orariNuovi,
                        'published'   => 1,
                        'note'        => trim($p['note'] ?? ''),
                    ];
                    $db->insertObject('#__messe_periodi', $r);
                }
            }

            $db->transactionCommit();
            return true;

        } catch (\Exception $e) {
            $db->transactionRollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    protected function populateState()
    {
        $id = (int) Factory::getApplication()->getInput()->getInt('id', 0);
        $this->setState($this->getName() . '.id', $id);
    }
}

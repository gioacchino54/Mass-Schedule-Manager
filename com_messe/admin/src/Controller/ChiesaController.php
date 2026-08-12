<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Controller backend — Chiesa
 *
 * SICUREZZA:
 * - Verifica token CSRF su ogni operazione di scrittura
 * - Verifica autorizzazioni utente prima di ogni azione
 * - Input sanitizzato tramite getInput() con tipo esplicito
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

class ChiesaController extends BaseController
{
    /**
     * Verifica token CSRF e autorizzazione utente
     */
    private function checkSecurity(string $action = 'core.edit'): void
    {
        // Verifica token CSRF — blocca Cross-Site Request Forgery
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Verifica che l'utente sia autenticato e abbia i permessi
        $user = Factory::getApplication()->getIdentity();

        if ($user->guest || !$user->authorise($action, 'com_messe')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }

    /**
     * Sanitizza e valida i dati in ingresso
     */
    private function sanitizeData(array $data): array
    {
        return [
            'id'          => (int) ($data['id'] ?? 0),
            'nome'        => strip_tags(trim($data['nome'] ?? '')),
            'rito'        => in_array($data['rito'] ?? '', ['romano', 'ambrosiano'])
                                ? $data['rito'] : 'romano',
            'indirizzo'   => strip_tags(trim($data['indirizzo'] ?? '')),
            'descrizione' => strip_tags(trim($data['descrizione'] ?? '')),
            'published'   => (int) (bool) ($data['published'] ?? 1),
            'ora_veglia'  => min(23, max(0, (int) ($data['ora_veglia'] ?? 21))),
            'minuti_veglia' => min(59, max(0, (int) ($data['minuti_veglia'] ?? 0))),
            'modalita_prefestiva' => in_array($data['modalita_prefestiva'] ?? '', ['nessuna', 'vigiliare', 'dedicato', 'feriale_serale'])
                                ? $data['modalita_prefestiva'] : 'feriale_serale',
            'sabato_solennita' => in_array($data['sabato_solennita'] ?? '', ['vigiliare', 'festivo'])
                                ? $data['sabato_solennita'] : 'festivo',
            'orari'       => $this->sanitizeOrari($data['orari'] ?? []),
            'eccezioni'   => $this->sanitizeEccezioni($data['eccezioni'] ?? []),
            'periodi'     => $this->sanitizePeriodi($data['periodi'] ?? []),
            'settimana_santa' => $this->sanitizeSettimanaSanta($data['settimana_santa'] ?? []),
        ];
    }

    private function sanitizeOrari(array $orari): array
    {
        $result = [];
        $tipiValidi = ['feriale', 'vigilia', 'festivo', 'prefestivo'];

        foreach ($tipiValidi as $tipo) {
            if (empty($orari[$tipo])) continue;
            foreach ($orari[$tipo] as $idx => $o) {
                $result[$tipo][(int) $idx] = [
                    'ora'    => min(23, max(0, (int) ($o['ora'] ?? 0))),
                    'minuti' => min(59, max(0, (int) ($o['minuti'] ?? 0))),
                    'label'  => strip_tags(trim($o['label'] ?? '')),
                    'giorni' => $o['giorni'] ?? '',
                ];
            }
        }

        return $result;
    }

    private function sanitizeEccezioni(array $eccezioni): array
    {
        $result = [];

        foreach ($eccezioni as $idx => $e) {
            $dataMd = trim($e['data_md'] ?? '');

            // Valida formato MM-GG
            if (!preg_match('/^\d{2}-\d{2}$/', $dataMd)) continue;

            $result[(int) $idx] = [
                'data_md' => $dataMd,
                'ora'     => min(23, max(0, (int) ($e['ora'] ?? 0))),
                'minuti'  => min(59, max(0, (int) ($e['minuti'] ?? 0))),
                'label'   => strip_tags(trim($e['label'] ?? '')),
                'luogo'   => strip_tags(trim($e['luogo'] ?? '')),
                'modalita' => in_array($e['modalita'] ?? '', ['sostituisci', 'aggiungi'])
                                ? $e['modalita'] : 'sostituisci',
            ];
        }

        return $result;
    }

    private function sanitizeSettimanaSanta(array $righe): array
    {
        $result = [];
        $giorniValidi = ['palme','lunedi_santo','martedi_santo','mercoledi_santo','giovedi_santo','venerdi_santo','sabato_santo'];

        foreach ($righe as $idx => $s) {
            $label = strip_tags(trim($s['label'] ?? ''));
            if (empty($label)) continue;

            $result[(int) $idx] = [
                'giorno_riferimento' => in_array($s['giorno_riferimento'] ?? '', $giorniValidi)
                                            ? $s['giorno_riferimento'] : 'palme',
                'ora'      => min(23, max(0, (int) ($s['ora'] ?? 18))),
                'minuti'   => min(59, max(0, (int) ($s['minuti'] ?? 0))),
                'label'    => $label,
                'luogo'    => strip_tags(trim($s['luogo'] ?? '')),
                'modalita' => in_array($s['modalita'] ?? '', ['sostituisci', 'aggiungi'])
                                ? $s['modalita'] : 'aggiungi',
            ];
        }

        return $result;
    }

    private function sanitizePeriodi(array $periodi): array
    {
        $result = [];

        foreach ($periodi as $idx => $p) {
            $nome = strip_tags(trim($p['nome'] ?? ''));
            if (empty($nome)) continue;

            $tipoData   = in_array($p['tipo_data'] ?? '', ['date', 'mesi']) ? $p['tipo_data'] : 'mesi';
            $tipoOrario = in_array($p['tipo_orario'] ?? '', ['feriale','vigilia','festivo','tutti'])
                            ? $p['tipo_orario'] : 'feriale';
            $azione     = in_array($p['azione'] ?? '', ['sopprimi','sostituisci'])
                            ? $p['azione'] : 'sopprimi';

            // Valida date nel formato MM-GG
            $dataInizio = trim($p['data_inizio'] ?? '');
            $dataFine   = trim($p['data_fine'] ?? '');
            if ($tipoData === 'date') {
                if (!preg_match('/^\d{2}-\d{2}$/', $dataInizio)) $dataInizio = '';
                if (!preg_match('/^\d{2}-\d{2}$/', $dataFine))   $dataFine   = '';
            }

            // Valida mesi (array di interi 1-12)
            $mesi = [];
            if (!empty($p['mesi']) && is_array($p['mesi'])) {
                foreach ($p['mesi'] as $m) {
                    $m = (int) $m;
                    if ($m >= 1 && $m <= 12) $mesi[] = $m;
                }
            }

            $result[(int) $idx] = [
                'nome'        => $nome,
                'tipo_data'   => $tipoData,
                'tipo_orario' => $tipoOrario,
                'azione'      => $azione,
                'data_inizio' => $dataInizio,
                'data_fine'   => $dataFine,
                'mesi'        => $mesi,
                'orari_nuovi' => $p['orari_nuovi'] ?? [],
            ];
        }

        return $result;
    }

    /**
     * Salva e rimane nel form in modalità modifica (pulsante Salva)
     */
    public function apply()
    {
        $this->checkSecurity('core.edit');

        $app  = Factory::getApplication();
        $raw  = $app->getInput()->get('jform', [], 'array');
        $data = $this->sanitizeData($raw);

        $model = $this->getModel('Chiesa', 'Administrator');

        if (!$model->save($data)) {
            $app->enqueueMessage($model->getError(), 'error');
            $app->redirect(
                Route::_(
                    'index.php?option=com_messe&view=chiesa&layout=edit&id=' . $data['id'],
                    false
                )
            );
            return false;
        }

        $id = (int) $data['id'];

        $app->enqueueMessage('Chiesa salvata correttamente.', 'message');
        $app->redirect(
            Route::_(
                'index.php?option=com_messe&view=chiesa&layout=edit&id=' . $id,
                false
            )
        );

        return true;
    }

    /**
     * Salva e torna alla lista (pulsante Salva e Chiudi)
     */
    public function save()
    {
        $this->checkSecurity('core.edit');

        $app  = Factory::getApplication();
        $raw  = $app->getInput()->get('jform', [], 'array');
        $data = $this->sanitizeData($raw);

        $model = $this->getModel('Chiesa', 'Administrator');

        if (!$model->save($data)) {
            $app->enqueueMessage($model->getError(), 'error');
            $app->redirect(
                Route::_(
                    'index.php?option=com_messe&view=chiesa&layout=edit&id=' . $data['id'],
                    false
                )
            );
            return false;
        }

        $app->enqueueMessage('Chiesa salvata correttamente.', 'message');
        $app->redirect(
            Route::_('index.php?option=com_messe&view=chiese', false)
        );

        return true;
    }

    /**
     * Salva e apre un form vuoto per inserire una nuova chiesa
     * (pulsante Salva e Nuovo)
     */
    public function save2new()
    {
        $this->checkSecurity('core.edit');

        $app  = Factory::getApplication();
        $raw  = $app->getInput()->get('jform', [], 'array');
        $data = $this->sanitizeData($raw);

        $model = $this->getModel('Chiesa', 'Administrator');

        if (!$model->save($data)) {
            $app->enqueueMessage($model->getError(), 'error');
            $app->redirect(
                Route::_(
                    'index.php?option=com_messe&view=chiesa&layout=edit&id=' . $data['id'],
                    false
                )
            );
            return false;
        }

        $app->enqueueMessage('Chiesa salvata correttamente.', 'message');
        $app->redirect(
            Route::_('index.php?option=com_messe&view=chiesa&layout=edit&id=0', false)
        );

        return true;
    }

    /**
     * Nuova chiesa
     */
    public function add()
    {
        $this->checkSecurity('core.create');

        Factory::getApplication()->redirect(
            Route::_('index.php?option=com_messe&view=chiesa&layout=edit&id=0', false)
        );
    }

    /**
     * Annulla e torna alla lista
     */
    public function cancel()
    {
        // Verifica token anche per cancel per prevenire CSRF redirect
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        Factory::getApplication()->redirect(
            Route::_('index.php?option=com_messe&view=chiese', false)
        );
    }
}

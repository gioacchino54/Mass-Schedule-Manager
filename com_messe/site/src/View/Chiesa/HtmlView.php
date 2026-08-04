<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * View frontend — Chiesa — v1.2.5
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\View\Chiesa;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use GioachinoCipriano\Component\Messe\Site\Helper\MesseHelper;

class HtmlView extends BaseHtmlView
{
    public $chiesa        = null;
    public $chiese        = [];
    public $orari         = [];
    public $tutteLeChiese = [];
    public $params;
    public $testMode      = false;
    public $testDate      = null;
    public $now           = 0;

    public function display($tpl = null)
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();

        $lang = $app->getLanguage();
        $lang->load('com_messe', JPATH_SITE);

        $this->params = $app->getParams('com_messe');

        $this->testMode = (bool) $this->params->get('test_mode', 0);
        $this->testDate = $this->params->get('test_date', '');
        $this->now      = ($this->testMode && !empty($this->testDate))
            ? (int) strtotime($this->testDate)
            : time();

        /** @var \GioachinoCipriano\Component\Messe\Site\Model\ChiesaModel $model */
        $model = $this->getModel();

        $this->chiese = $model->getChiese();

        $windowParams = [
            'days_window'     => (int) $this->params->get('days_window', 7),
            'days_prefestivo' => (int) $this->params->get('days_prefestivo', 5),
            'days_speciali'   => (int) $this->params->get('days_speciali', 15),
            'usa_soglia_oraria_prefestiva' => (int) $this->params->get('usa_soglia_oraria_prefestiva', 1),
            'ora_prefestivo'     => (int) $this->params->get('ora_prefestivo', 16),
            'ora_prefestivo_max' => (int) $this->params->get('ora_prefestivo_max', 20),
            'mostra_giorno_prefestivo' => (int) $this->params->get('mostra_giorno_prefestivo', 0),
            'periodi_influenzano_prefestiva' => (int) $this->params->get('periodi_influenzano_prefestiva', 1),
        ];

        // Legge id da tutte le possibili sorgenti in ordine di priorità:
        // 1. GET/POST diretto (?id=X)
        // 2. Parametro menu item (chiesa_id)
        // 3. Nessuno = tutte le chiese
        $id = $input->getInt('id', 0);

        if (!$id) {
            $id = (int) $this->params->get('chiesa_id', 0);
        }

        if ($id) {
            $this->chiesa = $model->getChiesa($id);
            if ($this->chiesa) {
                $this->orari = MesseHelper::calcolaOrari(
                    $this->chiesa,
                    $this->now,
                    $windowParams
                );
            }
        } else {
            // Mostra tutte le chiese
            foreach ($this->chiese as $c) {
                $chiesaCompleta = $model->getChiesa((int) $c->id);
                if ($chiesaCompleta) {
                    $this->tutteLeChiese[] = [
                        'chiesa' => $chiesaCompleta,
                        'orari'  => MesseHelper::calcolaOrari(
                            $chiesaCompleta,
                            $this->now,
                            $windowParams
                        ),
                    ];
                }
            }
        }

        parent::display($tpl);
    }
}

<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Modulo — mod_messe — v1.0.7
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use GioachinoCipriano\Module\Messe\Site\Helper\ModMesseHelper;

$chiesa_id = (int) $params->get('chiesa_id', 0);

if (!$chiesa_id) {
    return;
}

$chiesa = ModMesseHelper::getChiesa($chiesa_id);

if (!$chiesa) {
    return;
}

// Test mode
$testMode = (bool) $params->get('test_mode', 0);
$testDate = $params->get('test_date', '');
$now      = ($testMode && !empty($testDate))
    ? (int) strtotime($testDate)
    : time();

$windowParams = [
    'days_window'     => (int) $params->get('days_window', 7),
    'days_prefestivo' => (int) $params->get('days_prefestivo', 5),
    'days_speciali'   => (int) $params->get('days_speciali', 15),
    'usa_soglia_oraria_prefestiva' => (int) $params->get('usa_soglia_oraria_prefestiva', 1),
    'ora_prefestivo'     => (int) $params->get('ora_prefestivo', 16),
    'ora_prefestivo_max' => (int) $params->get('ora_prefestivo_max', 20),
    'mostra_giorno_prefestivo' => (int) $params->get('mostra_giorno_prefestivo', 0),
    'periodi_influenzano_prefestiva' => (int) $params->get('periodi_influenzano_prefestiva', 1),
];

$orari        = ModMesseHelper::calcolaOrari($chiesa, $now, $windowParams);
$showNextMass = (int) $params->get('show_next_mass', 1);
$showOrari    = (int) $params->get('show_orari', 1);

require ModuleHelper::getLayoutPath('mod_messe', $params->get('layout', 'default'));

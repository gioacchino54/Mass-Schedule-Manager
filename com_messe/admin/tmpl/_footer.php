<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Partial — Footer copyright/licenza per le view backend
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$messeAnnoInizio = 2026;
$messeAnnoCorrente = (int) date('Y');
$messeAnnoRange = ($messeAnnoCorrente > $messeAnnoInizio)
    ? $messeAnnoInizio . '-' . $messeAnnoCorrente
    : (string) $messeAnnoInizio;
?>
<div class="messe-footer-copyright text-muted small mt-4 pt-3 border-top">
    <p class="mb-1">
        Copyright <?= $messeAnnoRange ?>
        <a href="https://gioacchinocipriano.it/" target="_blank" rel="noopener noreferrer">Gioacchino Cipriano</a>.
        Tutti i diritti riservati.
    </p>
    <p class="mb-0">
        Gestione Orari Messe è Software Libero e viene distribuito nei termini della
        <a href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License</a>,
        versione 2 o – a tua scelta – successiva.
    </p>
</div>

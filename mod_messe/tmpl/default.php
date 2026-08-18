<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Template modulo — default — v1.0.7
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use GioachinoCipriano\Module\Messe\Site\Helper\ModMesseHelper;

$giorni   = $orari['giorni'] ?? [];
$gruppi   = ModMesseHelper::getGruppi();
$testMode = (bool) $params->get('test_mode', 0);
$testDate = $params->get('test_date', '');
$now      = ($testMode && !empty($testDate)) ? (int) strtotime($testDate) : time();
?>

<div class="mod-messe">

    <?php if ($testMode) : ?>
    <div class="alert alert-warning small py-1 px-2 mb-2">
        <?= Text::_('MOD_MESSE_TESTMODE_AVVISO') ?>
        <strong><?= date('d/m/Y H:i', $now) ?></strong>
    </div>
    <?php endif; ?>

    <?php if (!empty($chiesa->nome)) : ?>
    <!-- ✅ Nome chiesa in h2 -->
    <h2 class="mod-messe-chiesa-nome h5 fw-bold mb-1">
        ⛪ <?= htmlspecialchars($chiesa->nome) ?>
    </h2>
    <?php endif; ?>

    <?php if (!empty($chiesa->rito)) : ?>
    <span class="mod-messe-badge-rito mb-2 d-inline-block" style="
            padding:2px 10px;
            border-radius:20px;
            font-size:0.75rem;
            font-weight:600;
            border:1px solid <?= $chiesa->rito === 'ambrosiano' ? '#0dcaf0' : '#6c757d' ?>;
            color:<?= $chiesa->rito === 'ambrosiano' ? '#06748b' : '#495057' ?>;
            background:<?= $chiesa->rito === 'ambrosiano' ? 'rgba(13,202,240,0.1)' : 'rgba(108,117,125,0.1)' ?>;
        ">
        <?= Text::_($chiesa->rito === 'ambrosiano' ? 'COM_MESSE_RITO_AMBROSIANO' : 'COM_MESSE_RITO_ROMANO') ?>
    </span>
    <div class="mb-2"></div>
    <?php endif; ?>

    <?php if ($showNextMass && !empty($orari['prossima'])) :
        $p = $orari['prossima']; ?>
    <div class="mod-messe-prossima mb-3 p-2 border border-primary rounded">
        <div class="small text-muted mb-1">
            <?= Text::_('MOD_MESSE_PROSSIMA') ?>
        </div>
        <div class="fw-bold">
            <?= $giorni[(int) date('w', $p['ts'])] ?>
            <?= date('d/m', $p['ts']) ?>
            <?= Text::_('MOD_MESSE_ORE') ?>
            <?= date('H:i', $p['ts']) ?>
        </div>
        <?php if (!empty($p['label'])) : ?>
            <div class="small fst-italic"><?= htmlspecialchars($p['label']) ?></div>
        <?php endif; ?>
        <?php if (!empty($p['luogo'])) : ?>
            <div class="small text-muted">📍 <?= htmlspecialchars($p['luogo']) ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($showOrari) : ?>
    <div class="mod-messe-orari">

        <?php foreach ($gruppi as $tipoGruppo => $titoloGruppo) :

            $messeGruppo = array_filter(
                $orari['elenco'] ?? [],
                fn($m) => $m['tipo'] === $tipoGruppo
            );

            if (empty($messeGruppo)) continue;
        ?>
        <div class="mod-messe-gruppo mb-2">
            <div class="mod-messe-gruppo-titolo small fw-bold text-muted mb-1">
                <?= $titoloGruppo ?>
            </div>

            <?php if ($tipoGruppo === 'particolare') :
                foreach ($messeGruppo as $m) : ?>
                <div class="mod-messa d-flex gap-2 small py-1 border-bottom">
                    <span class="text-muted" style="min-width:80px">
                        <?= $m['giorno_label'] ?> <?= date('d/m', $m['ts']) ?>
                    </span>
                    <span class="fw-bold"><?= date('H:i', $m['ts']) ?></span>
                    <?php if (!empty($m['nome'])) : ?>
                        <span class="fst-italic"><?= htmlspecialchars($m['nome']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($m['luogo'])) : ?>
                        <span class="text-muted">📍 <?= htmlspecialchars($m['luogo']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach;

            else :
                $orariUnici = [];
                foreach ($messeGruppo as $m) {
                    $giorniKey = !empty($m['giorni']) ? implode(',', $m['giorni']) : '';
                    $key = date('H:i', $m['ts']) . '|' . ($m['label'] ?? '') . '|' . $giorniKey;
                    if (!isset($orariUnici[$key])) $orariUnici[$key] = $m;
                }
                ksort($orariUnici);
                foreach ($orariUnici as $m) :
                    $giorniLabel = '';
                    if (!empty($m['giorni'])) {
                        $nomiGiorni  = array_map(fn($g) => $giorni[$g] ?? '', $m['giorni']);
                        $giorniLabel = implode(', ', array_filter($nomiGiorni));
                    }
                ?>
                <div class="mod-messa d-flex gap-2 small py-1 border-bottom">
                    <span class="fw-bold" style="min-width:40px"><?= date('H:i', $m['ts']) ?></span>
                    <?php if ($giorniLabel !== '') : ?>
                        <span class="text-muted"><?= htmlspecialchars($giorniLabel) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($m['label'])) : ?>
                        <span class="text-muted">— <?= htmlspecialchars($m['label']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($m['nome'])) : ?>
                        <span class="text-muted fst-italic">· <?= htmlspecialchars($m['nome']) ?></span>
                    <?php endif; ?>
                </div>
                <?php endforeach;
            endif; ?>

        </div>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

</div>

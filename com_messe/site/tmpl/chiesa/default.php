<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Template frontend — v1.2.4
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use GioachinoCipriano\Component\Messe\Site\Helper\MesseHelper;

$chiesa        = $this->chiesa;
$chiese        = $this->chiese;
$tutteLeChiese = $this->tutteLeChiese ?? [];
$orari         = $this->orari;
$testMode      = $this->testMode ?? false;
$now           = $this->now ?? time();
$gruppi        = MesseHelper::getGruppi();
// Usa sempre l'URL del menu item corrente per il selettore
$menu    = \Joomla\CMS\Factory::getApplication()->getMenu();
$active  = $menu->getActive();
$urlBase = $active
    ? Route::_('index.php?Itemid=' . $active->id)
    : Route::_('index.php?option=com_messe&view=chiesa');

/**
 * Funzione per renderizzare una singola chiesa con i suoi orari
 */
function renderOrariChiesa(object $c, array $orari, array $gruppi): void
{
    $giorni = $orari['giorni'] ?? [];
    ?>

    <!-- Intestazione chiesa - stile card chiaro -->
    <div class="messe-intestazione card mb-3" style="background:#eef4fb;border:1px solid #b8d0eb;">
        <div class="card-body py-2 px-3">
            <h2 class="messe-chiesa-nome mb-1" style="font-size:1.2rem;color:#1a3a5c;">
                ⛪ <?= htmlspecialchars($c->nome) ?>
            </h2>
            <?php if (!empty($c->indirizzo)) : ?>
                <p class="text-muted mb-1 small">📍 <?= htmlspecialchars($c->indirizzo) ?></p>
            <?php endif; ?>
            <span style="
                    display:inline-block;
                    padding:2px 10px;
                    border-radius:20px;
                    font-size:0.8rem;
                    font-weight:600;
                    border:1px solid <?= in_array($c->rito, ['romano','ambrosiano']) ? ($c->rito === 'ambrosiano' ? '#0dcaf0' : '#6c757d') : '#6c757d' ?>;
                    color:<?= $c->rito === 'ambrosiano' ? '#06748b' : '#495057' ?>;
                    background:<?= $c->rito === 'ambrosiano' ? 'rgba(13,202,240,0.1)' : 'rgba(108,117,125,0.1)' ?>;
                ">
                    <?= \Joomla\CMS\Language\Text::_($c->rito === 'ambrosiano' ? 'COM_MESSE_RITO_AMBROSIANO' : 'COM_MESSE_RITO_ROMANO') ?>
                </span>
        </div>
    </div>

    <!-- Prossima messa - stile card chiaro -->
    <?php if (!empty($orari['prossima'])) :
        $p = $orari['prossima']; ?>
    <div class="messe-prossima card mb-4" style="background:#eef4fb;border:1px solid #b8d0eb;">
        <div class="card-body py-2 px-3">
            <div class="small text-muted mb-1">
                🕐 <?= \Joomla\CMS\Language\Text::_('COM_MESSE_PROSSIMA_MESSA') ?>
            </div>
            <p class="fw-bold mb-1" style="color:#1a3a5c;">
                <?= $giorni[(int) date('w', $p['ts'])] ?>
                <?= date('d/m', $p['ts']) ?>
                <?= \Joomla\CMS\Language\Text::_('COM_MESSE_ALLE') ?>
                <?= date('H:i', $p['ts']) ?>
                <?php if (!empty($p['label'])) : ?>
                    &mdash; <em><?= htmlspecialchars($p['label']) ?></em>
                <?php endif; ?>
            </p>
            <?php if (!empty($p['luogo'])) : ?>
                <p class="mb-0"><small class="text-muted">📍 <?= htmlspecialchars($p['luogo']) ?></small></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Orari per categoria -->
    <div class="messe-orari">
        <h3 class="mb-3" style="font-size:1.1rem;"><?= \Joomla\CMS\Language\Text::_('COM_MESSE_FRONTEND_TITLE') ?></h3>

        <?php foreach ($gruppi as $tipoGruppo => $titoloGruppo) :

            $messeGruppo = array_filter(
                $orari['elenco'] ?? [],
                fn($m) => $m['tipo'] === $tipoGruppo
            );

            if (empty($messeGruppo)) continue;
        ?>
        <div class="messe-gruppo mb-4">
            <h4 class="border-bottom pb-1 mb-2" style="font-size:1rem;"><?= htmlspecialchars($titoloGruppo) ?></h4>
            <div class="messe-lista">

                <?php if ($tipoGruppo === 'particolare') :
                    foreach ($messeGruppo as $m) : ?>
                    <div class="messa d-flex flex-wrap gap-2 py-2 border-bottom align-items-baseline">
                        <span class="text-muted small" style="min-width:90px">
                            <strong><?= htmlspecialchars($m['giorno_label']) ?></strong>
                            <?= date('d/m', $m['ts']) ?>
                        </span>
                        <span class="fw-bold"><?= date('H:i', $m['ts']) ?></span>
                        <?php if (!empty($m['nome'])) : ?>
                            <span class="fst-italic"><?= htmlspecialchars($m['nome']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($m['luogo'])) : ?>
                            <span class="text-muted small">📍 <?= htmlspecialchars($m['luogo']) ?></span>
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
                    <div class="messa d-flex gap-3 py-2 border-bottom align-items-baseline">
                        <span class="fw-bold" style="min-width:50px;"><?= date('H:i', $m['ts']) ?></span>
                        <?php if ($giorniLabel !== '') : ?>
                            <span class="text-muted small"><?= htmlspecialchars($giorniLabel) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($m['label'])) : ?>
                            <span class="text-muted">— <?= htmlspecialchars($m['label']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($m['nome'])) : ?>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars($m['nome']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach;
                endif; ?>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php
}
?>

<div class="com-messe">

    <?php if ($testMode) : ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
        <strong><?= Text::_('COM_MESSE_TESTMODE_AVVISO') ?></strong>
        <?= date('d/m/Y H:i', $now) ?>
    </div>
    <?php endif; ?>

    <!-- Selettore chiesa -->
    <?php if (count($chiese) > 1) : ?>
    <div class="messe-selettore mb-4">
        <form method="get" action="<?= $urlBase ?>">
            <div class="input-group">
                <label class="input-group-text" for="messe-chiesa-select">
                    <?= Text::_('COM_MESSE_SELETTORE_CHIESA') ?>
                </label>
                <select name="id" id="messe-chiesa-select" class="form-select"
                        onchange="this.form.submit()">
                    <option value="0" <?= (!$chiesa) ? 'selected' : '' ?>>
                        <?= Text::_('COM_MESSE_TUTTE_CHIESE') ?>
                    </option>
                    <?php foreach ($chiese as $c) : ?>
                    <option value="<?= (int) $c->id ?>"
                        <?= ($chiesa && (int) $c->id === (int) $chiesa->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c->nome) ?>
                        <?= $c->rito === 'ambrosiano'
                            ? ' (' . Text::_('COM_MESSE_RITO_AMBROSIANO') . ')'
                            : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($chiesa) :
        // Vista chiesa singola
        renderOrariChiesa($chiesa, $orari, $gruppi);

    elseif (!empty($tutteLeChiese)) :
        // Vista tutte le chiese
        $primo = true;
        foreach ($tutteLeChiese as $entry) :
            if (!$primo) : ?>
                <hr class="my-5">
            <?php endif;
            $primo = false;
            renderOrariChiesa($entry['chiesa'], $entry['orari'], $gruppi);
        endforeach;

    else : ?>
        <div class="alert alert-info"><?= Text::_('COM_MESSE_NESSUNA_CHIESA') ?></div>
    <?php endif; ?>

</div>

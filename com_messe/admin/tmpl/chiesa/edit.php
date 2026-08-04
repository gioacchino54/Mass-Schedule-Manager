<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Template backend — Form modifica chiesa
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$item = $this->item;
$tipi = [
    'feriale'    => 'Feriale (Lun–Ven)',
    'vigilia'    => 'Vigilia (Sabato)',
    'festivo'    => 'Festivo (Dom + Solennità)',
    'prefestivo' => 'Prefestiva dedicata (usata solo se Modalità = "Orario dedicato")',
];

$orariPerTipo = ['feriale' => [], 'vigilia' => [], 'festivo' => [], 'prefestivo' => []];
foreach ($item->orari ?? [] as $o) {
    $orariPerTipo[$o->tipo][] = $o;
}

// Carica Bootstrap tooltip
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// JavaScript per submitbutton - necessario per toolbar Joomla 5/6
Factory::getApplication()->getDocument()->addScriptDeclaration('
    Joomla.submitbutton = function(task) {
        var form = document.getElementById("adminForm");
        if (form) {
            form.task.value = task;
            form.submit();
        }
    };
');
?>

<form action="<?= Route::_('index.php?option=com_messe') ?>"
      method="post" name="adminForm" id="adminForm">

<div class="row">
<div class="col-lg-9">

    <!-- DATI PRINCIPALI -->
    <div class="card mb-3">
        <div class="card-header fw-bold"><?= Text::_('COM_MESSE_DATI_CHIESA') ?></div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">
                    <?= Text::_('COM_MESSE_CAMPO_NOME') ?>
                    <span class="text-danger">*</span>
                </label>
                <input type="text" name="jform[nome]" class="form-control" required
                       value="<?= $this->escape($item->nome ?? '') ?>" />
            </div>

            <div class="mb-3">
                <label class="form-label"><?= Text::_('COM_MESSE_CAMPO_INDIRIZZO') ?></label>
                <input type="text" name="jform[indirizzo]" class="form-control"
                       value="<?= $this->escape($item->indirizzo ?? '') ?>" />
            </div>

            <div class="mb-3">
                <label class="form-label"><?= Text::_('COM_MESSE_CAMPO_DESCRIZIONE') ?></label>
                <textarea name="jform[descrizione]" class="form-control" rows="2"><?= $this->escape($item->descrizione ?? '') ?></textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><?= Text::_('COM_MESSE_CAMPO_RITO') ?></label>
                    <select name="jform[rito]" class="form-select">
                        <option value="romano"     <?= ($item->rito ?? 'romano') === 'romano'     ? 'selected' : '' ?>><?= Text::_('COM_MESSE_RITO_ROMANO') ?></option>
                        <option value="ambrosiano" <?= ($item->rito ?? '') === 'ambrosiano' ? 'selected' : '' ?>><?= Text::_('COM_MESSE_RITO_AMBROSIANO') ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= Text::_('COM_MESSE_CAMPO_PUBBLICATA') ?></label>
                    <select name="jform[published]" class="form-select">
                        <option value="1" <?= (int) ($item->published ?? 1) === 1 ? 'selected' : '' ?>>Sì</option>
                        <option value="0" <?= (int) ($item->published ?? 1) === 0 ? 'selected' : '' ?>>No</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- VEGLIA PASQUALE -->
    <div class="card mb-3">
        <div class="card-header fw-bold">🕯️ <?= Text::_('COM_MESSE_VEGLIA_SEZIONE') ?></div>
        <div class="card-body">
            <p class="text-muted small mb-3"><?= Text::_('COM_MESSE_VEGLIA_NOTE') ?></p>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_ORA') ?></label>
                    <input type="number" class="form-control form-control-sm"
                           name="jform[ora_veglia]" min="0" max="23"
                           value="<?= (int) ($item->ora_veglia ?? 21) ?>" />
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_MINUTI') ?></label>
                    <input type="number" class="form-control form-control-sm"
                           name="jform[minuti_veglia]" min="0" max="59" step="5"
                           value="<?= (int) ($item->minuti_veglia ?? 0) ?>" />
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <span class="text-muted small">
                        <?= sprintf('%02d:%02d', (int) ($item->ora_veglia ?? 21), (int) ($item->minuti_veglia ?? 0)) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALITÀ MESSA PREFESTIVA -->
    <div class="card mb-3">
        <div class="card-header fw-bold">🕕 <?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_SEZIONE') ?></div>
        <div class="card-body">
            <p class="text-muted small mb-3"><?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_NOTE') ?></p>
            <?php $modalitaAttuale = $item->modalita_prefestiva ?? 'feriale_serale'; ?>
            <select name="jform[modalita_prefestiva]" class="form-select">
                <option value="nessuna" <?= $modalitaAttuale === 'nessuna' ? 'selected' : '' ?>>
                    <?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_NESSUNA') ?>
                </option>
                <option value="vigiliare" <?= $modalitaAttuale === 'vigiliare' ? 'selected' : '' ?>>
                    <?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_VIGILIARE') ?>
                </option>
                <option value="dedicato" <?= $modalitaAttuale === 'dedicato' ? 'selected' : '' ?>>
                    <?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_DEDICATO') ?>
                </option>
                <option value="feriale_serale" <?= $modalitaAttuale === 'feriale_serale' ? 'selected' : '' ?>>
                    <?= Text::_('COM_MESSE_MODALITA_PREFESTIVA_FERIALE_SERALE') ?>
                </option>
            </select>
        </div>
    </div>

    <!-- ORARI PER TIPO -->
    <?php foreach ($tipi as $tipoKey => $tipoLabel) : ?>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold"><?= Text::_('COM_MESSE_ORARI_' . strtoupper($tipoKey)) ?></span>
            <button type="button" class="btn btn-sm btn-success"
                    onclick="aggiungiOrario('<?= $tipoKey ?>')">
                <?= Text::_('COM_MESSE_BTN_AGGIUNGI_ORARIO') ?>
            </button>
        </div>
        <div class="card-body p-2">
            <div id="orari-<?= $tipoKey ?>">
                <?php foreach ($orariPerTipo[$tipoKey] as $idx => $o) : ?>
                <div class="row g-2 mb-2 align-items-end orario-row">
                    <div class="col-md-2">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_ORA') ?></label>
                        <input type="number" class="form-control form-control-sm" min="0" max="23"
                               name="jform[orari][<?= $tipoKey ?>][<?= $idx ?>][ora]"
                               value="<?= (int) $o->ora ?>" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_MINUTI') ?></label>
                        <input type="number" class="form-control form-control-sm" min="0" max="59" step="5"
                               name="jform[orari][<?= $tipoKey ?>][<?= $idx ?>][minuti]"
                               value="<?= (int) $o->minuti ?>" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_ETICHETTA') ?></label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[orari][<?= $tipoKey ?>][<?= $idx ?>][label]"
                               value="<?= $this->escape($o->label ?? '') ?>"
                               placeholder="es. Lodi, Solo Vespri…" />
                    </div>
                    <?php if (in_array($tipoKey, ['feriale', 'prefestivo'])) : ?>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_GIORNI') ?></label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[orari][<?= $tipoKey ?>][<?= $idx ?>][giorni]"
                               value="<?= $this->escape($o->giorni ?? '') ?>"
                               placeholder='es. [1,2,3,4,5]' />
                    </div>
                    <?php endif; ?>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger w-100"
                                onclick="this.closest('.orario-row').remove()">✕</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- CELEBRAZIONI SPECIALI -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">🌟 <?= Text::_('COM_MESSE_CELEBRAZIONI_SPECIALI') ?></span>
            <button type="button" class="btn btn-sm btn-success"
                    onclick="aggiungiEccezione()">
                <?= Text::_('COM_MESSE_BTN_AGGIUNGI_CELEBRAZIONE') ?>
            </button>
        </div>
        <div class="card-body p-2">
            <p class="text-muted small mb-2"><?= Text::_('COM_MESSE_NOTE_VIGILIA_PASQUALE') ?></p>
            <div id="eccezioni-lista">
                <?php foreach ($item->eccezioni ?? [] as $idx => $e) : ?>
                <div class="row g-2 mb-2 align-items-end eccezione-row">
                    <div class="col-md-2">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_DATA_MD') ?></label>
                        <input type="text" class="form-control form-control-sm" maxlength="5"
                               name="jform[eccezioni][<?= $idx ?>][data_md]"
                               value="<?= $this->escape($e->data_md) ?>"
                               placeholder="12-24" />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_ORA') ?></label>
                        <input type="number" class="form-control form-control-sm" min="0" max="23"
                               name="jform[eccezioni][<?= $idx ?>][ora]"
                               value="<?= (int) $e->ora ?>" />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_MINUTI') ?></label>
                        <input type="number" class="form-control form-control-sm" min="0" max="59" step="5"
                               name="jform[eccezioni][<?= $idx ?>][minuti]"
                               value="<?= (int) $e->minuti ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_DESCRIZIONE_CELEBRAZIONE') ?></label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[eccezioni][<?= $idx ?>][label]"
                               value="<?= $this->escape($e->label ?? '') ?>"
                               placeholder="es. Messa della Notte di Natale" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm"><?= Text::_('COM_MESSE_CAMPO_LUOGO') ?></label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[eccezioni][<?= $idx ?>][luogo]"
                               value="<?= $this->escape($e->luogo ?? '') ?>"
                               placeholder="es. Chiesa parrocchiale" />
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger w-100"
                                onclick="this.closest('.eccezione-row').remove()">✕</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- VARIAZIONI STAGIONALI -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">📅 <?= Text::_('COM_MESSE_VARIAZIONI_STAGIONALI') ?></span>
            <button type="button" class="btn btn-sm btn-success"
                    onclick="aggiungiPeriodo()">+ Aggiungi variazione</button>
        </div>
        <div class="card-body p-2">
            <p class="text-muted small mb-2">
                Definisci periodi in cui alcuni orari vengono soppressi o sostituiti.
            </p>
            <div id="periodi-lista">
                <?php foreach ($item->periodi ?? [] as $idx => $p) :
                    $mesiSel    = json_decode($p->mesi ?? '[]', true);
                    $orariNuovi = json_decode($p->orari_nuovi ?? '[]', true);
                    $nomiMesi   = ['','Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
                ?>
                <div class="card mb-2 periodo-row border-secondary">
                    <div class="card-body p-2">
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label form-label-sm">Nome variazione</label>
                                <input type="text" class="form-control form-control-sm"
                                       name="jform[periodi][<?= $idx ?>][nome]"
                                       value="<?= $this->escape($p->nome ?? '') ?>"
                                       placeholder="es. Orario estivo" />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label form-label-sm">Tipo periodo</label>
                                <select name="jform[periodi][<?= $idx ?>][tipo_data]"
                                        class="form-select form-select-sm"
                                        onchange="toggleTipoData(this)">
                                    <option value="mesi" <?= ($p->tipo_data ?? 'mesi') === 'mesi' ? 'selected' : '' ?>>Per mese</option>
                                    <option value="date" <?= ($p->tipo_data ?? '') === 'date'  ? 'selected' : '' ?>>Per data</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label form-label-sm">Tipo orario</label>
                                <select name="jform[periodi][<?= $idx ?>][tipo_orario]"
                                        class="form-select form-select-sm">
                                    <option value="feriale" <?= ($p->tipo_orario ?? '') === 'feriale' ? 'selected' : '' ?>>Feriale</option>
                                    <option value="vigilia" <?= ($p->tipo_orario ?? '') === 'vigilia' ? 'selected' : '' ?>>Vigilia</option>
                                    <option value="festivo" <?= ($p->tipo_orario ?? '') === 'festivo' ? 'selected' : '' ?>>Festivo</option>
                                    <option value="tutti"   <?= ($p->tipo_orario ?? '') === 'tutti'   ? 'selected' : '' ?>>Tutti</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label form-label-sm">Azione</label>
                                <select name="jform[periodi][<?= $idx ?>][azione]"
                                        class="form-select form-select-sm"
                                        onchange="toggleAzione(this)">
                                    <option value="sopprimi"    <?= ($p->azione ?? '') === 'sopprimi'    ? 'selected' : '' ?>>Sopprimi</option>
                                    <option value="sostituisci" <?= ($p->azione ?? '') === 'sostituisci' ? 'selected' : '' ?>>Sostituisci</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger w-100"
                                        onclick="this.closest('.periodo-row').remove()">✕</button>
                            </div>
                        </div>

                        <!-- Selezione mesi -->
                        <div class="periodo-mesi-wrapper mb-2 <?= ($p->tipo_data ?? 'mesi') !== 'mesi' ? 'd-none' : '' ?>">
                            <label class="form-label form-label-sm">Mesi attivi</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php for ($nm = 1; $nm <= 12; $nm++) : ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="jform[periodi][<?= $idx ?>][mesi][]"
                                           value="<?= $nm ?>"
                                           <?= in_array($nm, $mesiSel) ? 'checked' : '' ?> />
                                    <label class="form-check-label small"><?= $nomiMesi[$nm] ?></label>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Selezione date -->
                        <div class="periodo-date-wrapper mb-2 <?= ($p->tipo_data ?? '') !== 'date' ? 'd-none' : '' ?>">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm">Data inizio (MM-GG)</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="jform[periodi][<?= $idx ?>][data_inizio]"
                                           value="<?= $this->escape($p->data_inizio ?? '') ?>"
                                           placeholder="07-01" maxlength="5" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label form-label-sm">Data fine (MM-GG)</label>
                                    <input type="text" class="form-control form-control-sm"
                                           name="jform[periodi][<?= $idx ?>][data_fine]"
                                           value="<?= $this->escape($p->data_fine ?? '') ?>"
                                           placeholder="08-31" maxlength="5" />
                                </div>
                            </div>
                        </div>

                        <!-- Orari sostitutivi -->
                        <div class="periodo-orari-wrapper <?= ($p->azione ?? '') !== 'sostituisci' ? 'd-none' : '' ?>">
                            <label class="form-label form-label-sm">Orari sostitutivi</label>
                            <div class="orari-sostitutivi-lista">
                                <?php foreach ($orariNuovi as $oidx => $oa) : ?>
                                <div class="row g-1 mb-1 orario-sost-row">
                                    <div class="col-2">
                                        <input type="number" class="form-control form-control-sm"
                                               min="0" max="23"
                                               name="jform[periodi][<?= $idx ?>][orari_nuovi][<?= $oidx ?>][h]"
                                               value="<?= (int) $oa['h'] ?>" />
                                    </div>
                                    <div class="col-2">
                                        <input type="number" class="form-control form-control-sm"
                                               min="0" max="59" step="5"
                                               name="jform[periodi][<?= $idx ?>][orari_nuovi][<?= $oidx ?>][m]"
                                               value="<?= (int) $oa['m'] ?>" />
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control form-control-sm"
                                               name="jform[periodi][<?= $idx ?>][orari_nuovi][<?= $oidx ?>][label]"
                                               value="<?= $this->escape($oa['label'] ?? '') ?>"
                                               placeholder="Etichetta" />
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="this.closest('.orario-sost-row').remove()">✕</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                                    onclick="aggiungiOrarioSostitutivo(this)">+ Orario</button>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div><!-- /col-lg-9 -->
</div><!-- /row -->

<input type="hidden" name="jform[id]" value="<?= (int) ($item->id ?? 0) ?>" />
<input type="hidden" name="task"      value="" />
<input type="hidden" name="option"    value="com_messe" />
<?= HTMLHelper::_('form.token') ?>
</form>

<?php require __DIR__ . '/../_footer.php'; ?>

<script>
let orarioIdx    = { feriale: 9000, vigilia: 9000, festivo: 9000, prefestivo: 9000 };
let eccezioneIdx = 9000;
let periodoIdx   = 9000;
let orarioSostIdx = 9000;

function aggiungiOrario(tipo) {
    const idx       = orarioIdx[tipo]++;
    const isFeriale = tipo === 'feriale' || tipo === 'prefestivo';
    const giornoField = isFeriale ? `
        <div class="col-md-3">
            <label class="form-label form-label-sm">Giorni (vuoto = tutti)</label>
            <input type="text" class="form-control form-control-sm"
                   name="jform[orari][${tipo}][${idx}][giorni]"
                   placeholder="es. [1,2,3,4,5]" />
        </div>` : '';

    document.getElementById('orari-' + tipo).insertAdjacentHTML('beforeend', `
    <div class="row g-2 mb-2 align-items-end orario-row">
        <div class="col-md-2">
            <label class="form-label form-label-sm">Ora</label>
            <input type="number" class="form-control form-control-sm" min="0" max="23"
                   name="jform[orari][${tipo}][${idx}][ora]" value="8" />
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm">Minuti</label>
            <input type="number" class="form-control form-control-sm" min="0" max="59" step="5"
                   name="jform[orari][${tipo}][${idx}][minuti]" value="0" />
        </div>
        <div class="col-md-4">
            <label class="form-label form-label-sm">Etichetta (opzionale)</label>
            <input type="text" class="form-control form-control-sm"
                   name="jform[orari][${tipo}][${idx}][label]" placeholder="es. Lodi" />
        </div>
        ${giornoField}
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger w-100"
                    onclick="this.closest('.orario-row').remove()">✕</button>
        </div>
    </div>`);
}

function aggiungiEccezione() {
    const idx = eccezioneIdx++;
    document.getElementById('eccezioni-lista').insertAdjacentHTML('beforeend', `
    <div class="row g-2 mb-2 align-items-end eccezione-row">
        <div class="col-md-2">
            <label class="form-label form-label-sm">Data (MM-GG)</label>
            <input type="text" class="form-control form-control-sm" maxlength="5"
                   name="jform[eccezioni][${idx}][data_md]" placeholder="12-24" />
        </div>
        <div class="col-md-1">
            <label class="form-label form-label-sm">Ora</label>
            <input type="number" class="form-control form-control-sm" min="0" max="23"
                   name="jform[eccezioni][${idx}][ora]" value="18" />
        </div>
        <div class="col-md-1">
            <label class="form-label form-label-sm">Min</label>
            <input type="number" class="form-control form-control-sm" min="0" max="59" step="5"
                   name="jform[eccezioni][${idx}][minuti]" value="0" />
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm">Descrizione</label>
            <input type="text" class="form-control form-control-sm"
                   name="jform[eccezioni][${idx}][label]"
                   placeholder="es. Messa della Notte di Natale" />
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm">Luogo (opzionale)</label>
            <input type="text" class="form-control form-control-sm"
                   name="jform[eccezioni][${idx}][luogo]"
                   placeholder="es. Chiesa parrocchiale" />
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger w-100"
                    onclick="this.closest('.eccezione-row').remove()">✕</button>
        </div>
    </div>`);
}

function aggiungiPeriodo() {
    const idx = periodoIdx++;
    const nomiMesi = ['','Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
    let checkboxMesi = '';
    for (let m = 1; m <= 12; m++) {
        checkboxMesi += `<div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox"
                   name="jform[periodi][${idx}][mesi][]" value="${m}" />
            <label class="form-check-label small">${nomiMesi[m]}</label>
        </div>`;
    }

    document.getElementById('periodi-lista').insertAdjacentHTML('beforeend', `
    <div class="card mb-2 periodo-row border-secondary">
        <div class="card-body p-2">
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <label class="form-label form-label-sm">Nome variazione</label>
                    <input type="text" class="form-control form-control-sm"
                           name="jform[periodi][${idx}][nome]" placeholder="es. Orario estivo" />
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Tipo periodo</label>
                    <select name="jform[periodi][${idx}][tipo_data]"
                            class="form-select form-select-sm" onchange="toggleTipoData(this)">
                        <option value="mesi">Per mese</option>
                        <option value="date">Per data</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Tipo orario</label>
                    <select name="jform[periodi][${idx}][tipo_orario]" class="form-select form-select-sm">
                        <option value="feriale">Feriale</option>
                        <option value="vigilia">Vigilia</option>
                        <option value="festivo">Festivo</option>
                        <option value="tutti">Tutti</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label form-label-sm">Azione</label>
                    <select name="jform[periodi][${idx}][azione]"
                            class="form-select form-select-sm" onchange="toggleAzione(this)">
                        <option value="sopprimi">Sopprimi</option>
                        <option value="sostituisci">Sostituisci</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100"
                            onclick="this.closest('.periodo-row').remove()">✕</button>
                </div>
            </div>
            <div class="periodo-mesi-wrapper mb-2">
                <label class="form-label form-label-sm">Mesi attivi</label>
                <div class="d-flex flex-wrap gap-2">${checkboxMesi}</div>
            </div>
            <div class="periodo-date-wrapper mb-2 d-none">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Data inizio (MM-GG)</label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[periodi][${idx}][data_inizio]"
                               placeholder="07-01" maxlength="5" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Data fine (MM-GG)</label>
                        <input type="text" class="form-control form-control-sm"
                               name="jform[periodi][${idx}][data_fine]"
                               placeholder="08-31" maxlength="5" />
                    </div>
                </div>
            </div>
            <div class="periodo-orari-wrapper d-none">
                <label class="form-label form-label-sm">Orari sostitutivi</label>
                <div class="orari-sostitutivi-lista"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                        onclick="aggiungiOrarioSostitutivo(this)">+ Orario</button>
            </div>
        </div>
    </div>`);
}

function toggleTipoData(sel) {
    const card = sel.closest('.periodo-row');
    card.querySelector('.periodo-mesi-wrapper').classList.toggle('d-none', sel.value !== 'mesi');
    card.querySelector('.periodo-date-wrapper').classList.toggle('d-none', sel.value !== 'date');
}

function toggleAzione(sel) {
    const card = sel.closest('.periodo-row');
    card.querySelector('.periodo-orari-wrapper').classList.toggle('d-none', sel.value !== 'sostituisci');
}

function aggiungiOrarioSostitutivo(btn) {
    const lista  = btn.previousElementSibling;
    const select = btn.closest('.periodo-row').querySelector('select[name*="[azione]"]');
    const match  = select.name.match(/periodi\]\[(\d+)\]/);
    const pIdx   = match ? match[1] : periodoIdx;
    const oIdx   = orarioSostIdx++;

    lista.insertAdjacentHTML('beforeend', `
    <div class="row g-1 mb-1 orario-sost-row">
        <div class="col-2">
            <input type="number" class="form-control form-control-sm" min="0" max="23"
                   name="jform[periodi][${pIdx}][orari_nuovi][${oIdx}][h]" value="8" />
        </div>
        <div class="col-2">
            <input type="number" class="form-control form-control-sm" min="0" max="59" step="5"
                   name="jform[periodi][${pIdx}][orari_nuovi][${oIdx}][m]" value="0" />
        </div>
        <div class="col-4">
            <input type="text" class="form-control form-control-sm"
                   name="jform[periodi][${pIdx}][orari_nuovi][${oIdx}][label]"
                   placeholder="Etichetta" />
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-sm btn-danger"
                    onclick="this.closest('.orario-sost-row').remove()">✕</button>
        </div>
    </div>`);
}
</script>

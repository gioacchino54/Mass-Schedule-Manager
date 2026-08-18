<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Template backend — Lista Chiese
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
?>
<form action="<?= Route::_('index.php?option=com_messe&view=chiese') ?>"
      method="post" name="adminForm" id="adminForm">

    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle"
                           onclick="Joomla.checkAll(this)" />
                </th>
                <th>Nome Chiesa</th>
                <th width="12%">Rito</th>
                <th width="25%">Indirizzo</th>
                <th width="8%" class="text-center">Pubblicata</th>
                <th width="5%">ID</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($this->items)) : ?>
            <tr>
                <td colspan="6" class="text-center py-3">
                    Nessuna chiesa trovata.
                    <a href="<?= Route::_('index.php?option=com_messe&view=chiesa&layout=edit') ?>">
                        Aggiungi la prima chiesa.
                    </a>
                </td>
            </tr>
        <?php else : ?>
            <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td><?= HTMLHelper::_('grid.id', $i, $item->id) ?></td>
                <td>
                    <a href="<?= Route::_('index.php?option=com_messe&view=chiesa&layout=edit&id=' . (int) $item->id) ?>">
                        <?= $this->escape($item->nome) ?>
                    </a>
                </td>
                <td>
                    <?php if ($item->rito === 'ambrosiano') : ?>
                        <span class="badge" style="
                                border:1px solid #0dcaf0;
                                color:#06748b;
                                background:rgba(13,202,240,0.1);
                                font-weight:600;
                            "><?= Text::_('COM_MESSE_RITO_AMBROSIANO') ?></span>
                    <?php else : ?>
                        <span class="badge" style="
                                border:1px solid #6c757d;
                                color:#495057;
                                background:rgba(108,117,125,0.1);
                                font-weight:600;
                            "><?= Text::_('COM_MESSE_RITO_ROMANO') ?></span>
                    <?php endif; ?>
                </td>
                <td><?= $this->escape($item->indirizzo ?? '—') ?></td>
                <td class="text-center">
                    <?= HTMLHelper::_('jgrid.published', $item->published, $i, 'chiese.') ?>
                </td>
                <td><?= (int) $item->id ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <?= $this->pagination->getListFooter() ?>

    <input type="hidden" name="task"        value="" />
    <input type="hidden" name="boxchecked"  value="0" />
    <?= HTMLHelper::_('form.token') ?>
</form>

<?php require __DIR__ . '/../_footer.php'; ?>

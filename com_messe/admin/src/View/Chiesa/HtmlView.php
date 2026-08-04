<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * View backend — Form modifica chiesa
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\View\Chiesa;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    public $item;

    public function display($tpl = null)
    {
        $model      = $this->getModel();
        $this->item = $model->getItem();

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $isNew = ((int) $this->item->id === 0);

        ToolbarHelper::title(
            $isNew ? 'Nuova Chiesa' : 'Modifica: ' . $this->item->nome,
            'church'
        );

        // 1. Salva — salva e rimane in modalità modifica sulla stessa chiesa
        ToolbarHelper::apply('chiesa.apply', 'JTOOLBAR_APPLY');

        // 2. Salva e Nuovo — salva e apre un form vuoto per una nuova chiesa
        ToolbarHelper::save2new('chiesa.save2new', 'JTOOLBAR_SAVE_AND_NEW');

        // 3. Salva e Chiudi — salva e torna alla lista
        ToolbarHelper::save('chiesa.save', 'JTOOLBAR_SAVE');

        // 4. Chiudi — chiude senza salvare
        ToolbarHelper::cancel(
            'chiesa.cancel',
            $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
        );
    }
}

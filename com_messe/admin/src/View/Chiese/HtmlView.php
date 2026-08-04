<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * View backend — Lista Chiese
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\View\Chiese;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public $items;
    public $pagination;
    public $state;

    public function display($tpl = null)
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title('Gestione Orari Messe — Chiese', 'calendar');
        ToolbarHelper::addNew('chiesa.add');
        ToolbarHelper::editList('chiesa.edit');
        ToolbarHelper::publishList('chiese.publish');
        ToolbarHelper::unpublishList('chiese.unpublish');
        ToolbarHelper::deleteList('', 'chiese.delete');
        ToolbarHelper::preferences('com_messe');
    }
}

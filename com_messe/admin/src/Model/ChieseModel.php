<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Model backend — Lista Chiese
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class ChieseModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'nome', 'rito', 'published', 'ordering'];
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['id', 'nome', 'rito', 'indirizzo', 'published', 'ordering'])
              ->from($db->quoteName('#__messe_chiese'))
              ->order('ordering ASC, nome ASC');

        $published = $this->getState('filter.published', '');
        if ($published !== '') {
            $query->where($db->quoteName('published') . ' = ' . (int) $published);
        }

        return $query;
    }
}

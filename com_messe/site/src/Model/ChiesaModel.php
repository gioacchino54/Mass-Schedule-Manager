<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Model frontend — Chiesa
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ChiesaModel extends BaseDatabaseModel
{
    public function getChiesa(int $id): ?object
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_chiese'))
            ->where($db->quoteName('id')        . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa = $db->loadObject();

        if (!$chiesa) return null;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_orari'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->order('tipo ASC, ordering ASC');
        $db->setQuery($query);
        $chiesa->orari = $db->loadObjectList();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_eccezioni'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1')
            ->order('data_md ASC');
        $db->setQuery($query);
        $chiesa->eccezioni = $db->loadObjectList();

        // Periodi stagionali
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_periodi'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa->periodi = $db->loadObjectList();

        // Settimana Santa
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__messe_settimana_santa'))
            ->where($db->quoteName('chiesa_id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $chiesa->settimanaSanta = $db->loadObjectList();

        return $chiesa;
    }

    public function getChiese(): array
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select(['id', 'nome', 'indirizzo', 'rito'])
            ->from($db->quoteName('#__messe_chiese'))
            ->where($db->quoteName('published') . ' = 1')
            ->order('ordering ASC, nome ASC');
        $db->setQuery($query);

        return $db->loadObjectList() ?? [];
    }
}

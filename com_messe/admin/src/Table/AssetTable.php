<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Table — Asset
 *
 * Registra correttamente il componente nell'albero degli asset di Joomla
 * impedendo che il rebuild sovrascriva le rules con valori di default sbagliati.
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Rules;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class AssetTable extends \Joomla\CMS\Table\Asset
{
    /**
     * Rules corrette per com_messe
     */
    private const DEFAULT_RULES = [
        'core.admin'      => [],
        'core.manage'     => [],
        'core.create'     => [],
        'core.delete'     => [],
        'core.edit'       => [],
        'core.edit.state' => [],
    ];

    public function __construct(DatabaseInterface $db)
    {
        parent::__construct($db);
    }

    /**
     * Sovrascrive store() per assicurarsi che le rules
     * non vengano mai sovrascritte con il formato sbagliato
     */
    public function store($updateNulls = true): bool
    {
        // Se stiamo salvando il record di com_messe
        if (isset($this->name) && $this->name === 'com_messe') {
            $rules = json_decode($this->rules ?? '{}', true);

            // Se le rules hanno il formato sbagliato, le corregge
            if (!is_array($rules) || !isset($rules['core.admin'])) {
                $this->rules = json_encode(self::DEFAULT_RULES);
            }
        }

        return parent::store($updateNulls);
    }
}

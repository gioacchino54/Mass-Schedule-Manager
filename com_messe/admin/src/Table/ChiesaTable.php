<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Table — Chiesa
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;

class ChiesaTable extends Table
{
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__messe_chiese', 'id', $db);
    }

    public function check(): bool
    {
        if (empty(trim($this->nome))) {
            $this->setError('Il nome della chiesa è obbligatorio.');
            return false;
        }

        if (!in_array($this->rito, ['romano', 'ambrosiano'])) {
            $this->rito = 'romano';
        }

        return true;
    }
}

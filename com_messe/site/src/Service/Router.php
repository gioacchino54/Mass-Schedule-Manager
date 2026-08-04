<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Router — Frontend
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterBase;
use Joomla\Database\DatabaseInterface;

class Router extends RouterBase
{
    public function __construct(SiteApplication $app, DatabaseInterface $db)
    {
        parent::__construct($app, $db);
    }

    /**
     * Converte URL interno in SEF
     * es. index.php?option=com_messe&view=chiesa&id=2&Itemid=123
     * diventa: alias-menu/2
     */
    public function build(&$query): array
    {
        $segments = [];

        if (!empty($query['id']) && (int) $query['id'] > 0) {
            $segments[] = (int) $query['id'];
            unset($query['id']);
        }

        unset($query['view']);

        return $segments;
    }

    /**
     * Converte URL SEF in variabili interne
     * es. alias-menu/2 -> view=chiesa, id=2
     */
    public function parse(&$segments): array
    {
        $vars = ['view' => 'chiesa'];

        if (!empty($segments) && is_numeric($segments[0])) {
            $vars['id'] = (int) array_shift($segments);
        }

        return $vars;
    }
}

<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Extension — Frontend
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\RouterServiceInterface;
use Joomla\CMS\Extension\RouterServiceTrait;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceTrait;
use Joomla\Database\DatabaseInterface;

class MesseComponent extends MVCComponent
    implements MVCFactoryServiceInterface, RouterServiceInterface
{
    use MVCFactoryServiceTrait;
    use RouterServiceTrait;

    public function createRouter(SiteApplication $app, DatabaseInterface $db): RouterInterface
    {
        return new \GioachinoCipriano\Component\Messe\Site\Service\Router($app, $db);
    }
}

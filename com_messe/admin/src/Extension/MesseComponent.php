<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Extension — Backend
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

class MesseComponent extends MVCComponent
    implements MVCFactoryServiceInterface, BootableExtensionInterface
{
    use MVCFactoryServiceTrait;
    use HTMLRegistryAwareTrait;

    public function boot(ContainerInterface $container): void
    {
        // Nessuna operazione necessaria al boot
    }
}

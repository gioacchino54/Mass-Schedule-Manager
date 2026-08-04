<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Service Provider — Backend
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use GioachinoCipriano\Component\Messe\Administrator\Extension\MesseComponent;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(
            new MVCFactory('\\GioachinoCipriano\\Component\\Messe')
        );

        $container->registerServiceProvider(
            new ComponentDispatcherFactory('\\GioachinoCipriano\\Component\\Messe')
        );

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new MesseComponent(
                    $container->get(ComponentDispatcherFactoryInterface::class)
                );

                $component->setMVCFactory(
                    $container->get(MVCFactoryInterface::class)
                );

                return $component;
            }
        );
    }
};

<?php
/**
 * Gestione Orari Messe by Gioacchino Cipriano
 * Controller frontend — Display
 *
 * @copyright   Copyright (C) 2026 Gioacchino Cipriano. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace GioachinoCipriano\Component\Messe\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class DisplayController extends BaseController
{
    protected $default_view = 'chiesa';

    public function display($cachable = false, $urlparams = [])
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();

        // Forza sempre la vista chiesa
        $input->set('view', 'chiesa');

        // Se SEF non è attivo e manca view nell'URL originale,
        // assicuriamoci che id venga passato correttamente
        $id = $input->getInt('id', 0);
        if ($id) {
            $input->set('id', $id);
        }

        return parent::display($cachable, $urlparams);
    }
}

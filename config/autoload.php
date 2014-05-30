<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package newspagination
 * @copyright Dominik Zogg <dominik.zogg@gmail.com>
 * @author Dominik Zogg
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'DominikZogg',
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Hooks
    'DominikZogg\NewsPaginationHook'     => 'system/modules/newspagination/hooks/NewsPaginationHook.php',

    // Modules
    'DominikZogg\NewsPagination'         => 'system/modules/newspagination/modules/NewsPagination.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'mod_newsreader'    => 'system/modules/newspagination/templates',
    'newspagination'    => 'system/modules/newspagination/templates',
));
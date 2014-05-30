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

$GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader'] = str_replace(
    '{template_legend}',
    '{newspagination_legend},addNewspagination;{template_legend}',
    $GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader']
);

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'addNewspagination';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['addNewspagination'] = 'news_paginationCount,news_paginationShowtitle';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['addNewspagination'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['addNewspagination'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => array
    (
        'submitOnChange' => true,
    ),
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news_paginationCount'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['news_paginationCount'],
    'exclude' => true,
    'inputType' => 'text',
    'default' => 10,
    'eval' => array
    (
        'mandatory' => true,
        'rgxp' => 'digit',
        'tl_class' => 'w50',
    ),
);

$GLOBALS['TL_DCA']['tl_module']['fields']['news_paginationShowtitle'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_module']['news_paginationShowtitle'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => array
    (
        'tl_class' => 'w50',
    ),
);
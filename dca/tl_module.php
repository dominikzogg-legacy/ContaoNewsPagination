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
 * Add subpalettes
 */
$arrAddSubpalette = array
(
    'newspagination' => array
    (
        'palette' => 'template_legend',
        'subpalette' => 'news_paginationCount,news_paginationShowtitle',
    ),
);
foreach($arrAddSubpalette as $strSubpaletteName => $arrPaletteAndSubpaletteInfo)
{
    // modificate palette
    foreach($GLOBALS['TL_DCA']['tl_module']['palettes'] as $strKey => $strValue)
    {
        if($strKey != '__selector__')
        {
            $GLOBALS['TL_DCA']['tl_module']['palettes'][$strKey] = str_replace
            (
                $arrPaletteAndSubpaletteInfo['palette'],
                $strSubpaletteName . '_legend},add' . ucfirst($strSubpaletteName) . ';{' . $arrPaletteAndSubpaletteInfo['palette'],
                $GLOBALS['TL_DCA']['tl_module']['palettes'][$strKey]
            );
        }
    }

    // add as subpalette
    $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'add' . ucfirst($strSubpaletteName);
    $GLOBALS['TL_DCA']['tl_module']['subpalettes']['add' . ucfirst($strSubpaletteName)] = $arrPaletteAndSubpaletteInfo['subpalette'];
}


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
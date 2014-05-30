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

namespace DominikZogg;

class NewsPaginationHook
{
    public function addNewsPagination($objTemplate)
    {
        if(strpos(get_class($objTemplate), 'FrontendTemplate') !== false) {
            $objTemplate->addNewsPagination = function() use ($objTemplate) {
                $objNewsPagination = new \NewsPagination($objTemplate);
                return($objNewsPagination->generate());
            };
        }
    }
}
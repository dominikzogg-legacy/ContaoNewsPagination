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
 * addNewsPagination
 * @param object $objNewsReader news reader object
 * @return string rendered news pagination template
 */
function addNewsPagination($objNewsReader)
{
    $objNewsPagination = new NewsPagination($objNewsReader);
    return($objNewsPagination->generate());
}
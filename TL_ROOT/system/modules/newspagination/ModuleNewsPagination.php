<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Dominik Zogg 2011
 * @author     Dominik Zogg <dominik.zogg@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class ModuleNewsPagination extends ModuleNews
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_newspagination';

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### NEWS PAGINATION ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Return if no news item has been specified
        if (!$this->Input->get('items'))
        {
            return '';
        }

        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        $arrArticles = array();
        $intTime = time();
        $intCounter = 0;
        $intActive = 0;

        // Get news
        $objArticles = $this->Database->prepare("
            SELECT
                news.id,
                news.alias,
                news.headline
            FROM
                tl_news AS news
            LEFT JOIN
                tl_news AS relatednews ON (
                    (relatednews.id = ? OR relatednews.alias = ?)
                    " . (!BE_USER_LOGGED_IN ? " AND (relatednews.start = '' OR relatednews.start < ?) AND (relatednews.stop = '' OR relatednews.stop > ?) AND relatednews.published = 1" : "") . "
                )
            WHERE
                news.pid = relatednews.pid AND
                (news.text != '' OR news.id = relatednews.id)
                " . (!BE_USER_LOGGED_IN ? " AND (news.start = '' OR news.start < ?) AND (news.stop = '' OR news.stop > ?) AND news.published = 1" : "") . "
            ORDER BY
                news.date DESC
        ")->execute(
            (is_numeric($this->Input->get('items')) ? $this->Input->get('items') : 0),
            $this->Input->get('items'),
            $intTime,
            $intTime,
            $intTime,
            $intTime
        );

        while($objArticles->next())
        {
            // +1 counter
            $intCounter++;

            // get alias
            $strAlias = (strlen($objArticles->alias) && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $objArticles->alias : $objArticles->id;

            // add to articles array
            $arrArticle = array
            (
                'isActive' => $this->Input->get('items') == $strAlias ? true : false,
                'href' => $this->addToUrl('items=' . $strAlias),
                'title' => specialchars($objArticles->headline),
                'link' => $intCounter,
            );

            // add to articles array
            $arrArticles[$intCounter] = $arrArticle;

            // set active
            if($arrArticle['isActive'])
            {
                $intActive = $intCounter;
            }
        }

        // assign articles to template
        $this->Template->articles = $arrArticles;

        // assign array first
        if($intActive > 2)
        {
            $arrFirst = reset($arrArticles);
            $arrFirst['link'] = $GLOBALS['TL_LANG']['MSC']['first'];
            $this->Template->first = $arrFirst;
        }

        // assign array prev
        if($intActive > 1)
        {
            $arrPrevious = $arrArticles[$intActive-1];
            $arrPrevious['link'] = $GLOBALS['TL_LANG']['MSC']['previous'];
            $this->Template->previous = $arrPrevious;
        }

        // assign array next
        if($intActive < $intCounter)
        {
            $arrNext = $arrArticles[$intActive+1];
            $arrNext['link'] = $GLOBALS['TL_LANG']['MSC']['next'];
            $this->Template->next = $arrNext;
        }

        // assign array last
        if($intActive < $intCounter-1)
        {
            $arrLast = end($arrArticles);
            $arrLast['link'] = $GLOBALS['TL_LANG']['MSC']['last'];
            $this->Template->last = $arrLast;
        }
    }
}
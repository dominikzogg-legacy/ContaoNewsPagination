<?php if(!defined('TL_ROOT')) die('You cannot access this file directly!');

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

class NewsPagination extends ModuleNews
{
    /**
     * template
     * @var string
     */
    protected $strTemplate = 'newspagination';

    /**
     * __construct
     * @param object $objNewsReader news reader object
     */
    public function __construct($objNewsReader)
    {
        // check if the parameter seems to be ok
        if(!is_object($objNewsReader) || get_class($objNewsReader) != 'FrontendTemplate')
        {
            throw new Exception('illegal call!');
        }

        // get libraries
        $this->import('Input');
        $this->import('Database');

        // get data from news reader
        $this->item = $this->Input->get('items');
        $this->itemtoshow = $objNewsReader->news_paginationCount;
        $this->showtitle = $objNewsReader->news_paginationShowtitle;
        $this->news_archives = $objNewsReader->news_archives;
    }

    /**
     * generate
     * @return string
     */
    public function generate()
    {
        return parent::generate();
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        $arrAllArticles = array();
        $arrArticles = array();
        $intCounter = 0;
        $intActive = 0;
        $intStartAt = 0;
        $intStopAt = 0;
        $intTime = time();

        // get all news items who are in the configured archives
        $objArticles = $this->Database->prepare("
            SELECT
                news.id,
                news.alias,
                news.headline
            FROM
                tl_news AS news
            WHERE
                news.pid IN(" . implode(',', $this->news_archives) . ") AND
                news.text != ''
                " . (!BE_USER_LOGGED_IN ? " AND (news.start = '' OR news.start < ?) AND (news.stop = '' OR news.stop > ?) AND news.published = 1" : "") . "
            ORDER BY
                news.date DESC
        ")->execute($intTime, $intTime);

        while($objArticles->next())
        {
            // +1 counter
            $intCounter++;

            // get alias
            $strAlias = (strlen($objArticles->alias) && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $objArticles->alias : $objArticles->id;

            // add to articles array
            $arrArticle = array
            (
                'isActive' => $this->item == $strAlias ? true : false,
                'href' => $this->addToUrl('items=' . $strAlias),
                'title' => specialchars($objArticles->headline),
                'link' => !$this->showtitle ? $intCounter : $objArticles->headline,
            );

            // add to articles array
            $arrAllArticles[$intCounter] = $arrArticle;

            // set active
            if($arrArticle['isActive'])
            {
                $intActive = $intCounter;
            }
        }

        // assign total
        $this->Template->total = sprintf($GLOBALS['TL_LANG']['MSC']['totalPages'], $intActive, $intCounter);

        // assign all articles
        $this->Template->allarticles = $arrAllArticles;

        // assign array first
        if($intActive > 2)
        {
            $arrFirst = reset($arrAllArticles);
            $arrFirst['link'] = $GLOBALS['TL_LANG']['MSC']['first'];
            $this->Template->first = $arrFirst;
        }

        // assign array prev
        if($intActive > 1)
        {
            $arrPrevious = $arrAllArticles[$intActive-1];
            $arrPrevious['link'] = $GLOBALS['TL_LANG']['MSC']['previous'];
            $this->Template->previous = $arrPrevious;
        }

        // assign array next
        if($intActive < $intCounter)
        {
            $arrNext = $arrAllArticles[$intActive+1];
            $arrNext['link'] = $GLOBALS['TL_LANG']['MSC']['next'];
            $this->Template->next = $arrNext;
        }

        // assign array last
        if($intActive < $intCounter-1)
        {
            $arrLast = end($arrAllArticles);
            $arrLast['link'] = $GLOBALS['TL_LANG']['MSC']['last'];
            $this->Template->last = $arrLast;
        }

        // check if we show all
        if($this->itemtoshow > 0)
        {
            // set start at
            $intStartAt = floor($intActive - ($this->itemtoshow / 2));
            $intStartAt = $intCounter - $this->itemtoshow > $intStartAt ? $intStartAt : $intCounter - $this->itemtoshow;
            $intStartAt = $intStartAt > 1 ?$intStartAt : 1;

            // set stop at
            $intStopAt = $intStartAt + $this->itemtoshow;
            $intStopAt = $intStopAt <= $intCounter ? $intStopAt : $intCounter;

            // fill article to show array
            foreach($arrAllArticles as $intKey => $arrArticle)
            {
                if($intKey >= $intStartAt && $intKey <= $intStopAt)
                {
                    $arrArticles[$intKey] = $arrArticle;
                }
            }

            // assign articles
            $this->Template->articles = $arrArticles;

            // assign start at if bigger than one
            if($intStartAt > 1)
            {
                $arrStartAt = $arrArticles[$intStartAt];
                $arrStartAt['link'] = $GLOBALS['TL_LANG']['MSC']['points'];
                $this->Template->startat = $arrStartAt;
            }

            // assign stop at if its smaller the count
            if($intStopAt < $intCounter)
            {
                $arrStopAt = $arrArticles[$intStopAt];
                $arrStopAt['link'] = $GLOBALS['TL_LANG']['MSC']['points'];
                $this->Template->stopat = $arrStopAt;
            }
        }
        else
        {
            // assign articles
            $this->Template->articles = $arrAllArticles;
        }
    }
}
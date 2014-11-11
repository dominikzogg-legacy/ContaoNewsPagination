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

class NewsPagination extends \ModuleNews
{
    /**
     * @var string
     */
    protected $strTemplate = 'newspagination';

    /**
     * @param \FrontendTemplate $objNewsReader
     */
    public function __construct($objNewsReader)
    {
        // check if the parameter seems to be ok
        if(!is_object($objNewsReader) || strpos(get_class($objNewsReader), 'FrontendTemplate') === false)
        {
            throw new \Exception('illegal call!');
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
        // get page
        global $objPage;

        $arrAllArticles = array();
        $arrArticles = array();
        $intCounter = 0;
        $intActive = 0;
        $intTime = time();

        // get all news items who are in the configured archives
        $objArticles = $this->Database->prepare("
            SELECT
                news.id,
                news.alias,
                news.headline
            FROM
                tl_news AS news
            LEFT JOIN
                tl_content AS content ON(content.ptable = 'tl_news' AND news.id = content.pid)
            WHERE
                news.pid IN(" . implode(',', $this->news_archives) . ")
                " . (!BE_USER_LOGGED_IN ? " AND (news.start = '' OR news.start < ?) AND (news.stop = '' OR news.stop > ?) AND news.published = 1" : "") . "
            GROUP BY
                news.id
            ORDER BY
                news.date DESC
        ")->execute($intTime, $intTime);

        while($objArticles->next())
        {
            // +1 counter
            $intCounter++;

            // get alias
            $strAlias = (!$GLOBALS['TL_CONFIG']['disableAlias'] && $objArticles->alias != '') ? $objArticles->alias : $objArticles->id;

            // add to articles array
            $arrArticle = array
            (
                'isActive' => $this->item == $strAlias ? true : false,
                'href' => ampersand($this->generateFrontendUrl($objPage->row(), ((isset($GLOBALS['TL_CONFIG']['useAutoItem']) && $GLOBALS['TL_CONFIG']['useAutoItem']) ?  '/' : '/items/') . $strAlias)),
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
        $this->Template->total = sprintf($GLOBALS['TL_LANG']['MSC']['totalNews'], $intActive, $intCounter);

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

            // assign start at if bigger than one
            if($intStartAt > 1)
            {
                $arrStartAt = $arrAllArticles[$intStartAt-1];
                $arrStartAt['link'] = $GLOBALS['TL_LANG']['MSC']['points'];
                $this->Template->startat = $arrStartAt;
            }

            // assign stop at if its smaller the count
            if($intStopAt < $intCounter)
            {
                $arrStopAt = $arrAllArticles[$intStopAt+1];
                $arrStopAt['link'] = $GLOBALS['TL_LANG']['MSC']['points'];
                $this->Template->stopat = $arrStopAt;
            }

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
        }
        else
        {
            // assign articles
            $this->Template->articles = $arrAllArticles;
        }
    }
}

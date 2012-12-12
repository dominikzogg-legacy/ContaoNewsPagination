-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

--
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
  `addNewspagination` char(1) NOT NULL default '',
  `news_paginationCount` smallint(5) unsigned NOT NULL default '0',
  `news_paginationShowtitle` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
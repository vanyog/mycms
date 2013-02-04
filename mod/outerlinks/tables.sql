CREATE TABLE IF NOT EXISTS `outer_links` (
  `ID` int(11) NOT NULL auto_increment,
  `place` int(11) NOT NULL default '0',
  `date_time_1` datetime NOT NULL default '0000-00-00 00:00:00',
  `link` varchar(255) default NULL,
  `Title` text,
  `up` int(11) default NULL,
  `clicked` int(11) NOT NULL default '0',
  `evtime` float NOT NULL default '0',
  `private` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `link` (`link`),
  KEY `date_time_0` (`date_time_1`),
  KEY `clicked` (`clicked`),
  KEY `place` (`place`),
  FULLTEXT KEY `Title` (`Title`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 PACK_KEYS=0 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('outerlinks_categories',NOW(),NOW(),'bg','категории'),
('outerlinks_find',NOW(),NOW(),'bg','Търсене'),
('outerlinks_home',NOW(),NOW(),'bg','Начало'),
('outerlinks_in',NOW(),NOW(),'bg','в'),
('outerlinks_intitles',NOW(),NOW(),'bg','надписите'),
('outerlinks_inurls',NOW(),NOW(),'bg','адресите'),
('outerlinks_searchin',NOW(),NOW(),'bg','Търсене в:'),
('outerlinks_totalcount',NOW(),NOW(),'bg','Общ брой');
CREATE TABLE IF NOT EXISTS `outer_links` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `place` int(11) NOT NULL DEFAULT '0',
  `date_time_1` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link` varchar(255) DEFAULT '',
  `Title` text,
  `Comment` text NOT NULL,
  `up` int(11) DEFAULT '0',
  `clicked` int(11) NOT NULL DEFAULT '0',
  `evtime` float NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `link` (`link`),
  KEY `date_time_0` (`date_time_1`),
  KEY `clicked` (`clicked`),
  KEY `place` (`place`),
  FULLTEXT KEY `Title` (`Title`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 PACK_KEYS=0 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`no;ink`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('outerlinks_categories',0,NOW(),NOW(),'bg','категории'),
('outerlinks_find',1,NOW(),NOW(),'bg','Търсене'),
('outerlinks_found',0,NOW(),NOW(),'bg','намерени връзки'),
('outerlinks_home',0,NOW(),NOW(),'bg','Начало'),
('outerlinks_in',0,NOW(),NOW(),'bg','в'),
('outerlinks_intitles',0,NOW(),NOW(),'bg','надписите'),
('outerlinks_inurls',0,NOW(),NOW(),'bg','адресите'),
('outerlinks_searchin',0,NOW(),NOW(),'bg','Търсене в:'),
('outerlinks_totalcount',0,NOW(),NOW(),'bg','Общ брой');
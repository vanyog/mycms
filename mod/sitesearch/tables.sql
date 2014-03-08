CREATE TABLE IF NOT EXISTS `sitesearch_words` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `word` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `IP` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `date_time_1` (`date_time_1`),
  KEY `date_time_2` (`date_time_2`),
  KEY `word` (`word`),
  KEY `count` (`count`),
  KEY `IP` (`IP`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`nolink`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('sitesearch_clear',1,NOW(),NOW(),'bg','Почистване'),
('sitesearch_count',1,NOW(),NOW(),'bg','Намерени страници'),
('sitesearch_count',1,NOW(),NOW(),'en','Pages found'),
('sitesearch_last',1,NOW(),NOW(),'bg','Последен резултат'),
('sitesearch_notext',1,NOW(),NOW(),'bg','Не е въведен текст за търсене.'),
('sitesearch_notext',1,NOW(),NOW(),'en','Missing text to search for.'),
('sitesearch_notfound',1,NOW(),NOW(),'bg','Не е намерено срещане на думата/думите:'),
('sitesearch_notfound',0,NOW(),NOW(),'en','Not found occurrence of the word / words: '),
('sitesearch_searchfor',0,NOW(),NOW(),'bg','Резултат от търсене на думата/думите'),
('sitesearch_searchfor',1,NOW(),NOW(),'en','Result of search for the word / words'),
('sitesearch_submit',1,NOW(),NOW(),'bg','Търсене в сайта'),
('sitesearch_submit',1,NOW(),NOW(),'en','Search the site');
CREATE TABLE IF NOT EXISTS `user_agents` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `agent` varchar(255) CHARACTER SET ascii NOT NULL,
  `IP` varchar(15) CHARACTER SET ascii NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `type` varchar(10) CHARACTER SET ascii DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `agent` (`agent`),
  KEY `type` (`type`),
  KEY `count` (`count`),
  KEY `date_time_1` (`date_time_1`,`date_time_2`)
) ENGINE=MyISAM  DEFAULT CHARSET=armscii8 AUTO_INCREMENT=1 ;
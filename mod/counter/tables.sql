CREATE TABLE IF NOT EXISTS `mod_counter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(15) NOT NULL,
  `date_time` datetime NOT NULL,
  `url` varchar(100) CHARACTER SET utf8 NOT NULL,
  `page` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `IP` (`IP`),
  KEY `date_time` (`date_time`),
  KEY `url` (`url`),
  KEY `page` (`page`),
  KEY `referrer` (`referrer`),
  KEY `agent` (`agent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

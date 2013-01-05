CREATE TABLE IF NOT EXISTS `mod_counter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) CHARACTER SET utf8 NOT NULL,
  `page` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `IP` varchar(15) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `url` (`url`),
  KEY `page` (`page`),
  KEY `referrer` (`referrer`),
  KEY `agent` (`agent`),
  KEY `date_time` (`date_time`),
  KEY `IP` (`IP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

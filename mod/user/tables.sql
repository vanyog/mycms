CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date_time_0` datetime NOT NULL,
  `date_time_1` datetime NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `password` (`password`),
  KEY `date_time_0` (`date_time_0`),
  KEY `date_time_1` (`date_time_1`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('user_login',NOW(),NOW(),'bg','Влизане в системата'),
('user_login_button',NOW(),NOW(),'bg','Влизане'),
('user_password',NOW(),NOW(),'bg','Парола'),
('user_username',NOW(),NOW(),'bg','Потребителско име');
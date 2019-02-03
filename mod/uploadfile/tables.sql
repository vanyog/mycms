CREATE TABLE IF NOT EXISTS `files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `date_time_3` datetime NOT NULL,
  `date_time_4` datetime NOT NULL,
  `filename` varchar(255) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `pid` (`pid`),
  KEY `name` (`name`),
  KEY `filename` (`filename`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`nolink`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('uploadfile_confdel',1,NOW(),NOW(),'bg','Да се изтрие ли файл '),
('uploadfile_file',1,NOW(),NOW(),'bg','Файл за качване:'),
('uploadfile_fileexists',1,NOW(),NOW(),'bg','На сървъра вече има файл със същото име, който вероятно се използва на друга страница. Преименувайте файла и опитайте отново.'),
('uploadfile_fileinuse',1,NOW(),NOW(),'bg','Файл със същото име се използва на друго място на сайта. Преименувайте файла и опитайте отново.'),
('uploadfile_idnotexists',1,NOW(),NOW(),'bg','Не съществуващ номер на файл.'),
('uploadfile_linktext',1,NOW(),NOW(),'bg','Текст на хипервръзката към файла:'),
('uploadfile_nofile',1,NOW(),NOW(),'bg','Няма качен файл'),
('uploadfile_submit',1,NOW(),NOW(),'bg','Качване'),
('uploadfile_timehide',1,NOW(),NOW(),'bg','Дата на скриване:'),
('uploadfile_timeshow',1,NOW(),NOW(),'bg','Дата на показване:'),
('uploadfile_upladpagetitle',1,NOW(),NOW(),'bg','Качване на файл');
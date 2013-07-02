CREATE TABLE IF NOT EXISTS `files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `pid` (`pid`),
  KEY `name` (`name`),
  KEY `filename` (`filename`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('uploadfile_file',NOW(),NOW(),'bg','Файл за качване:'),
('uploadfile_fileinuse',NOW(),NOW(),'bg','Файл със същото име се използва на друго място на сайта. Преименувайте файла и опитайте отново.'),
('uploadfile_linktext',NOW(),NOW(),'bg','Текст на хипервръзката към файла:'),
('uploadfile_nofile',NOW(),NOW(),'bg','Няма качен файл'),
('uploadfile_submit',NOW(),NOW(),'bg','Качване'),
('uploadfile_upladpagetitle',NOW(),NOW(),'bg','Качване на файл'),
('uploadfile_idnotexists',NOW(),NOW(),'bg','Не съществуващ номер на файл.'),
('uploadfile_confdel',NOW(),NOW(),'bg','Да се изтрие ли файл '),
('uploadfile_fileexists',NOW(),NOW(),'bg','На сървъра вече има файл със същото име, който вероятно се използва на друга страница. Преименувайте файла и опитайте отново.');
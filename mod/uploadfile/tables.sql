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
('uploadfile_confdel',1,NOW(),NOW(),'bg','�� �� ������ �� ���� '),
('uploadfile_file',1,NOW(),NOW(),'bg','���� �� �������:'),
('uploadfile_fileexists',1,NOW(),NOW(),'bg','�� ������� ���� ��� ���� ��� ������ ���, ����� �������� �� �������� �� ����� ��������. ������������� ����� � �������� ������.'),
('uploadfile_fileinuse',1,NOW(),NOW(),'bg','���� ��� ������ ��� �� �������� �� ����� ����� �� �����. ������������� ����� � �������� ������.'),
('uploadfile_idnotexists',1,NOW(),NOW(),'bg','�� ����������� ����� �� ����.'),
('uploadfile_linktext',1,NOW(),NOW(),'bg','����� �� ������������� ��� �����:'),
('uploadfile_nofile',1,NOW(),NOW(),'bg','���� ����� ����'),
('uploadfile_submit',1,NOW(),NOW(),'bg','�������'),
('uploadfile_timehide',1,NOW(),NOW(),'bg','���� �� ��������:'),
('uploadfile_timeshow',1,NOW(),NOW(),'bg','���� �� ���������:'),
('uploadfile_upladpagetitle',1,NOW(),NOW(),'bg','������� �� ����');
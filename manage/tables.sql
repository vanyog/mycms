CREATE TABLE IF NOT EXISTS `content` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `nolink` tinyint(1) NOT NULL DEFAULT '0',
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `language` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT 'bg',
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `date_time_1` (`date_time_1`),
  KEY `date_time_2` (`date_time_2`),
  KEY `name` (`name`),
  KEY `language` (`language`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`, `nolink`, `date_time_1`, `date_time_2`, `language`, `text`) VALUES
('home_page_title', 0, NOW(), NOW(), 'bg', 'Начална страница'),
('home_page_content', 0, NOW(), NOW(), 'bg', '<p>Текст на страницата.</p>'),
('error_404_title', 0, NOW(), NOW(), 'bg', 'Грешен номер на страница'),
('error_404_content', 0, NOW(), NOW(), 'bg', '<p>На сайта няма страница с такъв номер.</p>'),
('p1_link', NOW(), 0, NOW(), 'bg', 'Начало'),
('saveData', NOW(), 1, NOW(), 'bg', 'Съхраняване на данните'),
('dataSaved', NOW(), 1, NOW(), 'bg', 'Данните бяха съхранени.'),
('month_names', NOW(), 1, NOW(), 'bg', '$month = array(\'\',\'януари\',\'февруари\',\'март\',\'април\',\'май\',\'юни\',\'юли\',\'август\',\'септември\',\'октомври\',\'ноември\',\'декември\');');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `place` int(11) NOT NULL,
  `group` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `menu_items` (`ID`, `place`, `group`, `name`, `link`) VALUES
(1, 10, 1, 'p1_link', '1');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_tree` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `index_page` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `menu_tree` (`ID`, `group`, `parent`, `index_page`) VALUES
(1, 1, 0, 1);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `options` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `options` (`name`, `value`) VALUES
('languages', '$languages = array(''bg'' => ''Български'' /*, ''en'' => ''English''*/ );'),
('default_language', 'bg'),
('admin_path', 'manage'),
('adm_name', 'admin'),
('adm_value', 'on'),
('edit_name', 'edit'),
('edit_value', 'on'),
('host_web', 'mysite.org'),
('host_local', 'localhost'),
('phpmyadmin_web', 'http://mysite.org/phpmyadmin'),
('phpmyadmin_local', 'http://localhost/phpmyadmin'),
('mod_path', '_mod'),
('cache_time', '10');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `menu_group` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` varchar(50) NOT NULL,
  `template_id` int(11) NOT NULL DEFAULT '1',
  `hidden` tinyint(1) NOT NULL DEFAULT '1',
  `options` varchar(50) DEFAULT NULL,
  `dcount` int(11) NOT NULL DEFAULT '0',
  `tcount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `pages` (`ID`, `menu_group`, `title`, `content`, `template_id`, `hidden`, `options`) VALUES
(1, 1, 'home_page_title', 'home_page_content', 1, 1, '');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `scripts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `script` text NOT NULL,
  `coment` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `scripts` (`name`, `script`, `coment`) VALUES
('ADMINMENU', 'include_once("f_adm_links.php"); $tx = adm_links();', 'Показва линкове за администриране на сайта'),
('PAGETITLE', '$tx = translate($page_data[''title'']);', 'Заглавие на страницата, показвано между таговете <h1></h1>.'),
('CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[''content'']);', 'Показване съдържанието на страницата и ли надпис със зададено име.'),
('MENU', 'include_once(''f_menu.php'');\r\n$tx = menu($page_data[''menu_group'']);', 'Показване на група от хипервръзки (меню)'),
('BODYADDS', '$tx = $body_adds;', 'Вмъква добавките към <body> тага'),
('PAGEHEADER', '$tx = $page_header;', 'Вмъква добавките към хедъра на страницата'),
('HEADTITLE', '$tx = translate($page_data[''title''],false);', 'Заглавие на страницата, без линк за редактиране, показвано между таговете <title></title>.'),
('LANGUAGEFLAGS', '$tx = flags();', 'Показва флагчета за смяна на езика');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `template` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `templates` (`ID`, `parent`, `template`, `comment`) VALUES
(1, 0, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">\r\n\r\n<head>\r\n  <title><!--$$_HEADTITLE_$$--></title>\r\n  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">\r\n  <link href="style.css" rel="stylesheet" type="text/css">\r\n<!--$$_PAGEHEADER_$$-->\r\n</head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<!--$$_MENU_$$-->\r\n\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n\r\n<p id="powered_by">Направено с <a href="https://github.com/vanyog/mycms/wiki" target="_blank">MyCMS</a> <!--$$_PAGESTAT_$$--></p>\r\n</body>\r\n</html>\r\n\r\n','Шаблон по подразбиране');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `visit_history` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `page_id` (`page_id`),
  KEY `date` (`date`),
  KEY `count` (`count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `filters` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `filters` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `page_cache` (
  `page_ID` int(11) NOT NULL,
  `date_time_1` datetime NOT NULL,
  `text` mediumtext COLLATE utf8_bin NOT NULL,
  UNIQUE KEY `page_ID` (`page_ID`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
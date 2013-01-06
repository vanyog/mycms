-- --------------------------------------------------------

--
-- Структура на таблица `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `language` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT 'bg',
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `date_time_1` (`date_time_1`),
  KEY `date_time_2` (`date_time_2`),
  KEY `name` (`name`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Ссхема на данните от таблица `content`
--

INSERT INTO `content` (`ID`, `name`, `date_time_1`, `date_time_2`, `language`, `text`) VALUES
(1, 'home_page_title', '2011-02-01 15:31:51', '2011-02-01 15:32:10', 'bg', 'Home page'),
(2, 'home_page_content', '2011-02-01 15:32:24', '2011-02-01 15:32:59', 'bg', '<p>Some Text.</p>');

-- --------------------------------------------------------

--
-- Структура на таблица `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `place` int(11) NOT NULL,
  `group` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `group` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура на таблица `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

--
-- Ссхема на данните от таблица `n_options`
--

INSERT INTO `n_options` (`ID`, `name`, `value`) VALUES
(1, 'languages', '$languages = array(''bg'' => ''���������'' /*, ''en'' => ''English''*/ );'),
(2, 'default_language', 'bg'),
(4, 'host_web', 'mysite.com'),
(5, 'host_local', 'mysite'),
(6, 'phpmyadmin_web', 'http://mysite.com/phpmyadmin'),
(7, 'phpmyadmin_local', 'http://localhost/phpmyadmin');

-- --------------------------------------------------------

--
-- Структура на таблица `pages`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Ссхема на данните от таблица `pages`
--

INSERT INTO `pages` (`ID`, `menu_group`, `title`, `content`, `template_id`, `hidden`, `options`, `dcount`, `tcount`) VALUES
(1, 0, 'home_page_title', 'home_page_content', 1, 1, '', 1, 2);

-- --------------------------------------------------------

--
-- Структура на таблица `scripts`
--

CREATE TABLE IF NOT EXISTS `scripts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `script` text NOT NULL,
  `coment` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Ссхема на данните от таблица `scripts`
--

INSERT INTO `scripts` (`ID`, `name`, `script`, `coment`) VALUES
(1, 'ADMINMENU', 'include_once("f_adm_links.php"); $tx = adm_links();', 'Показва линкове за администриране на сайта'),
(2, 'PAGETITLE', '$tx = translate($page_data[''title'']);', 'Заглавие на страницата'),
(3, 'CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[''content'']);', 'Показване съдържанието на страницата и ли надпис със зададено име.'),
(4, 'MENU', 'include_once(''f_menu.php'');\r\n$tx = menu($page_data[''menu_group'']);', 'Показване на група от хипервръзки (меню)'),
(5, 'BODYADDS', '$tx = $body_adds;', 'Вмъква добавките към <body> тага'),
(6, 'PAGEHEADER', '$tx = $page_header;', 'Вмъква добавките към хедъра на страницата');

-- --------------------------------------------------------

--
-- Структура на таблица `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Ссхема на данните от таблица `templates`
--

INSERT INTO `templates` (`ID`, `parent`, `template`) VALUES
(1, 0, '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">\r\n\r\n<head>\r\n  <title><!--$$_PAGETITLE_$$--></title>\r\n  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">\r\n  <link href="/_new/style.css" rel="stylesheet" type="text/css">\r\n<!--$$_PAGEHEADER_$$-->\r\n</head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<!--$$_MENU_$$-->\r\n\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n\r\n</body>\r\n</html>\r\n\r\n');

-- --------------------------------------------------------

--
-- Структура на таблица `visit_history`
--

CREATE TABLE IF NOT EXISTS `visit_history` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `page_id` (`page_id`),
  KEY `date` (`date`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


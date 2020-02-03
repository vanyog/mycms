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
('home_page_title', 0, 'NOW()', 'NOW()', 'bg', 'Начална страница'),
('home_page_title', 0, 'NOW()', 'NOW()', 'en', 'Home Page'),
('home_page_content', 0, 'NOW()', 'NOW()', 'bg', '<p>Текст на страницата.</p>'),
('home_page_content', 0, 'NOW()', 'NOW()', 'en', '<p>Content of the Homa Page.</p>'),
('error_404_title', 0, 'NOW()', 'NOW()', 'bg', 'Грешен номер на страница'),
('error_404_title', 0, 'NOW()', 'NOW()', 'en', 'Incorrect page number'),
('error_404_content', 0, 'NOW()', 'NOW()', 'bg', '<p>На сайта няма страница с такъв номер.</p>'),
('error_404_content', 0, 'NOW()', 'NOW()', 'en', '<p>Page is not found.</p>'),
('p1_link', 127, 'NOW()', 'NOW()', 'bg', 'Начало'),
('menu_start', 127, 'NOW()', 'NOW()', 'bg', ''),
('p1_link', 127, 'NOW()', 'NOW()', 'en', 'Home'),
('saveData', 127, 'NOW()', 'NOW()', 'bg', 'Съхраняване на данните'),
('saveData', 127, 'NOW()', 'NOW()', 'en', 'Save gada'),
('dataSaved', 127, 'NOW()', 'NOW()', 'bg', 'Данните бяха съхранени.'),
('dataSaved', 127, 'NOW()', 'NOW()', 'en', 'Data were saved.'),
('month_names', 127, 'NOW()', 'NOW()', 'bg', '$month = array("","януари","февруари","март","април","май","юни","юли","август","септември","октомври","ноември","декември");'),
('month_names', 127, 'NOW()', 'NOW()', 'en', '$month = array("","January","February","March","April","May","June","July","August","September","October","November","December");'),
('user_address', 1, 'NOW()', 'NOW()', 'bg', 'Адрес:'),
('user_address', 1, 'NOW()', 'NOW()', 'en', 'Address:'),
('user_backto', 1, 'NOW()', 'NOW()', 'bg', 'Връщане към:'),
('user_backto', 1, 'NOW()', 'NOW()', 'en', 'Go to:'),
('user_country', 1, 'NOW()', 'NOW()', 'bg', 'Държава:'),
('user_country', 1, 'NOW()', 'NOW()', 'en', 'Country:'),
('user_delete', 1, 'NOW()', 'NOW()', 'bg', 'Изтриване на потребителя'),
('user_delete', 1, 'NOW()', 'NOW()', 'en', 'Delete User'),
('user_email', 1, 'NOW()', 'NOW()', 'bg', 'Имейл:'),
('user_email', 1, 'NOW()', 'NOW()', 'en', 'E-mail:'),
('user_enter', 1, 'NOW()', 'NOW()', 'bg', 'Вход'),
('user_enter', 0, 'NOW()', 'NOW()', 'en', 'Login'),
('user_firstname', 1, 'NOW()', 'NOW()', 'bg', 'Име:'),
('user_firstname', 1, 'NOW()', 'NOW()', 'en', 'Name:'),
('user_firstuser', 1, 'NOW()', 'NOW()', 'bg', '<p>На сайта все още няма регистрирани потребители. Сега ше регистрирате първия потребител.</p>'),
('user_firstuser', 0, 'NOW()', 'NOW()', 'en', '<p>Site has not yet registered users. Now we register the first user.</p>'),
('user_homеpage', 1, 'NOW()', 'NOW()', 'bg', 'Началната страница'),
('user_homеpage', 1, 'NOW()', 'NOW()', 'en', 'Home page'),
('user_institution', 1, 'NOW()', 'NOW()', 'bg', 'Месторабота:'),
('user_institution', 1, 'NOW()', 'NOW()', 'en', 'Institution:'),
('user_lastpage', 1, 'NOW()', 'NOW()', 'bg', 'Предишната страница'),
('user_lastpage', 1, 'NOW()', 'NOW()', 'en', 'Previous Page'),
('user_logaut', 1, 'NOW()', 'NOW()', 'bg', 'Изход'),
('user_logaut', 1, 'NOW()', 'NOW()', 'en', 'Logout'),
('user_login', 1, 'NOW()', 'NOW()', 'bg', 'Влизане в системата'),
('user_login', 1, 'NOW()', 'NOW()', 'en', 'User login'),
('user_login_button', 1, 'NOW()', 'NOW()', 'bg', 'Влизане'),
('user_login_button', 1, 'NOW()', 'NOW()', 'en', 'Log in'),
('user_logoutcontent', 1, 'NOW()', 'NOW()', 'bg', '<p>Вие успешно излязохте от системата</p>'),
('user_logoutcontent', 1, 'NOW()', 'NOW()', 'en', '<p>You have successfully logged out of the system</p>'),
('user_logouttitle', 1, 'NOW()', 'NOW()', 'bg', 'Изход от системата'),
('user_logouttitle', 1, 'NOW()', 'NOW()', 'en', 'Log out page'),
('user_newreg', 1, 'NOW()', 'NOW()', 'bg', 'Нова регистрация'),
('user_newreg', 0, 'NOW()', 'NOW()', 'en', 'New registration'),
('user_password', 1, 'NOW()', 'NOW()', 'bg', 'Парола:'),
('user_password', 1, 'NOW()', 'NOW()', 'en', 'Password:'),
('user_passwordchanged', 1, 'NOW()', 'NOW()', 'bg', 'Паролата беше сменена. При следващото влизане използвайте новата парола.'),
('user_passwordchanged', 0, 'NOW()', 'NOW()', 'en', 'The password was changed. The next time use the new password.'),
('user_passwordconfirm', 1, 'NOW()', 'NOW()', 'bg', 'Повторение на паролата:'),
('user_passwordconfirm', 1, 'NOW()', 'NOW()', 'en', 'Repeat password:'),
('user_passwordinvalid', 1, 'NOW()', 'NOW()', 'bg', 'Паролата и нейното повторение не съвпадаха. Паролата не е променена.'),
('user_passwordinvalid', 0, 'NOW()', 'NOW()', 'en', 'Password and its repetition does not match. Password is not changed.'),
('user_savenew', 1, 'NOW()', 'NOW()', 'bg', 'Създаване на нов потребител'),
('user_savenew', 1, 'NOW()', 'NOW()', 'en', 'Create a new user'),
('user_secondname', 1, 'NOW()', 'NOW()', 'bg', 'Презиме:'),
('user_secondname', 1, 'NOW()', 'NOW()', 'en', 'Surname:'),
('user_telephone', 1, 'NOW()', 'NOW()', 'bg', 'Телефон:'),
('user_telephone', 1, 'NOW()', 'NOW()', 'en', 'Phone:'),
('user_thirdname', 1, 'NOW()', 'NOW()', 'bg', 'Фамилия:'),
('user_thirdname', 1, 'NOW()', 'NOW()', 'en', 'Family:'),
('user_username', 1, 'NOW()', 'NOW()', 'bg', 'Потребителско име:'),
('user_username', 1, 'NOW()', 'NOW()', 'en', 'Username:'),
('usermenu_addtomenu', 1, 'NOW()', 'NOW()', 'bg', 'Линк в менюто на текущата страница:'),
('usermenu_addtomenu', 1, 'NOW()', 'NOW()', 'en', 'Create a link in the current page:'),
('usermenu_back', 1, 'NOW()', 'NOW()', 'bg', 'Връщане обратно'),
('usermenu_back', 1, 'NOW()', 'NOW()', 'en', 'Go back'),
('usermenu_cantdelindex', 1, 'NOW()', 'NOW()', 'bg', 'Не можете да изтриете главната страница на раздел, докато в този раздел има други страници.'),
('usermenu_cantdelindex', 1, 'NOW()', 'NOW()', 'en', 'You can not delete the main page of a section, while this section has pages.'),
('usermenu_confirdeleting', 1, 'NOW()', 'NOW()', 'bg', 'Изтриването на страница ще направи невалидни всички хипервръзки, които сочат към нея. Наистина ли искате да изтриете тази страница?'),
('usermenu_confirdeleting', 1, 'NOW()', 'NOW()', 'en', 'Deleting a page will break all links to it. Do you really want to delete this page?'),
('usermenu_createnewpage', 1, 'NOW()', 'NOW()', 'bg', 'Създаване на нова страница'),
('usermenu_createnewpage', 1, 'NOW()', 'NOW()', 'en', 'Create a new page'),
('usermenu_edittext', 1, 'NOW()', 'NOW()', 'bg', 'Редактиране на текст'),
('usermenu_edittext', 1, 'NOW()', 'NOW()', 'en', 'Editing Text'),
('usermenu_language', 1, 'NOW()', 'NOW()', 'bg', 'Език:'),
('usermenu_language', 1, 'NOW()', 'NOW()', 'en', 'Language:'),
('usermenu_linktext', 1, 'NOW()', 'NOW()', 'bg', 'Текст на линка в менюто:'),
('usermenu_linktext', 1, 'NOW()', 'NOW()', 'en', 'Text on the link to page:'),
('usermenu_menupos', 1, 'NOW()', 'NOW()', 'bg', 'Позиция в менюто:'),
('usermenu_menupos', 1, 'NOW()', 'NOW()', 'en', 'Position in the menu:'),
('usermenu_newmenu', 1, 'NOW()', 'NOW()', 'bg', 'Нов раздел:'),
('usermenu_newmenu', 1, 'NOW()', 'NOW()', 'en', 'New section:'),
('usermenu_newpagecontent', 1, 'NOW()', 'NOW()', 'bg', 'Съдържание на страницата:'),
('usermenu_newpagecontent', 1, 'NOW()', 'NOW()', 'en', 'Page Content:'),
('usermenu_newpagesubmit', 1, 'NOW()', 'NOW()', 'bg', 'Създаване и отваряне на страницата'),
('usermenu_newpagesubmit', 1, 'NOW()', 'NOW()', 'en', 'Create and open the page'),
('usermenu_newpagetitle', 1, 'NOW()', 'NOW()', 'bg', 'Заглавие:'),
('usermenu_newpagetitle', 1, 'NOW()', 'NOW()', 'en', 'Heading:'),
('usermenu_texttoedit', 1, 'NOW()', 'NOW()', 'bg', 'Текст:'),
('usermenu_texttoedit', 1, 'NOW()', 'NOW()', 'en', 'Text:'),
('p2_title', 0, 'NOW()', 'NOW()', 'bg', 'Страница за администриране'),
('p2_content', 0, 'NOW()', 'NOW()', 'bg', '<!--$$_USER_edit_$$-->');
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
('languages', '$languages = array(\'bg\' => \'Български\' /*, \'en\' => \'English\'*/ );'),
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
(1, 1, 'home_page_title', 'home_page_content', 1, 0, ''),
(2, 1, 'p2_title', 'p2_content', 1, 1, '');
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
('ADMINMENU', 'include_once($idir."lib/f_adm_links.php"); $tx = adm_links();', 'Показва линкове за администриране на сайта'),
('PAGETITLE', '$tx = translate($page_data[\'title\']);', 'Заглавие на страницата, показвано между таговете <h1></h1>.'),
('CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[\'content\']);', 'Показване съдържанието на страницата и ли надпис със зададено име.'),
('MENU', 'include_once($idir."lib/f_menu.php");\r\n$tx = menu($page_data[\'menu_group\']);', 'Показване на група от хипервръзки (меню)'),
('BODYADDS', '$tx = $body_adds;', 'Вмъква добавките към <body> тага'),
('PAGEHEADER', '$tx = $page_header;', 'Вмъква добавките към хедъра на страницата'),
('HEADTITLE', '$tx = translate($page_data[\'title\'],false);', 'Заглавие на страницата, без линк за редактиране, показвано между таговете <title></title>.'),
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
(1, 0, '<!DOCTYPE HTML>\r\n<html lang="<!--$$_VARIABLE_default_language_$$-->">\r\n<head>\r\n  <title><!--$$_HEADTITLE_$$--></title>\r\n  <meta http-equiv="Content-Type" content="text/html; charset=<!--$$_VARIABLE_site_encoding_$$-->">\r\n  <meta name=viewport content="width=device-width, initial-scale=1">\r\n  <link href="style.css" rel="stylesheet" type="text/css">\r\n<!--$$_PAGEHEADER_$$--></head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<!--$$_MENU_$$-->\r\n\r\n<div id="page_content">\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n\r\n<p id="powered_by">Направено с <a href="https://github.com/vanyog/mycms/wiki" target="_blank">MyCMS</a> <!--$$_PAGESTAT_$$--></p>\r\n</div>\r\n\r\n<!--$$_USERMENU_/index.php?pid=2&amp;user2=logout_$$-->\r\n</body>\r\n</html>\r\n\r\n', 'Шаблон по подразбиране');
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
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_time_0` datetime NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `newpass` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `code` varchar(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `firstname` varchar(100) CHARACTER SET utf8 NOT NULL,
  `secondname` varchar(100) CHARACTER SET utf8 NOT NULL,
  `thirdname` varchar(100) CHARACTER SET utf8 NOT NULL,
  `country` varchar(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `institution` varchar(255) CHARACTER SET utf8 NOT NULL,
  `address` text CHARACTER SET utf8 NOT NULL,
  `telephone` varchar(20) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `IP` varchar(15) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
INSERT INTO `users` (`ID`, `type`, `date_time_0`, `date_time_1`, `date_time_2`, `username`, `password`, `newpass`, `email`, `code`, `firstname`, `secondname`, `thirdname`, `country`, `institution`, `address`, `telephone`, `IP`) VALUES
(1, '', '2016-01-24 10:50:28', '2016-01-24 10:50:28', '2016-01-24 11:01:39', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', '', '', '', '', '', '', '', '', '', '', '127.0.0.1');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `permissions` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('all','page','menu','module','record') COLLATE cp1251_bulgarian_ci NOT NULL DEFAULT 'page',
  `object` varchar(20) COLLATE cp1251_bulgarian_ci NOT NULL,
  `yes_no` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251 COLLATE=cp1251_bulgarian_ci;
-- --------------------------------------------------------
INSERT INTO `permissions` (`ID`, `user_id`, `type`, `object`, `yes_no`) VALUES
(1, 1, 'all', '', 1);

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
('home_page_title', 0, 'NOW()', 'NOW()', 'bg', '������� ��������'),
('home_page_title', 0, 'NOW()', 'NOW()', 'en', 'Home Page'),
('home_page_content', 0, 'NOW()', 'NOW()', 'bg', '<p>����� �� ����������.</p>'),
('home_page_content', 0, 'NOW()', 'NOW()', 'en', '<p>Content of the Homa Page.</p>'),
('error_404_title', 0, 'NOW()', 'NOW()', 'bg', '������ ����� �� ��������'),
('error_404_title', 0, 'NOW()', 'NOW()', 'en', 'Incorrect page number'),
('error_404_content', 0, 'NOW()', 'NOW()', 'bg', '<p>�� ����� ���� �������� � ����� �����.</p>'),
('error_404_content', 0, 'NOW()', 'NOW()', 'en', '<p>Page is not found.</p>'),
('p1_link', 127, 'NOW()', 'NOW()', 'bg', '������'),
('menu_start', 127, 'NOW()', 'NOW()', 'bg', ''),
('p1_link', 127, 'NOW()', 'NOW()', 'en', 'Home'),
('saveData', 127, 'NOW()', 'NOW()', 'bg', '����������� �� �������'),
('saveData', 127, 'NOW()', 'NOW()', 'en', 'Save gada'),
('dataSaved', 127, 'NOW()', 'NOW()', 'bg', '������� ���� ���������.'),
('dataSaved', 127, 'NOW()', 'NOW()', 'en', 'Data were saved.'),
('month_names', 127, 'NOW()', 'NOW()', 'bg', '$month = array("","������","��������","����","�����","���","���","���","������","���������","��������","�������","��������");'),
('month_names', 127, 'NOW()', 'NOW()', 'en', '$month = array("","January","February","March","April","May","June","July","August","September","October","November","December");'),
('user_address', 1, 'NOW()', 'NOW()', 'bg', '�����:'),
('user_address', 1, 'NOW()', 'NOW()', 'en', 'Address:'),
('user_backto', 1, 'NOW()', 'NOW()', 'bg', '������� ���:'),
('user_backto', 1, 'NOW()', 'NOW()', 'en', 'Go to:'),
('user_country', 1, 'NOW()', 'NOW()', 'bg', '�������:'),
('user_country', 1, 'NOW()', 'NOW()', 'en', 'Country:'),
('user_delete', 1, 'NOW()', 'NOW()', 'bg', '��������� �� �����������'),
('user_delete', 1, 'NOW()', 'NOW()', 'en', 'Delete User'),
('user_email', 1, 'NOW()', 'NOW()', 'bg', '�����:'),
('user_email', 1, 'NOW()', 'NOW()', 'en', 'E-mail:'),
('user_enter', 1, 'NOW()', 'NOW()', 'bg', '����'),
('user_enter', 0, 'NOW()', 'NOW()', 'en', 'Login'),
('user_firstname', 1, 'NOW()', 'NOW()', 'bg', '���:'),
('user_firstname', 1, 'NOW()', 'NOW()', 'en', 'Name:'),
('user_firstuser', 1, 'NOW()', 'NOW()', 'bg', '<p>�� ����� ��� ��� ���� ������������ �����������. ���� �� ������������ ������ ����������.</p>'),
('user_firstuser', 0, 'NOW()', 'NOW()', 'en', '<p>Site has not yet registered users. Now we register the first user.</p>'),
('user_hom�page', 1, 'NOW()', 'NOW()', 'bg', '��������� ��������'),
('user_hom�page', 1, 'NOW()', 'NOW()', 'en', 'Home page'),
('user_institution', 1, 'NOW()', 'NOW()', 'bg', '�����������:'),
('user_institution', 1, 'NOW()', 'NOW()', 'en', 'Institution:'),
('user_lastpage', 1, 'NOW()', 'NOW()', 'bg', '���������� ��������'),
('user_lastpage', 1, 'NOW()', 'NOW()', 'en', 'Previous Page'),
('user_logaut', 1, 'NOW()', 'NOW()', 'bg', '�����'),
('user_logaut', 1, 'NOW()', 'NOW()', 'en', 'Logout'),
('user_login', 1, 'NOW()', 'NOW()', 'bg', '������� � ���������'),
('user_login', 1, 'NOW()', 'NOW()', 'en', 'User login'),
('user_login_button', 1, 'NOW()', 'NOW()', 'bg', '�������'),
('user_login_button', 1, 'NOW()', 'NOW()', 'en', 'Log in'),
('user_logoutcontent', 1, 'NOW()', 'NOW()', 'bg', '<p>��� ������� ��������� �� ���������</p>'),
('user_logoutcontent', 1, 'NOW()', 'NOW()', 'en', '<p>You have successfully logged out of the system</p>'),
('user_logouttitle', 1, 'NOW()', 'NOW()', 'bg', '����� �� ���������'),
('user_logouttitle', 1, 'NOW()', 'NOW()', 'en', 'Log out page'),
('user_newreg', 1, 'NOW()', 'NOW()', 'bg', '���� �����������'),
('user_newreg', 0, 'NOW()', 'NOW()', 'en', 'New registration'),
('user_password', 1, 'NOW()', 'NOW()', 'bg', '������:'),
('user_password', 1, 'NOW()', 'NOW()', 'en', 'Password:'),
('user_passwordchanged', 1, 'NOW()', 'NOW()', 'bg', '�������� ���� �������. ��� ���������� ������� ����������� ������ ������.'),
('user_passwordchanged', 0, 'NOW()', 'NOW()', 'en', 'The password was changed. The next time use the new password.'),
('user_passwordconfirm', 1, 'NOW()', 'NOW()', 'bg', '���������� �� ��������:'),
('user_passwordconfirm', 1, 'NOW()', 'NOW()', 'en', 'Repeat password:'),
('user_passwordinvalid', 1, 'NOW()', 'NOW()', 'bg', '�������� � ������� ���������� �� ���������. �������� �� � ���������.'),
('user_passwordinvalid', 0, 'NOW()', 'NOW()', 'en', 'Password and its repetition does not match. Password is not changed.'),
('user_savenew', 1, 'NOW()', 'NOW()', 'bg', '��������� �� ��� ����������'),
('user_savenew', 1, 'NOW()', 'NOW()', 'en', 'Create a new user'),
('user_secondname', 1, 'NOW()', 'NOW()', 'bg', '�������:'),
('user_secondname', 1, 'NOW()', 'NOW()', 'en', 'Surname:'),
('user_telephone', 1, 'NOW()', 'NOW()', 'bg', '�������:'),
('user_telephone', 1, 'NOW()', 'NOW()', 'en', 'Phone:'),
('user_thirdname', 1, 'NOW()', 'NOW()', 'bg', '�������:'),
('user_thirdname', 1, 'NOW()', 'NOW()', 'en', 'Family:'),
('user_username', 1, 'NOW()', 'NOW()', 'bg', '������������� ���:'),
('user_username', 1, 'NOW()', 'NOW()', 'en', 'Username:'),
('usermenu_addtomenu', 1, 'NOW()', 'NOW()', 'bg', '���� � ������ �� �������� ��������:'),
('usermenu_addtomenu', 1, 'NOW()', 'NOW()', 'en', 'Create a link in the current page:'),
('usermenu_back', 1, 'NOW()', 'NOW()', 'bg', '������� �������'),
('usermenu_back', 1, 'NOW()', 'NOW()', 'en', 'Go back'),
('usermenu_cantdelindex', 1, 'NOW()', 'NOW()', 'bg', '�� ������ �� �������� �������� �������� �� ������, ������ � ���� ������ ��� ����� ��������.'),
('usermenu_cantdelindex', 1, 'NOW()', 'NOW()', 'en', 'You can not delete the main page of a section, while this section has pages.'),
('usermenu_confirdeleting', 1, 'NOW()', 'NOW()', 'bg', '����������� �� �������� �� ������� ��������� ������ �����������, ����� ����� ��� ���. �������� �� ������ �� �������� ���� ��������?'),
('usermenu_confirdeleting', 1, 'NOW()', 'NOW()', 'en', 'Deleting a page will break all links to it. Do you really want to delete this page?'),
('usermenu_createnewpage', 1, 'NOW()', 'NOW()', 'bg', '��������� �� ���� ��������'),
('usermenu_createnewpage', 1, 'NOW()', 'NOW()', 'en', 'Create a new page'),
('usermenu_edittext', 1, 'NOW()', 'NOW()', 'bg', '����������� �� �����'),
('usermenu_edittext', 1, 'NOW()', 'NOW()', 'en', 'Editing Text'),
('usermenu_language', 1, 'NOW()', 'NOW()', 'bg', '����:'),
('usermenu_language', 1, 'NOW()', 'NOW()', 'en', 'Language:'),
('usermenu_linktext', 1, 'NOW()', 'NOW()', 'bg', '����� �� ����� � ������:'),
('usermenu_linktext', 1, 'NOW()', 'NOW()', 'en', 'Text on the link to page:'),
('usermenu_menupos', 1, 'NOW()', 'NOW()', 'bg', '������� � ������:'),
('usermenu_menupos', 1, 'NOW()', 'NOW()', 'en', 'Position in the menu:'),
('usermenu_newmenu', 1, 'NOW()', 'NOW()', 'bg', '��� ������:'),
('usermenu_newmenu', 1, 'NOW()', 'NOW()', 'en', 'New section:'),
('usermenu_newpagecontent', 1, 'NOW()', 'NOW()', 'bg', '���������� �� ����������:'),
('usermenu_newpagecontent', 1, 'NOW()', 'NOW()', 'en', 'Page Content:'),
('usermenu_newpagesubmit', 1, 'NOW()', 'NOW()', 'bg', '��������� � �������� �� ����������'),
('usermenu_newpagesubmit', 1, 'NOW()', 'NOW()', 'en', 'Create and open the page'),
('usermenu_newpagetitle', 1, 'NOW()', 'NOW()', 'bg', '��������:'),
('usermenu_newpagetitle', 1, 'NOW()', 'NOW()', 'en', 'Heading:'),
('usermenu_texttoedit', 1, 'NOW()', 'NOW()', 'bg', '�����:'),
('usermenu_texttoedit', 1, 'NOW()', 'NOW()', 'en', 'Text:'),
('p2_title', 0, 'NOW()', 'NOW()', 'bg', '�������� �� ��������������'),
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
('languages', '$languages = array(\'bg\' => \'���������\' /*, \'en\' => \'English\'*/ );'),
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
('ADMINMENU', 'include_once($idir."lib/f_adm_links.php"); $tx = adm_links();', '������� ������� �� �������������� �� �����'),
('PAGETITLE', '$tx = translate($page_data[\'title\']);', '�������� �� ����������, ��������� ����� �������� <h1></h1>.'),
('CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[\'content\']);', '��������� ������������ �� ���������� � �� ������ ��� �������� ���.'),
('MENU', 'include_once($idir."lib/f_menu.php");\r\n$tx = menu($page_data[\'menu_group\']);', '��������� �� ����� �� ����������� (����)'),
('BODYADDS', '$tx = $body_adds;', '������ ��������� ��� <body> ����'),
('PAGEHEADER', '$tx = $page_header;', '������ ��������� ��� ������ �� ����������'),
('HEADTITLE', '$tx = translate($page_data[\'title\'],false);', '�������� �� ����������, ��� ���� �� �����������, ��������� ����� �������� <title></title>.'),
('LANGUAGEFLAGS', '$tx = flags();', '������� �������� �� ����� �� �����');
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
(1, 0, '<!DOCTYPE HTML>\r\n<html lang="<!--$$_VARIABLE_default_language_$$-->">\r\n<head>\r\n  <title><!--$$_HEADTITLE_$$--></title>\r\n  <meta http-equiv="Content-Type" content="text/html; charset=<!--$$_VARIABLE_site_encoding_$$-->">\r\n  <meta name=viewport content="width=device-width, initial-scale=1">\r\n  <link href="style.css" rel="stylesheet" type="text/css">\r\n<!--$$_PAGEHEADER_$$--></head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<!--$$_MENU_$$-->\r\n\r\n<div id="page_content">\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n\r\n<p id="powered_by">��������� � <a href="https://github.com/vanyog/mycms/wiki" target="_blank">MyCMS</a> <!--$$_PAGESTAT_$$--></p>\r\n</div>\r\n\r\n<!--$$_USERMENU_/index.php?pid=2&amp;user2=logout_$$-->\r\n</body>\r\n</html>\r\n\r\n', '������ �� ������������');
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

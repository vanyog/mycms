CREATE TABLE IF NOT EXISTS `permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('page','menu','module') COLLATE cp1251_bulgarian_ci NOT NULL DEFAULT 'page',
  `object` varchar(20) COLLATE cp1251_bulgarian_ci NOT NULL,
  `yes_no` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `object` (`object`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COLLATE=cp1251_bulgarian_ci AUTO_INCREMENT=1 ;
-- --------------------------------------------------------
INSERT INTO `content` (`name`,`nolink`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
('usermenu_confirdeleting',1,NOW(),NOW(),'bg','Изтриването на страница ще направи невалидни всички хипервръзки, които сочат към нея. Наистина ли искате да изтриете тази страница?'),
('usermenu_createnewpage',1,NOW(),NOW(),'bg','Създаване на нова страница'),
('usermenu_language',1,NOW(),NOW(),'bg','Език:'),
('usermenu_linktext',1,NOW(),NOW(),'bg','Текст на линка в менюто:'),
('usermenu_menupos',1,NOW(),NOW(),'bg','Позиция в менюто:'),
('usermenu_newpagecontent',1,NOW(),NOW(),'bg','Съдържание на страницата:'),
('usermenu_newpagesubmit',1,NOW(),NOW(),'bg','Създаване и отваряне на страницата'),
('usermenu_texttoedit',1,NOW(),NOW(),'bg','Text:'),
('usermenu_back',1,NOW(),NOW(),'bg','Връщане обратно'),
('usermenu_newpagetitle',1,NOW(),NOW(),'bg','Заглавие:'),
('usermenu_edittext',1,NOW(),NOW(),'bg','Редактиране на текст');
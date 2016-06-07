CREATE TABLE IF NOT EXISTS `refstat` (
  `ID` int(11) NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `page_id` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `IP` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `referer` text COLLATE utf8_unicode_ci NOT NULL,
  `agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
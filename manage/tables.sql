-- phpMyAdmin SQL Dump
-- version 5.2.0-rc1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Време на генериране: 24 ное 2023 в 05:19
-- Версия на сървъра: 8.0.26
-- Версия на PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данни: `mycms`
--

-- --------------------------------------------------------

--
-- Структура на таблица `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `ID` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `nolink` tinyint(1) NOT NULL DEFAULT '0',
  `date_time_1` datetime NOT NULL DEFAULT '0000-01-01 00:00:00',
  `date_time_2` datetime NOT NULL,
  `language` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'bg',
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `content`
--

INSERT INTO `content` (`ID`, `name`, `nolink`, `date_time_1`, `date_time_2`, `language`, `text`) VALUES
(1, 'p1_title', 0, '2023-11-01 09:14:59', '2023-11-21 19:07:10', 'bg', 'Начална страница'),
(2, 'p1_title', 0, '2023-11-01 09:14:59', '2023-11-21 19:07:24', 'en', 'Home Page'),
(3, 'p1_content', 0, '2023-11-01 09:14:59', '2023-11-22 21:35:19', 'bg', '<h2>Поздравления!</h2>\r\n\r\n<p>Вие успешно сте инталирали ситема за управление на съдържанието Vanyog CMS на своя сайт.</p>\r\n\r\n<p>Може да започнете с редактиране на всяка от примерните страници, за да представите в тях свое съдържание, а след това да продължите със създаване на още страници. Вижте: <a href=\"https://github.com/vanyog/mycms/wiki/%D0%A3%D0%BA%D0%B0%D0%B7%D0%B0%D0%BD%D0%B8%D1%8F-%D0%B7%D0%B0-%D0%B0%D0%B4%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D0%B8%D1%80%D0%B0%D0%BD%D0%B5-%D0%BD%D0%B0-%D1%81%D0%B0%D0%B9%D1%82\" target=\"_blank\">Указания за поддържане на сайт с VanyoG CMS</a></p>\r\n\r\n<p>Приятно и успешно представяне в Интернет!</p>\r\n\r\n\r\n\r\n<h2>Защо VanyoG CMS?</h2>\r\n\r\n<ul>\r\n<li>Малка по обем система. Можете да инсталирате няколко копия и да ги използвате за различни чести на своя сайт, ако искате всяка част да има независим дизайн и съдържание.</li>\r\n<li>Напълно готов за ползване, бърз, оптимизиран по стандартите сайт с автоматично поддържаща се карта на сайта и търсачка.</li>\r\n<li>Бързо преминаване от разглеждане на сайта към редактиране на съдържанието и външния вид.</li>\r\n</ul>'),
(4, 'p1_content', 0, '2023-11-01 09:14:59', '2023-11-23 11:43:45', 'en', '<h2>Congratulations!</h2>\r\n\r\n<p>You have successfully installed the Vanyog CMS content management system on your site.</p>\r\n\r\n<p>You can start by editing each of the sample pages to feature your own content, then continue to create more pages.</p>\r\n\r\n<p>Enjoy and successful presentation! See: <a href=\"https://github.com/vanyog/mycms/wiki/%D0%A3%D0%BA%D0%B0%D0%B7%D0%B0%D0%BD%D0%B8%D1%8F-%D0%B7%D0%B0-%D0%B0%D0%B4%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D0%B8%D1%80%D0%B0%D0%BD%D0%B5-%D0%BD%D0%B0-%D1%81%D0%B0%D0%B9%D1%82\" target=\"_blank\">Указания за поддържане на сайт с VanyoG CMS</a></p>\r\n\r\n<p>Have a nice and successful presentation on the Internet!</p>\r\n\r\n<h2>Why VanyoG CMS?</h2>\r\n\r\n<ul>\r\n<li>Small volume system. You can install multiple copies and use them for different parts of your site if you want each part to have independent design and content.</li>\r\n<li>Completely ready-to-use, fast, standards-optimized site with automatically maintained sitemap and search engine.</li>\r\n<li>Quickly switch from browsing the site to editing the content and appearance.</li>\r\n</ul>'),
(5, 'error_404_title', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Грешен номер на страница'),
(6, 'error_404_title', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Incorrect page number'),
(7, 'error_404_content', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '<p>На сайта няма страница с такъв номер.</p>'),
(8, 'error_404_content', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', '<p>Page is not found.</p>'),
(9, 'p1_link', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Начало'),
(10, 'menu_start', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', ''),
(11, 'p1_link', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Home'),
(12, 'saveData', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Съхраняване на данните'),
(13, 'saveData', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Save gada'),
(14, 'dataSaved', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Данните бяха съхранени.'),
(15, 'dataSaved', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Data were saved.'),
(16, 'month_names', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '$month = array(\"\",\"януари\",\"февруари\",\"март\",\"април\",\"май\",\"юни\",\"юли\",\"август\",\"септември\",\"октомври\",\"ноември\",\"декември\");'),
(17, 'month_names', 127, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', '$month = array(\"\",\"January\",\"February\",\"March\",\"April\",\"May\",\"June\",\"July\",\"August\",\"September\",\"October\",\"November\",\"December\");'),
(18, 'user_address', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Адрес:'),
(19, 'user_address', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Address:'),
(20, 'user_backto', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Връщане към:'),
(21, 'user_backto', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Go to:'),
(22, 'user_country', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Държава:'),
(23, 'user_country', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Country:'),
(24, 'user_delete', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Изтриване на потребителя'),
(25, 'user_delete', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Delete User'),
(26, 'user_email', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Имейл:'),
(27, 'user_email', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'E-mail:'),
(28, 'user_enter', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Вход'),
(29, 'user_enter', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Login'),
(30, 'user_firstname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Име:'),
(31, 'user_firstname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Name:'),
(32, 'user_firstuser', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '<p>На сайта все още няма регистрирани потребители. Сега ше регистрирате първия потребител.</p>'),
(33, 'user_firstuser', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', '<p>Site has not yet registered users. Now we register the first user.</p>'),
(34, 'user_homеpage', 1, '2023-11-01 09:14:59', '2023-11-18 17:13:52', 'bg', 'Началната страница'),
(35, 'user_homеpage', 1, '2023-11-01 09:14:59', '2023-11-19 09:23:47', 'en', 'Home page'),
(36, 'user_institution', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Месторабота:'),
(37, 'user_institution', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Institution:'),
(38, 'user_lastpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Предишната страница'),
(39, 'user_lastpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Previous Page'),
(40, 'user_logaut', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Изход'),
(41, 'user_logaut', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Logout'),
(42, 'user_login', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Влизане в системата'),
(43, 'user_login', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'User login'),
(44, 'user_login_button', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Влизане'),
(45, 'user_login_button', 1, '2023-11-01 09:14:59', '2023-11-17 15:27:08', 'en', 'Log in'),
(46, 'user_logoutcontent', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '<p>Вие успешно излязохте от системата</p>'),
(47, 'user_logoutcontent', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', '<p>You have successfully logged out of the system</p>'),
(48, 'user_logouttitle', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Изход от системата'),
(49, 'user_logouttitle', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Log out page'),
(50, 'user_newreg', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Нова регистрация'),
(51, 'user_newreg', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'New registration'),
(52, 'user_password', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Парола:'),
(53, 'user_password', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Password:'),
(54, 'user_passwordchanged', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Паролата беше сменена. При следващото влизане използвайте новата парола.'),
(55, 'user_passwordchanged', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'The password was changed. The next time use the new password.'),
(56, 'user_passwordconfirm', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Повторение на паролата:'),
(57, 'user_passwordconfirm', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Repeat password:'),
(58, 'user_passwordinvalid', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Паролата и нейното повторение не съвпадаха. Паролата не е променена.'),
(59, 'user_passwordinvalid', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Password and its repetition does not match. Password is not changed.'),
(60, 'user_savenew', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Създаване на нов потребител'),
(61, 'user_savenew', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Create a new user'),
(62, 'user_secondname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Презиме:'),
(63, 'user_secondname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Surname:'),
(64, 'user_telephone', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Телефон:'),
(65, 'user_telephone', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Phone:'),
(66, 'user_thirdname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Фамилия:'),
(67, 'user_thirdname', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Family:'),
(68, 'user_username', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Потребителско име:'),
(69, 'user_username', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Username:'),
(70, 'usermenu_addtomenu', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Линк в менюто на текущата страница:'),
(71, 'usermenu_addtomenu', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Create a link in the current page:'),
(72, 'usermenu_back', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Връщане обратно'),
(73, 'usermenu_back', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Go back'),
(74, 'usermenu_cantdelindex', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Не можете да изтриете главната страница на раздел, докато в този раздел има други страници.'),
(75, 'usermenu_cantdelindex', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'You can not delete the main page of a section, while this section has pages.'),
(76, 'usermenu_confirdeleting', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Изтриването на страница ще направи невалидни всички хипервръзки, които сочат към нея. Наистина ли искате да изтриете тази страница?'),
(77, 'usermenu_confirdeleting', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Deleting a page will break all links to it. Do you really want to delete this page?'),
(78, 'usermenu_createnewpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Създаване на нова страница'),
(79, 'usermenu_createnewpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Create a new page'),
(80, 'usermenu_edittext', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Редактиране на текст'),
(81, 'usermenu_edittext', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Editing Text'),
(82, 'usermenu_language', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Език:'),
(83, 'usermenu_language', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Language:'),
(84, 'usermenu_linktext', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Текст на линка в менюто:'),
(85, 'usermenu_linktext', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Text on the link to page:'),
(86, 'usermenu_menupos', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Позиция в менюто:'),
(87, 'usermenu_menupos', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Position in the menu:'),
(88, 'usermenu_newmenu', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Нов раздел:'),
(89, 'usermenu_newmenu', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'New section:'),
(90, 'usermenu_newpagecontent', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Съдържание на страницата:'),
(91, 'usermenu_newpagecontent', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Page Content:'),
(92, 'usermenu_newpagesubmit', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Създаване и отваряне на страницата'),
(93, 'usermenu_newpagesubmit', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Create and open the page'),
(94, 'usermenu_newpagetitle', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Заглавие:'),
(95, 'usermenu_newpagetitle', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Heading:'),
(96, 'usermenu_texttoedit', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Текст:'),
(97, 'usermenu_texttoedit', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Text:'),
(98, 'menu_start_1', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', ''),
(99, 'menu_start_1', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', ''),
(100, 'admin_style', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', ''),
(101, 'admin_style', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', ''),
(102, 'p2_title', 0, '2023-11-01 09:14:59', '2023-11-12 04:18:20', 'bg', 'Регистриран потребител'),
(104, 'pagestat_total', 0, '2023-11-01 09:34:03', '2023-11-01 09:34:32', 'bg', 'Посещения на страницата: общо '),
(105, 'pagestat_today', 0, '2023-11-01 09:34:36', '2023-11-01 09:34:42', 'bg', ' днес '),
(106, 'powered_by', 0, '2023-11-01 11:15:28', '2023-11-12 04:45:40', 'en', 'Powered by '),
(107, 'powered_by', 0, '2023-11-01 11:16:19', '2023-11-01 17:38:35', 'bg', 'Направено с '),
(326, 'sitesearch_notfound', 0, '2023-11-16 22:13:07', '2023-11-16 22:13:07', 'en', 'No result was found for '),
(110, 'home_page_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>Текст на страницата.</p>'),
(111, 'home_page_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>Content of the Homa Page.</p>'),
(325, 'sitesearch_clear', 1, '2023-11-16 22:11:59', '2023-11-16 22:12:32', 'en', 'Cleaning'),
(324, 'sitesearch_submit', 1, '2023-11-16 22:11:40', '2023-11-16 22:11:40', 'en', 'Search'),
(114, 'error_404_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>На сайта няма страница с такъв номер.</p>'),
(115, 'error_404_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>Page is not found.</p>'),
(116, 'p1_link', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Начало'),
(117, 'menu_start', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', ''),
(118, 'p1_link', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Home'),
(119, 'saveData', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Съхраняване на данните'),
(120, 'saveData', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Save gada'),
(121, 'dataSaved', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Данните бяха съхранени.'),
(122, 'dataSaved', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Data were saved.'),
(123, 'month_names', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '$month = array(\"\",\"януари\",\"февруари\",\"март\",\"април\",\"май\",\"юни\",\"юли\",\"август\",\"септември\",\"октомври\",\"ноември\",\"декември\");'),
(124, 'month_names', 127, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '$month = array(\"\",\"January\",\"February\",\"March\",\"April\",\"May\",\"June\",\"July\",\"August\",\"September\",\"October\",\"November\",\"December\");'),
(125, 'user_address', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Адрес:'),
(126, 'user_address', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Address:'),
(127, 'user_backto', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Връщане към:'),
(128, 'user_backto', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Go to:'),
(129, 'user_country', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Държава:'),
(130, 'user_country', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Country:'),
(131, 'user_delete', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Изтриване на потребителя'),
(132, 'user_delete', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Delete User'),
(133, 'user_email', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Имейл:'),
(134, 'user_email', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'E-mail:'),
(135, 'user_enter', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Вход'),
(136, 'user_enter', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Login'),
(137, 'user_firstname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Име:'),
(138, 'user_firstname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Name:'),
(139, 'user_firstuser', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>На сайта все още няма регистрирани потребители. Сега ше регистрирате първия потребител.</p>'),
(140, 'user_firstuser', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>Site has not yet registered users. Now we register the first user.</p>'),
(141, 'user_homеpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Началната страница'),
(142, 'user_homеpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Home page'),
(143, 'user_institution', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Месторабота:'),
(144, 'user_institution', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Institution:'),
(145, 'user_lastpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Предишната страница'),
(146, 'user_lastpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Previous Page'),
(147, 'user_logaut', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Изход'),
(148, 'user_logaut', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Logout'),
(149, 'user_login', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Влизане в системата'),
(150, 'user_login', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'User login'),
(151, 'user_login_button', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Влизане'),
(153, 'user_logoutcontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>Вие успешно излязохте от системата</p>'),
(154, 'user_logoutcontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>You have successfully logged out of the system</p>'),
(156, 'user_logouttitle', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Log out page'),
(157, 'user_newreg', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Нова регистрация'),
(158, 'user_newreg', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'New registration'),
(159, 'user_password', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Парола:'),
(160, 'user_password', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Password:'),
(161, 'user_passwordchanged', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Паролата беше сменена. При следващото влизане използвайте новата парола.'),
(162, 'user_passwordchanged', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'The password was changed. The next time use the new password.'),
(163, 'user_passwordconfirm', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Повторение на паролата:'),
(164, 'user_passwordconfirm', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Repeat password:'),
(165, 'user_passwordinvalid', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Паролата и нейното повторение не съвпадаха. Паролата не е променена.'),
(166, 'user_passwordinvalid', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Password and its repetition does not match. Password is not changed.'),
(167, 'user_savenew', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Създаване на нов потребител'),
(168, 'user_savenew', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Create a new user'),
(169, 'user_secondname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Презиме:'),
(170, 'user_secondname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Surname:'),
(171, 'user_telephone', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Телефон:'),
(172, 'user_telephone', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Phone:'),
(173, 'user_thirdname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Фамилия:'),
(174, 'user_thirdname', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Family:'),
(175, 'user_username', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Потребителско име:'),
(176, 'user_username', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Username:'),
(177, 'usermenu_addtomenu', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Линк в менюто на текущата страница:'),
(178, 'usermenu_addtomenu', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Create a link in the current page:'),
(179, 'usermenu_back', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Връщане обратно'),
(180, 'usermenu_back', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Go back'),
(181, 'usermenu_cantdelindex', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Не можете да изтриете главната страница на раздел, докато в този раздел има други страници.'),
(182, 'usermenu_cantdelindex', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'You can not delete the main page of a section, while this section has pages.'),
(183, 'usermenu_confirdeleting', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Изтриването на страница ще направи невалидни всички хипервръзки, които сочат към нея. Наистина ли искате да изтриете тази страница?'),
(184, 'usermenu_confirdeleting', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Deleting a page will break all links to it. Do you really want to delete this page?'),
(185, 'usermenu_createnewpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Създаване на нова страница'),
(186, 'usermenu_createnewpage', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Create a new page'),
(187, 'usermenu_edittext', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Редактиране на текст'),
(188, 'usermenu_edittext', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Editing Text'),
(189, 'usermenu_language', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Език:'),
(190, 'usermenu_language', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Language:'),
(191, 'usermenu_linktext', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Текст на линка в менюто:'),
(192, 'usermenu_linktext', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Text on the link to page:'),
(193, 'usermenu_menupos', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Позиция в менюто:'),
(194, 'usermenu_menupos', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Position in the menu:'),
(195, 'usermenu_newmenu', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Нов раздел:'),
(196, 'usermenu_newmenu', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'New section:'),
(197, 'usermenu_newpagecontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Съдържание на страницата:'),
(198, 'usermenu_newpagecontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Page Content:'),
(199, 'usermenu_newpagesubmit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Създаване и отваряне на страницата'),
(200, 'usermenu_newpagesubmit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Create and open the page'),
(201, 'usermenu_newpagetitle', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Заглавие:'),
(203, 'usermenu_texttoedit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Текст:'),
(204, 'usermenu_texttoedit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Text:'),
(205, 'menu_start_1', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', ''),
(206, 'menu_start_1', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', ''),
(207, 'admin_style', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', ''),
(208, 'admin_style', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', ''),
(323, 'sitesearch_label', 1, '2023-11-16 22:10:52', '2023-11-19 09:23:08', 'en', 'Search the site'),
(210, 'p2_content', 0, '2023-11-01 12:23:46', '2023-11-22 16:22:29', 'bg', '<!--$$_USERREG_admin|logout_$$-->\r\n<!--$$_USERREG_admin|edit_$$-->'),
(211, 'pagestat_total', 0, '2023-11-01 12:25:02', '2023-11-01 12:27:14', 'en', 'Page is visited: in total '),
(212, 'pagestat_today', 0, '2023-11-01 12:26:22', '2023-11-01 12:28:23', 'en', ', today '),
(213, 'userreg_nouserlogedin', 0, '2023-11-01 20:06:12', '2023-11-01 20:06:31', 'bg', 'Няма влязъл потребител'),
(214, 'userreg_new', 1, '2023-11-01 21:19:27', '2023-11-23 05:26:41', 'bg', 'Нов потребител'),
(215, 'userreg_create', 1, '2023-11-01 21:20:44', '2023-11-23 05:26:03', 'bg', 'Създаване на потребителя'),
(216, 'user_language', 1, '2023-11-11 21:54:29', '2023-11-18 08:47:08', 'bg', 'Предпочитан език за кореспонденция: '),
(217, 'user_aemails', 1, '2023-11-11 21:55:17', '2023-11-18 08:47:18', 'bg', 'Допълнителни имейли, отделени със запетаи, без интервали: '),
(218, 'user_position', 1, '2023-11-11 21:57:04', '2023-11-18 08:47:41', 'bg', 'Предпочитано обръщение (проф. инж. и др. подобни): '),
(219, 'userreg_egithelp', 0, '2023-11-11 21:57:44', '2023-11-11 21:58:38', 'bg', '<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>Указания за попълване</h2>\r\n\r\n<p>Попълването на нито едно от полетата не е задължително, но може да е необходимо за целта, поради която е създаден профила.</p>\r\n\r\n<p>Променете само съдържанието на полетата с информация, която искате да промените или добавите, и натискате бутона <strong>&quot;Съхраняване на данните&quot;</strong></p>\r\n\r\n<p>След натискане на бутона &quot;Съхраняване на данните&quot;, системата зарежда променените данни и показва отново настоящата страница. Така виждате промените и убеждавате, че са запазени.</p>\r\n\r\n<p>Ако няма да променяте личните си данни или когато свършите с редактирането, преминете към друга страница от сайта чрез някоя от хипервръзките, намиращи се около този формуляр.</p>\r\n\r\n<p>Полетата <strong>&quot;Парола&quot;</strong> и &quot;<strong>Повторение на паролата&quot;</strong> се попълват само ако искате да смените сегашната си парола с друга. Промяната се извършва само ако в двете полета се въведат еднакви пароли.</p>\r\n\r\n<p>Поле <strong>&quot;Потребителско име&quot;</strong> се променя само ако желаете при влизане в сайта да пишете нещо друго, а не имейл адреса си.</p>\r\n\r\n<p>Ако промените &quot;Потребителско име&quot; или &quot;Парола&quot; промяната настъпва веднага след натискане на бутона &quot;Съхраняване на данните&quot; и при следващото влизане в сайта трябва да използвате новите.</p>\r\n\r\n<p>Полето <strong>&quot;Длъжност и звание&quot;</strong> се използва в обръщението към Вас в изпращани от системата имейли. Имайте предвид това и го напишете, както бихте искали да стои пред Вашето име. Например: \"доц. д-р инж.\"</p>\r\n\r\n<p>В полето &quot;<strong>Допълнителни имейли</strong>&quot; може да попълните други имайл адреси, които използвате и до които искате да бъдат изпращани съобщенията от системата.</p>\r\n'),
(220, 'p2_content', 0, '2023-11-11 22:00:15', '2023-11-23 11:43:52', 'en', '<!--$$_USERREG_admin|logout_$$-->\r\n<!--$$_USERREG_admin|edit_$$-->'),
(221, 'p2_title', 0, '2023-11-11 22:00:43', '2023-11-12 04:43:53', 'en', 'Registered user'),
(222, 'user_language', 1, '2023-11-11 22:01:57', '2023-11-18 11:23:54', 'en', 'Preferred language for correspondence: '),
(223, 'user_aemails', 1, '2023-11-11 22:03:08', '2023-11-18 11:23:48', 'en', 'Additional emails separated by commas, no spaces: '),
(224, 'user_position', 1, '2023-11-11 22:07:23', '2023-11-18 11:23:36', 'en', 'Rank & degree (Prof. Eng., etc.): '),
(225, 'userreg_egithelp', 0, '2023-11-11 22:07:53', '2023-11-11 22:07:53', 'en', '<p><a id=\\\"help\\\" name=\\\"help\\\"></a></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>Instructions for completion of this form</h2>\r\n\r\n<p>Completing of any of the fields is optional.</p>\r\n\r\n<p>Edit only the contents of the field with the information you want to add or change and click the <strong>&quot;Save Changes&quot;</strong> button.</p>\r\n\r\n<p>After clicking the &quot;Save Changes&quot; button, the system loads the changed data and displays this page again to show you the changes.</p>\r\n\r\n<p>If you have finished editing or you do not want to change your personal data, use the hyperlinks you see above and around this form to go to another page on the site.</p>\r\n\r\n<p>Edit the fields <strong>&quot;Password&quot;</strong> and <strong>&quot;Repeat password&quot;</strong> only if you want to change your current password with another one. The change shall be made only if the both fields have the same passwords.</p>\r\n\r\n<p>Change the field <strong>&quot;Username&quot;</strong> if only you want to log in with another username instead of your email address.</p>\r\n\r\n<p>If you change the &quot;Username&quot; and &quot;Password&quot; be careful not to forget that you had made changes, and the next time log in the site with your new username and password.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>'),
(226, 'userreg_newreg', 0, '2023-11-12 04:21:23', '2023-11-12 04:21:40', 'bg', 'Нова регистрация'),
(227, 'userreg_logintext', 0, '2023-11-12 04:21:55', '2023-11-12 04:22:57', 'bg', '<h2>Вход за регистрирани потребители</h2>\r\n<p>Потребителското име трябва да е имейл адресът, с който сте се регистрирали, освен ако при редактиране на личните си данни не сте задали Вие друго потребителско име.</p>\r\n<p><b>Ако още не сте се регистрирали или сте забравили паролата си,<br>отворете страница</b> '),
(228, 'USERREG_admin', 0, '2023-11-12 04:23:10', '2023-11-12 04:23:14', 'bg', NULL),
(229, 'userreg_login', 0, '2023-11-12 04:24:12', '2023-11-12 04:26:40', 'bg', '<b>Вход</b>'),
(230, 'userreg_newregtext', 0, '2023-11-12 04:25:17', '2023-11-12 04:26:18', 'bg', '<h2>Регистриране или смяна на паролата</h2>\r\n<p>Паролата трябва да е не по-къса от 8 символа.</p>\r\n<p><b>Ако вече сте се регистрирали, за да влезете в сайта, използвайте страница</b>'),
(231, 'userreg_gdpr', 0, '2023-11-12 04:26:51', '2023-11-12 04:27:49', 'bg', 'Лични данни:'),
(232, 'userreg_gdpr2', 0, '2023-11-12 04:27:59', '2023-11-12 04:30:41', 'bg', 'С поставяне на тази отметка, Вие се съгласявате на настоящия сайт да се запазват и обработват Ваши лични данни.</a>\r\n'),
(233, 'userreg_regsubmit', 0, '2023-11-12 04:31:46', '2023-11-12 04:32:06', 'bg', 'Регистриране'),
(234, 'userreg_newhelp', 0, '2023-11-12 04:32:58', '2023-11-12 04:34:19', 'bg', '<p>&nbsp;</p>\r\n<hr>\r\n\r\n<h2>Указания за регистриране</h2>\r\n\r\n<div class=\"who\">\r\n<p>Този формуляр се използва в два случая:</p>\r\n\r\n<ol>\r\n	<li>Ако за първи път се регистрирате.</li>\r\n	<li>Ако сте се регистрирали и искате да смените/възстановите паролата си.</li>\r\n</ol>\r\n\r\n<p>И в двата случая се прави едно и също.</p>\r\n\r\n<p>И трите полета е задължително да се попълнят.</p>\r\n\r\n<p><strong>Имейл адресът</strong>, трябва да е от реално съществуваща електронна поща, защото регистрацията се завършва след отваряне на изпратено до попълнения адрес съобщение.</p>\r\n\r\n<p>В случай, че използвате този формуляр за да смените паролата си, посочете имейла, с който вече сте се регистрирали.</p>\r\n\r\n<p><strong>Паролата</strong> трябва де е не по-къса от 8 символа. <span style=\"color:red\">Запомнете паролата или я запишете!</span></p>\r\n\r\n<p>Поставете отметката &quot;<strong>Не съм робот</strong>&quot;.</p>\r\n\r\n<p>Щом натиснете бутона &quot;Регистриране&quot; системата изпраща съобщение до посочения имел.</p>\r\n\r\n<p>Завършването на регистрацията или смяната на паролата с нова, става след като я потвърдите, като отворите адреса от полученото съобщение.</p>\r\n\r\n<p>При следващо влизане в своя профил посочете имейл адреса си и новата парола, която сте потвърдили.</p>\r\n</div>\r\n\r\n<p>&nbsp;</p>'),
(235, 'userreg_newhelp', 0, '2023-11-12 04:39:23', '2023-11-12 04:39:23', 'en', '<p>&nbsp;</p>\r\n<hr>\r\n\r\n<h2>Registration instructions</h2>\r\n\r\n<div class=\"who\">\r\n<p>This form is used in two cases:</p>\r\n\r\n<ol>\r\n	<li>If you are registering for the first time.</li>\r\n	<li>If you have registered and want to change/reset your password.</li>\r\n</ol>\r\n\r\n<p>In both cases, the same thing is done.</p>\r\n\r\n<p>All three fields must be filled out.</p>\r\n\r\n<p><strong>Email address</strong>, must be from a real existing e-mail, because the registration is completed after opening a message sent to the filled-in address.</p>\r\n\r\n<p>If you are using this form to change your password, please enter the email you have already registered with.</p>\r\n\r\n<p><strong>Password</strong> must be no shorter than 8 characters. <span style=\"color:red\">Remember the password or write it down!</span></p>\r\n\r\n<p>Tick &quot;<strong>I\'m not a robot</strong>&quot;.</p>\r\n\r\n<p>As soon as you press the button &quot;Register&quot; the system sends a message to the specified mistletoe.</p>\r\n\r\n<p>Completing the registration or changing the password to a new one is done after confirming it by opening the address from the received message.</p>\r\n\r\n<p>The next time you log into your account, provide your email address and the new password you confirmed.</p>\r\n</div>\r\n\r\n<p>&nbsp;</p>'),
(236, 'userreg_regsubmit', 0, '2023-11-12 04:39:45', '2023-11-12 04:39:45', 'en', 'Registration'),
(237, 'userreg_gdpr2', 0, '2023-11-12 04:41:08', '2023-11-12 04:41:08', 'en', 'By placing this checkmark, you agree to this site to store and process your personal data.'),
(238, 'userreg_gdpr', 0, '2023-11-12 04:41:27', '2023-11-12 04:41:27', 'en', 'Personal data:'),
(239, 'userreg_login', 0, '2023-11-12 04:41:41', '2023-11-12 04:41:41', 'en', '<b>Log in</b>'),
(240, 'userreg_newregtext', 0, '2023-11-12 04:42:16', '2023-11-12 04:42:16', 'en', '<h2>Register or change password</h2>\r\n<p>The password must be no shorter than 8 characters.</p>\r\n<p><b>If you have already registered to enter the site, use page</b>'),
(241, 'userreg_logintext', 0, '2023-11-12 04:42:39', '2023-11-17 15:23:13', 'en', '<h2>Login for registered users</h2>\r\n<p>The username must be the email address you registered with, unless you set a different username when editing your personal data.</p>\r\n<p><b>If you have not yet registered or have forgotten your password,<br>go to page </b>'),
(242, 'userreg_newreg', 0, '2023-11-12 04:42:45', '2023-11-12 04:42:45', 'en', 'New registration'),
(243, 'userreg_create', 1, '2023-11-12 04:44:52', '2023-11-23 11:36:33', 'en', 'Create the user'),
(244, 'userreg_new', 1, '2023-11-12 04:45:03', '2023-11-23 11:36:26', 'en', 'New user'),
(245, 'userreg_nouserlogedin', 0, '2023-11-12 04:45:34', '2023-11-12 04:45:34', 'en', 'There is no user logged in'),
(246, 'userreg_mustlogin', 0, '2023-11-12 04:58:26', '2023-11-12 05:06:31', 'bg', 'За да видите съдържанието на настоящатаа страница, трябва да влезете през страница '),
(247, 'userreg_logoutcontent', 0, '2023-11-12 05:09:39', '2023-11-12 05:10:16', 'bg', '<h2>Вие излязохте от системата</h2>\r\n<p>Можете да влезете отново през страница  '),
(248, 'userreg_backto', 0, '2023-11-12 05:12:41', '2023-11-12 05:12:58', 'bg', 'Връщане на страницата, от която излязохте'),
(249, 'userreg_wrong', 0, '2023-11-12 05:16:03', '2023-11-12 05:16:46', 'bg', 'Грешна парола или потребителско име. При забравена парола използвайте страница '),
(250, 'userreg_wrong', 0, '2023-11-12 05:19:18', '2023-11-12 05:19:18', 'en', 'Wrong password or username. If you forgot your password, use page '),
(251, 'userreg_backto', 0, '2023-11-12 05:19:57', '2023-11-12 05:19:57', 'en', 'Return to the page you exited from'),
(252, 'userreg_logoutcontent', 0, '2023-11-12 05:20:47', '2023-11-12 05:20:47', 'en', '<h2>You have logged out</h2>\r\n<p>You can log in again through page '),
(253, 'userreg_mustlogin', 0, '2023-11-12 05:21:29', '2023-11-12 05:21:29', 'en', 'To view the content of the current page, you must log in through page '),
(254, 'usermenu_editmenu', 0, '2023-11-14 15:55:46', '2023-11-14 15:55:46', 'bg', 'Редактиране на хипервръзка от меню'),
(255, 'usermenu_menugroup', 1, '2023-11-14 15:59:18', '2023-11-14 15:59:47', 'bg', 'Номер на менюто:'),
(256, 'usermenu_menutext', 1, '2023-11-14 16:00:15', '2023-11-14 16:00:24', 'bg', 'Текст върху хипервръзката:'),
(257, 'usermenu_menulinkdb', 1, '2023-11-14 16:00:57', '2023-11-14 16:01:07', 'bg', 'Номер на страницата, която се отваря:'),
(258, 'p2_link', 0, '0000-01-01 00:00:00', '2023-11-16 17:04:12', 'bg', 'Вход'),
(259, 'p3_title', 0, '2023-11-14 16:09:29', '2023-11-16 16:53:53', 'bg', 'Относно'),
(260, 'p3_content', 0, '2023-11-14 16:09:29', '2023-11-18 07:53:18', 'bg', '<p>Обикновено всеки сайт има такава страница. Редактирайте тази, за да представите в нея информация подходяща за Вашия сайт.</p>\r\n'),
(261, 'p3_link', 0, '2023-11-14 16:09:29', '2023-11-14 16:09:29', 'bg', 'Относно'),
(262, 'm1_link', 0, '2023-11-14 16:09:29', '2023-11-14 16:09:29', 'bg', 'Относно'),
(263, 'cookies_message', 0, '2023-11-15 10:23:30', '2023-11-15 23:59:08', 'bg', '<p>За да бъде приятно Вашето изживяване, този сайт използва &quot;бисквитки&quot;. <a href=\"/index.php?pid=5\">Вижте повече</a>. Съгласни ли сте да приемете тези &quot;бисквитки&quot;?&nbsp; <a href=\"\" onclick=\"cookies_accept();return false;\">ДА</a></p>'),
(264, 'cookies_message', 0, '2023-11-15 10:30:11', '2023-11-16 17:58:21', 'en', '<p id=\"cookie_message\">This website uses cookies to ensure you get the best experience on our website. <a href=\"/index.php?pid=5\">More info</a>. &nbsp; <a href=\"\" onclick=\"cookies_accept();return false;\">Got It</a>!</p>\r\n'),
(265, 'p3_title', 0, '2023-11-15 16:00:56', '2023-11-16 17:57:23', 'en', 'About'),
(266, 'p3_content', 0, '2023-11-15 16:01:48', '2023-11-18 11:25:41', 'en', '<p>Usually every site has such a page. Edit this page to present information relevant to your site.</p>'),
(267, 'p3_link', 0, '2023-11-15 16:02:01', '2023-11-15 16:02:01', 'en', 'About'),
(268, 'm1_link', 0, '2023-11-15 16:02:07', '2023-11-15 16:02:07', 'en', 'About'),
(269, 'p2_link', 0, '2023-11-15 16:02:12', '2023-11-16 17:55:01', 'en', 'Login'),
(270, 'usermenu_menulinkdb', 1, '2023-11-15 16:02:41', '2023-11-15 16:02:41', 'en', 'Number of the page that opens:'),
(271, 'usermenu_menutext', 1, '2023-11-15 16:03:02', '2023-11-15 16:03:02', 'en', 'Hyperlink text:'),
(272, 'usermenu_menugroup', 1, '2023-11-15 16:03:33', '2023-11-15 16:03:33', 'en', 'Menu number:'),
(273, 'usermenu_editmenu', 0, '2023-11-15 16:03:54', '2023-11-15 16:03:54', 'en', 'Edit a hyperlink from a menu'),
(274, 'p4_title', 0, '2023-11-15 20:21:29', '2023-11-15 20:21:29', 'bg', 'Политика за лични данни на този сайт'),
(275, 'p4_content', 0, '2023-11-15 20:22:28', '2023-11-17 07:27:47', 'bg', '<p>Настоящият сайт не обработва и не съхранява лични данни по начин позволяващ свързване на данни с конкретна личност.</p>\r\n<h2>\"Бисквитки\" и други подобни технологии</h2>\r\n<p>С цел подобряване на Вашето изживялане при посещаване на сайта използваме \"бисквитки\".  Вижте повече за тази технология в <a href=\"https://bg.wikipedia.org/wiki/HTTP_%D0%B1%D0%B8%D1%81%D0%BA%D0%B2%D0%B8%D1%82%D0%BA%D0%B0\" target=\"_blank\">bg.wikipedia.org/wiki/HTTP_бисквитка</a>.</p>\r\n<!--$$_COOKIES_$$-->\r\n<p>При желание от Ваша страна, когато бъдете попитани дали позволявате запазване на \"бисквитки\", породено от посещаване на този сайт, можете да откажете, а чрез инструментите на своя браузър можете да изтриете запазените \"бисквитки\".</p>'),
(276, 'cookies_table', 1, '2023-11-15 20:24:10', '2023-11-17 07:29:56', 'bg', 'Може да прегледате списъка на \"бисквитките\" от този сайт във Вашето устройство:'),
(277, 'cookies_name', 1, '2023-11-15 20:24:22', '2023-11-15 20:36:59', 'bg', 'Име'),
(278, 'cookies_value', 1, '2023-11-15 20:24:34', '2023-11-15 20:37:08', 'bg', 'Стойност'),
(279, 'cookies_description', 1, '2023-11-15 20:24:46', '2023-11-15 20:37:18', 'bg', 'Предназначение'),
(280, 'cookies_cookies_accept_description', 1, '2023-11-15 20:25:17', '2023-11-15 20:35:00', 'bg', 'Вашето съгласие, да приемате \"бисквитки\".'),
(281, 'cookies_PHPSESSID_description', 1, '2023-11-15 20:25:49', '2023-11-15 20:35:17', 'bg', 'Идентификатор на Вашата комуникационна сесия.'),
(282, 'cookies_edit_description', 1, '2023-11-15 20:32:16', '2023-11-15 20:35:28', 'bg', 'Дали е включен режим на редактиране.'),
(283, 'cookies_language_description', 1, '2023-11-15 20:34:18', '2023-11-15 20:35:37', 'bg', 'Избраният от Вас език, на който да се показва сайта.'),
(311, 'cookies_language_description', 1, '2023-11-16 18:01:42', '2023-11-16 18:01:42', 'en', 'Your chosen language in which to display the site.'),
(285, 'cookies_nocookie', 1, '2023-11-15 21:26:07', '2023-11-15 21:26:56', 'bg', 'Няма \"бисквитки\" от този сайт на Вашето устройство.'),
(286, 'p4_link', 0, '0000-01-01 00:00:00', '2023-11-16 16:52:04', 'bg', 'Лични данни'),
(287, 'p5_link', 0, '0000-01-01 00:00:00', '2023-11-16 16:54:30', 'bg', 'Относно'),
(288, 'menu_start_2', 0, '2023-11-16 16:32:50', '2023-11-16 16:32:50', 'bg', ''),
(289, 'menutree_start', 0, '2023-11-16 16:46:49', '2023-11-16 16:46:49', 'bg', ''),
(290, 'p5_title', 0, '2023-11-16 16:57:06', '2023-11-16 16:57:06', 'bg', 'Карта на сайта'),
(291, 'p5_content', 0, '2023-11-16 16:57:06', '2023-11-23 21:19:21', 'bg', '<!--$$_SITEMAP_$$-->\r\n<!--$$_SETVARIABLE_og_description|На тази страница се разглежда картата на сайта._$$-->'),
(292, 'p6_link', 0, '2023-11-16 16:57:06', '2023-11-16 16:57:06', 'bg', 'Карта на сайта'),
(293, 'm1_link', 0, '2023-11-16 16:57:06', '2023-11-16 16:57:06', 'bg', 'Карта на сайта'),
(294, 'site_map_contract', 0, '2023-11-16 16:58:54', '2023-11-16 16:59:09', 'bg', 'Сгъване на всички'),
(295, 'site_map_expand', 0, '2023-11-16 16:59:47', '2023-11-16 16:59:47', 'bg', 'Разгъване на всички'),
(296, 'sitemap_currentpage', 0, '2023-11-16 17:29:47', '2023-11-16 17:31:02', 'bg', ' - текущата страница'),
(297, 'sitemap_currentpage', 0, '2023-11-16 17:54:57', '2023-11-16 17:54:57', 'en', '- the current page'),
(298, 'site_map_expand', 0, '2023-11-16 17:55:50', '2023-11-16 17:55:50', 'en', 'Expand all'),
(299, 'site_map_contract', 0, '2023-11-16 17:56:17', '2023-11-16 17:56:17', 'en', 'Collapse all'),
(300, 'p5_content', 0, '2023-11-16 17:56:22', '2023-11-16 17:56:22', 'en', '<!--$$_SITEMAP_$$-->'),
(301, 'p5_title', 0, '2023-11-16 17:56:57', '2023-11-16 17:56:57', 'en', 'Site Map'),
(302, 'p6_link', 0, '2023-11-16 17:57:01', '2023-11-16 17:57:01', 'en', 'Site Map'),
(303, 'p5_link', 0, '2023-11-16 17:57:13', '2023-11-16 17:57:13', 'en', 'About'),
(304, 'p4_link', 0, '2023-11-16 17:57:52', '2023-11-16 17:57:52', 'en', 'Personal data'),
(305, 'cookies_nocookie', 1, '2023-11-16 17:58:58', '2023-11-16 17:58:58', 'en', 'There are no cookies from this site on your device.'),
(306, 'p4_content', 0, '2023-11-16 17:59:04', '2023-11-17 15:21:30', 'en', '<p>This site does not process or store personal data in a way that allows data to be linked to a specific person.</p>\r\n<h2>Cookies and other similar technologies</h2>\r\n<p>In order to improve your experience when visiting the site, we use \"cookies\". See more about this technology at <a href=\"https://en.wikipedia.org/wiki/HTTP_cookie\" target=\"_blank\">bg.wikipedia.org/wiki/HTTP_cookie</a>.</p>\r\n<!--$$_COOKIES_$$-->\r\n<p>If you wish, when you are asked whether you allow the storage of \"cookies\" resulting from visiting this site, you can refuse, and through the tools of your browser you can delete the stored \"cookies\".</p>'),
(339, 'p8_title', 0, '2023-11-17 20:46:19', '2023-11-17 20:46:19', 'bg', 'Авторски права'),
(307, 'cookies_description', 1, '2023-11-16 17:59:24', '2023-11-16 17:59:24', 'en', 'Purpose'),
(308, 'cookies_value', 1, '2023-11-16 17:59:43', '2023-11-16 17:59:43', 'en', 'Value'),
(309, 'cookies_name', 1, '2023-11-16 17:59:50', '2023-11-16 17:59:50', 'en', 'Name'),
(310, 'cookies_table', 1, '2023-11-16 18:00:12', '2023-11-17 07:49:28', 'en', 'You can view the list of cookies from this site on your device:'),
(312, 'cookies_edit_description', 1, '2023-11-16 18:02:03', '2023-11-16 18:02:03', 'en', 'Whether edit mode is enabled.'),
(313, 'cookies_PHPSESSID_description', 1, '2023-11-16 18:02:32', '2023-11-16 18:02:32', 'en', 'Identifier of your communication session.'),
(314, 'cookies_cookies_accept_description', 1, '2023-11-16 18:02:57', '2023-11-16 18:02:57', 'en', 'Your consent to accept cookies.'),
(315, 'p4_title', 0, '2023-11-16 18:03:26', '2023-11-16 18:03:26', 'en', 'This site\'s privacy policy'),
(316, 'sitesearch_start', 0, '2023-11-16 18:37:28', '2023-11-16 18:37:28', 'bg', ''),
(317, 'sitesearch_label', 1, '2023-11-16 18:37:48', '2023-11-19 09:22:46', 'bg', 'Търсене в сайта'),
(318, 'sitesearch_submit', 1, '2023-11-16 18:38:10', '2023-11-16 21:14:51', 'bg', 'Търсене'),
(319, 'sitesearch_clear', 1, '2023-11-16 18:54:23', '2023-11-16 21:13:51', 'bg', 'Почистване'),
(320, 'sitesearch_searchfor', 0, '2023-11-16 19:00:46', '2023-11-16 19:01:19', 'bg', 'Резултат от търсене на'),
(321, 'sitesearch_count', 0, '2023-11-16 19:01:31', '2023-11-16 19:01:48', 'bg', 'Намерени резултати'),
(322, 'sitesearch_notfound', 0, '2023-11-16 19:10:45', '2023-11-16 19:11:32', 'bg', 'Не беше намерен резултат за '),
(327, 'sitesearch_count', 0, '2023-11-16 22:13:30', '2023-11-16 22:13:30', 'en', 'Results found '),
(328, 'sitesearch_searchfor', 0, '2023-11-16 22:13:52', '2023-11-16 22:13:52', 'en', 'Search result for '),
(329, 'p7_title', 0, '2023-11-16 22:17:42', '2023-11-16 22:17:42', 'bg', 'Резултати от търсене'),
(330, 'p7_content', 0, '2023-11-16 22:19:15', '2023-11-16 22:19:15', 'bg', '<!--$$_SITESEARCH2_result_$$-->'),
(331, 'p7_content', 0, '2023-11-16 22:24:41', '2023-11-16 22:24:41', 'en', '<!--$$_SITESEARCH2_result_$$-->'),
(332, 'p7_title', 0, '2023-11-16 22:25:01', '2023-11-16 22:25:01', 'en', 'Search results'),
(333, 'cookies_admin_description', 1, '2023-11-17 07:06:12', '2023-11-17 07:10:26', 'bg', 'Означава дали сайта е в решим на администриране.'),
(334, 'cookies_noadm_description', 1, '2023-11-17 07:09:05', '2023-11-22 22:35:46', 'bg', 'Дали да са скрити временно линковете за администрираане. '),
(335, 'cookies_limit_description', 1, '2023-11-17 07:10:03', '2023-11-17 07:10:50', 'bg', 'Ограничение за броя на показваните записи от базата данни при едминистриране.'),
(336, 'cookies_noadm_description', 1, '2023-11-17 07:50:44', '2023-11-23 11:40:33', 'en', 'Whether the administration links are temporarily hidden.'),
(337, 'cookies_limit_description', 1, '2023-11-17 07:51:06', '2023-11-17 07:51:06', 'en', 'Limit on the number of displayed database records in administration.'),
(338, 'cookies_admin_description', 1, '2023-11-17 07:51:31', '2023-11-17 07:51:31', 'en', 'Indicates whether the site is under administration.'),
(340, 'p8_content', 0, '2023-11-17 20:46:19', '2023-11-17 20:53:56', 'bg', '<p>Софтуерът, задвижващ този сайт се разпространява според условията на <a href=\"https://www.gnu.org/licenses/gpl-3.0.en.html\" target=\"_blank\">GNU General Public License</a>.</p>\r\n\r\n<p>Материалите, които се публикуват на сайта се предоставят под лиценз <a href=\"http://creativecommons.org/licenses/by/4.0/?ref=chooser-v1\" target=\"_blank\" rel=\"license noopener noreferrer\" style=\"display:inline-block;\">CC BY 4.0<img style=\"height:22px!important;margin-left:3px;vertical-align:text-bottom;\" src=\"https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1\"><img style=\"height:22px!important;margin-left:3px;vertical-align:text-bottom;\" src=\"https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1\"></a></p> \r\n'),
(341, 'p7_link', 0, '2023-11-17 20:46:19', '2023-11-17 20:46:19', 'bg', 'Авторски права'),
(342, 'm2_link', 0, '2023-11-17 20:46:19', '2023-11-17 20:46:19', 'bg', 'Авторски права'),
(343, 'p8_title', 0, '2023-11-17 20:50:03', '2023-11-18 17:00:29', 'en', 'Copyright'),
(344, 'menutree_start', 0, '2023-11-17 20:51:05', '2023-11-18 16:14:55', 'en', ''),
(345, 'menu_start_2', 0, '2023-11-17 20:51:13', '2023-11-17 20:51:13', 'en', ''),
(346, 'p8_content', 0, '2023-11-17 20:51:32', '2023-11-17 20:55:33', 'en', '<p>The software that powers this site is distributed under the terms of <a href=\"https://www.gnu.org/licenses/gpl-3.0.en.html\" target=\"_blank\">GNU General Public License</a>.</p>\r\n\r\n<p>The materials published on the site are provided under license <a href=\"http://creativecommons.org/licenses/by/4.0/?ref=chooser-v1\" target=\"_blank\" rel=\"license noopener noreferrer\" style=\"display:inline-block;\">CC BY 4.0<img style=\"height:22px!important;margin-left:3px;vertical-align:text-bottom;\" src=\"https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1\"><img style=\"height:22px!important;margin-left:3px;vertical-align:text-bottom;\" src=\"https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1\"></a></p> '),
(347, 'userreg_changepass', 1, '2023-11-18 08:45:38', '2023-11-18 08:55:34', 'bg', '<b>Задайте нова парола на профила си!</b> (Не сте сменили паролата по подразбиране, която е посочена в документацията и известна на всички.)'),
(348, 'userreg_changepass', 1, '2023-11-18 09:32:33', '2023-11-18 11:00:12', 'en', '<b>Set a new password for your account!</b> (You have not changed the default password, which is specified in the documentation and known to everyone.)'),
(349, 'p7_link', 0, '2023-11-18 11:26:25', '2023-11-18 14:58:41', 'en', 'Copyright'),
(351, 'm2_link', 0, '2023-11-18 11:26:27', '2023-11-18 14:08:50', 'en', 'Copyright'),
(352, 'sitesearch_notext', 0, '2023-11-19 06:36:42', '2023-11-19 06:39:03', 'bg', '<p>На тази страница се показват резултатите от търсене в сайта.</p>\r\n<p>Потърсете нещо чрез формата за търсене, за да видите резултат.</p>'),
(353, 'sitesearch_notext', 0, '2023-11-19 09:23:43', '2023-11-19 09:23:43', 'en', '<p>This page displays the results of a site search.</p>\r\n<p>Search for something using the search form to see a result.</p>'),
(354, 'usermenu_newpagefrh', 0, '2023-11-22 16:20:59', '2023-11-22 16:20:59', 'bg', 'Нова страница от подзаглавие'),
(355, 'cookies_cols_description', 1, '2023-11-22 22:37:15', '2023-11-22 22:37:28', 'bg', 'Колко букви да се показват в колоните на таблиците, при редактиране на таблици от базата данни.');
INSERT INTO `content` (`ID`, `name`, `nolink`, `date_time_1`, `date_time_2`, `language`, `text`) VALUES
(356, 'cookies_rows_description', 1, '2023-11-22 22:38:21', '2023-11-22 22:38:35', 'bg', 'Колко реда да се показват в клетките на таблиците при радектиране на таблици от базата данни.'),
(357, 'user_finish', 1, '2023-11-23 03:55:25', '2023-11-23 03:57:11', 'bg', 'Връщане след изтриването'),
(358, 'user_finish', 1, '2023-11-23 11:37:45', '2023-11-23 11:37:45', 'en', 'Go back after deletion'),
(359, 'cookies_rows_description', 1, '2023-11-23 11:38:26', '2023-11-23 11:38:26', 'en', 'How many rows to display in table cells when redacting tables from the database.'),
(360, 'cookies_cols_description', 1, '2023-11-23 11:39:23', '2023-11-23 11:39:23', 'en', 'How many letters to display in table columns when editing tables from the database.'),
(361, 'usermenu_newpagefrh', 0, '2023-11-23 11:44:24', '2023-11-23 11:44:24', 'en', 'New page from subtitle'),
(362, 'uploadfile_nofile', 1, '2023-11-23 21:40:08', '2023-11-23 21:40:38', 'bg', 'Няма качен файл'),
(363, 'uploadfile_upladpagetitle', 1, '2023-11-23 21:57:02', '2023-11-23 21:57:27', 'bg', 'Качване на файл'),
(364, 'uploadfile_timeshow', 1, '2023-11-23 21:57:30', '2023-11-23 21:57:56', 'bg', 'Дата на показване:'),
(365, 'uploadfile_timehide', 1, '2023-11-23 21:57:59', '2023-11-23 21:58:21', 'bg', 'Дата на скриване:'),
(366, 'uploadfile_linktext', 1, '2023-11-23 21:58:29', '2023-11-23 21:59:04', 'bg', 'Описателен текст:'),
(367, 'uploadfile_file', 1, '2023-11-23 21:59:07', '2023-11-23 21:59:22', 'bg', 'Файл:'),
(368, 'uploadfile_submit', 1, '2023-11-23 21:59:42', '2023-11-23 22:00:00', 'bg', 'Качване на файла'),
(369, 'uploadfile_confdel', 1, '2023-11-23 23:29:40', '2023-11-23 23:30:13', 'bg', 'Найстина ли искате да изтриете този файл?');

-- --------------------------------------------------------

--
-- Структура на таблица `content_history`
--

DROP TABLE IF EXISTS `content_history`;
CREATE TABLE `content_history` (
  `ID` int NOT NULL,
  `date` date NOT NULL,
  `size` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

-- --------------------------------------------------------

--
-- Структура на таблица `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `ID` int NOT NULL,
  `pid` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `date_time_3` datetime NOT NULL,
  `date_time_4` datetime NOT NULL,
  `filename` varchar(255) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `files`
--

INSERT INTO `files` (`ID`, `pid`, `name`, `date_time_1`, `date_time_2`, `date_time_3`, `date_time_4`, `filename`, `text`) VALUES
(2, 1, 'A1', '2023-11-23 23:27:55', '2023-11-23 23:27:55', '0000-01-01 00:00:00', '0000-01-01 00:00:00', '/Users/vanyog/Sites/n/_uploaded_files/site-og-image-1200x630.jpg', '');

-- --------------------------------------------------------

--
-- Структура на таблица `filters`
--

DROP TABLE IF EXISTS `filters`;
CREATE TABLE `filters` (
  `ID` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `filters` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура на таблица `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `ID` int NOT NULL,
  `place` int NOT NULL,
  `group` int NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `menu_items`
--

INSERT INTO `menu_items` (`ID`, `place`, `group`, `name`, `link`) VALUES
(1, 10, 1, 'p1_link', '1'),
(2, 70, 1, 'p2_link', '2'),
(3, 20, 1, 'p3_link', '3'),
(4, 50, 2, 'p4_link', '4'),
(5, 30, 2, 'p5_link', '3'),
(6, 60, 1, 'p6_link', '5'),
(7, 40, 2, 'p7_link', '8');

-- --------------------------------------------------------

--
-- Структура на таблица `menu_tree`
--

DROP TABLE IF EXISTS `menu_tree`;
CREATE TABLE `menu_tree` (
  `ID` int NOT NULL,
  `group` int NOT NULL,
  `parent` int DEFAULT NULL,
  `index_page` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `menu_tree`
--

INSERT INTO `menu_tree` (`ID`, `group`, `parent`, `index_page`) VALUES
(1, 1, 0, 1),
(2, 2, 1, 3);

-- --------------------------------------------------------

--
-- Структура на таблица `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `ID` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Схема на данните от таблица `options`
--

INSERT INTO `options` (`ID`, `name`, `value`) VALUES
(1, 'languages', '$languages = array(\'bg\' => \'Български\', \'en\' => \'English\' );'),
(2, 'default_language', 'bg'),
(3, 'admin_path', 'manage'),
(4, 'adm_name', 'admin'),
(5, 'adm_value', 'on'),
(6, 'edit_name', 'edit'),
(7, 'edit_value', 'on'),
(8, 'host_web', 'vanyog.atwebpages.com'),
(9, 'host_local', 'localhost/n'),
(10, 'phpmyadmin_web', 'http://localhost/phpmyadmin'),
(11, 'phpmyadmin_local', 'http://localhost/phpmyadmin'),
(12, 'mod_path', '_mod'),
(13, 'cache_time', '10'),
(14, 'css_adm_links', 'body { margin-left:40px; }\r\n#adm_links { font-size:150%; line-height:35px; padding:0 4px; opacity:0.1; position:fixed; top:0; left:0; margin:0; background-color:white; width:30px; height:90vh; overflow-y:scroll; overflow-x:hidden; }\r\n#adm_links a, #adm_links span, #adm_links input { white-space:nowrap; float:left; clear:left; }\r\n#adm_links a:hover { text-decoration:underline; }\r\n'),
(15, 'css_all_pages', 'body { font-family: arial, sans-serif; }\r\nh1 { font-size: 2em; }\r\nh2 { font-size: 1.5em }\r\nh3 { font-size: 1.17em }\r\nh4, h5, h6 { font-size: 1em }\r\na { color:#005941; }\r\n.lang_flag { float:left; margin:0 15px 0 0; padding:0; }\r\n.lang_flag img { width:60px; height:30px; vertical-align:middle; }\r\nnav { display:inline-block; min-width: 160px; }\r\nheader { background-color:#005941; color:#fff; padding: 10px; overflow:auto; line-height:50px; }\r\nheader a { color:#fff; text-decoration:none; margin:15px; padding:10px; white-space:nowrap; }\r\nheader span { border: solid 1px; padding:10px; margin:14px; white-space:nowrap; }\r\nheader a:hover { color:#97ff93; }\r\nsection { min-height: calc(100vh - 185px); max-width:870px; margin:10px auto; box-shadow: 1px 1px 5px 0 rgba(50,50,50,0.4); padding:10px; }\r\nfooter { text-align:center; }\r\n#cookies_message { text-align:center; overflow:auto; background-color:#ffeda4; }\r\n.searched { background-color:yellow; }\r\ntd { padding-right:10px; }\r\n#site_search input[type=\"button\"] { height:30px; }\r\n'),
(17, 'css_usermenu', '#user_menu { display:inline-block; position:absolute; top:0; right:0; padding:5px; opacity:0.1; line-height:35px; }\r\n#user_menu:hover { background-color:#fff; opacity:1; }\r\n#user_menu a, #user_menu span, #user_menu div.sep { float:right; clear:right; }\r\n#user_menu div.sep { width:100% }\r\n#user_menu span a { float:none; }\r\n\r\n'),
(18, 'userreg_login_admin', 'index.php?pid=2&user2=login'),
(19, 'userreg_newreg_admin', 'index.php?pid=2&user2=newreg'),
(20, 'userreg_logout_admin', 'index.php?pid=2&user2=logout'),
(23, 'css_o_form', '@media screen and (max-width: 690px) {\r\nform#site_search_form label { display:inline-block; width:65px; height:18px; overflow:hidden; line-height:24px;\r\n}\r\n}\r\n@media screen and (max-width: 365px) {\r\nform#site_search_form label { display:none; }\r\n}\r\n'),
(24, 'css_site_map', 'div#site_map div { margin:25px 0; }\r\ndiv#site_map div div { margin-left:20px; }\r\ndiv#site_map .buttons button { margin:0 15px; }\r\ndiv#site_map .bullet { cursor:pointer; }'),
(25, 'css_site_serarch2', 'div#site_search { display:inline-block; float:right; }\r\ndiv#site_search form { display:inline-block; }\r\ndiv#sResDiv { background-color: white; box-shadow: -0px 0px 10px 3px rgba(50,50,50,0.6); padding: 0 6px; }\r\n'),
(26, 'sitesearch_resultpage', 'index.php?pid=7'),
(27, 'sitemap_1_en_cache', '<div id=\"site_map\">\n<p class=\"buttons\">\r\n<a href=\"\" onclick=\"mapContractExpandAll(1); return false;\">Collapse all</a>\r\n<a href=\"\" onclick=\"mapContractExpandAll(2); return false;\">Expand all</a>\r\n</p>\r\n\n<div id=\"map1\">\n<a href=\"/n/index.php?pid=1\">Home</a><br>\n</div>\n<div id=\"map3\">\n<span onclick=\"mapHideShow(this);\" class=\"bullet\">&#9660;</span>&nbsp;<a href=\"/n/index.php?pid=3\">About</a><br>\n<div id=\"map7\">\n<a href=\"/n/index.php?pid=8\">Авторски права</a><br>\n</div>\n<div id=\"map4\">\n<a href=\"/n/index.php?pid=4\">Personal data</a><br>\n</div>\n</div>\n<div id=\"map6\">\n<a href=\"/n/index.php?pid=5\">Site Map</a>- the current page<br>\n</div>\n<div id=\"map2\">\n<a href=\"/n/index.php?pid=2\">Login</a><br>\n</div>\n<p class=\"buttons\">\r\n<a href=\"\" onclick=\"mapContractExpandAll(1); return false;\">Collapse all</a>\r\n<a href=\"\" onclick=\"mapContractExpandAll(2); return false;\">Expand all</a>\r\n</p>\r\n\r\n<p class=\"clear\"></p></div>'),
(28, 'sitemap_1_bg_cache', '<div id=\"site_map\">\n<p class=\"buttons\">\r\n<button onclick=\"mapContractExpandAll(1);\">Сгъване на всички</button>\r\n<button onclick=\"mapContractExpandAll(2);\">Разгъване на всички</button>\r\n</p>\r\n\n<div id=\"map1\">\n<a href=\"/n/index.php?pid=1\">Начало</a><br>\n</div>\n<div id=\"map3\">\n<span onclick=\"mapHideShow(this);\" class=\"bullet\">&#9660;</span>&nbsp;<a href=\"/n/index.php?pid=3\">Относно</a><br>\n<div id=\"map7\">\n<a href=\"/n/index.php?pid=8\">Авторски права</a><br>\n</div>\n<div id=\"map4\">\n<a href=\"/n/index.php?pid=4\">Лични данни</a><br>\n</div>\n</div>\n<div id=\"map6\">\n<a href=\"/n/index.php?pid=5\">Карта на сайта</a> - текущата страница<br>\n</div>\n<div id=\"map2\">\n<a href=\"/n/index.php?pid=2\">Вход</a><br>\n</div>\n<p class=\"buttons\">\r\n<button onclick=\"mapContractExpandAll(1);\">Сгъване на всички</button>\r\n<button onclick=\"mapContractExpandAll(2);\">Разгъване на всички</button>\r\n</p>\r\n\r\n<p class=\"clear\"></p></div>'),
(29, 'css_menu_tree', 'div#menu_tree { padding:10px; }'),
(30, 'css_p.message', '.message { color:red; }'),
(31, 'sitemap_1_bg_cache_edit', '<div id=\"site_map\">\n<a href=\"/n/index.php?clear=on&amp;pid=5\">Clear cache</a><p class=\"buttons\">\r\n<button onclick=\"mapContractExpandAll(1);\">Сгъване на всички<a href=\"/n/manage/edit_record.php?t=content&amp;r=294\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></button>\r\n<button onclick=\"mapContractExpandAll(2);\">Разгъване на всички<a href=\"/n/manage/edit_record.php?t=content&amp;r=295\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></button>\r\n</p>\r\n\n<div id=\"map1\">\n<a href=\"/n/index.php?pid=1\">Начало</a> place:10 group:1<br>\n</div>\n<div id=\"map3\">\n<span onclick=\"mapHideShow(this);\" class=\"bullet\">&#9660;</span>&nbsp;<a href=\"/n/index.php?pid=3\">Относно<a href=\"/n/manage/edit_record.php?t=content&amp;r=261\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></a> place:20 group:1<br>\n<div id=\"map7\">\n<a href=\"/n/index.php?pid=8\">Авторски права<a href=\"/n/manage/edit_record.php?t=content&amp;r=341\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></a> place:35 group:2<br>\n</div>\n<div id=\"map4\">\n<a href=\"/n/index.php?pid=4\">Лични данни<a href=\"/n/manage/edit_record.php?t=content&amp;r=286\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></a> place:40 group:2<br>\n</div>\n</div>\n<div id=\"map6\">\n<a href=\"/n/index.php?pid=5\">Карта на сайта<a href=\"/n/manage/edit_record.php?t=content&amp;r=292\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></a> - текущата страница<a href=\"/n/manage/edit_record.php?t=content&amp;r=296\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a> place:50 group:1<br>\n</div>\n<div id=\"map2\">\n<a href=\"/n/index.php?pid=2\">Вход<a href=\"/n/manage/edit_record.php?t=content&amp;r=258\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></a> place:60 group:1<br>\n</div>\n<p class=\"buttons\">\r\n<button onclick=\"mapContractExpandAll(1);\">Сгъване на всички<a href=\"/n/manage/edit_record.php?t=content&amp;r=294\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></button>\r\n<button onclick=\"mapContractExpandAll(2);\">Разгъване на всички<a href=\"/n/manage/edit_record.php?t=content&amp;r=295\" style=\"color:#000000;background-color:#ffffff;margin:0;padding:0;\">*</a></button>\r\n</p>\r\n<a href=\"/n/index.php?clear=on&amp;pid=5\">Clear cache</a>\r\n<p class=\"clear\"></p></div>'),
(32, 'css_editrecord_form', 'form[name=editrecord_form] input[type=text], form[name=editrecord_form] input[type=password] \r\n{ width:calc(100% - 10px); }\r\nform[name=editrecord_form] textarea { max-width:calc(100% - 10px); }\r\n'),
(35, 'nav_script', '<script>\r\nwindow.addEventListener(\"resize\", setNavWidth);\r\nwindow.addEventListener(\"load\", setNavWidth);\r\nfunction setNavWidth(){\r\nvar navElement = document.getElementById(\"page_menu\");\r\nvar navPrev = navElement.previousElementSibling;\r\nvar navNext = navElement.nextElementSibling;\r\nvar navMaxW = navNext.getBoundingClientRect().left - navPrev.getBoundingClientRect().right - 30;\r\nif(navMaxW>160) navElement.style.width = navMaxW + \"px\";\r\n}\r\n</script>'),
(36, 'uploadfile_nofilenotext', 'true'),
(37, 'uploadfile_dir', '/n/_uploaded_files/');

-- --------------------------------------------------------

--
-- Структура на таблица `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `ID` int NOT NULL,
  `menu_group` int NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` varchar(50) NOT NULL,
  `template_id` int NOT NULL DEFAULT '1',
  `hidden` tinyint(1) NOT NULL DEFAULT '1',
  `donotcache` tinyint(1) NOT NULL DEFAULT '0',
  `options` varchar(50) DEFAULT NULL,
  `dcount` int NOT NULL DEFAULT '0',
  `tcount` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `pages`
--

INSERT INTO `pages` (`ID`, `menu_group`, `title`, `content`, `template_id`, `hidden`, `donotcache`, `options`, `dcount`, `tcount`) VALUES
(1, 1, 'p1_title', 'p1_content', 1, 0, 0, '', 0, 0),
(2, 1, 'p2_title', 'p2_content', 1, 0, 0, '', 0, 0),
(3, 2, 'p3_title', 'p3_content', 1, 0, 0, NULL, 0, 0),
(4, 2, 'p4_title', 'p4_content', 1, 0, 0, '', 0, 0),
(5, 1, 'p5_title', 'p5_content', 1, 0, 0, NULL, 0, 0),
(6, 1, 'error_404_title', 'error_404_content', 1, 0, 0, '', 0, 0),
(7, 1, 'p7_title', 'p7_content', 1, 0, 0, '', 0, 0),
(8, 2, 'p8_title', 'p8_content', 1, 0, 0, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Структура на таблица `page_cache`
--

DROP TABLE IF EXISTS `page_cache`;
CREATE TABLE `page_cache` (
  `ID` int NOT NULL,
  `page_ID` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `language` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_time_1` datetime NOT NULL,
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `referer` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура на таблица `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `ID` int NOT NULL,
  `user_id` int NOT NULL,
  `type` enum('all','page','menu','module','record') CHARACTER SET cp1251 COLLATE cp1251_bulgarian_ci NOT NULL DEFAULT 'page',
  `object` varchar(20) CHARACTER SET cp1251 COLLATE cp1251_bulgarian_ci NOT NULL,
  `yes_no` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COLLATE=cp1251_bulgarian_ci;

--
-- Схема на данните от таблица `permissions`
--

INSERT INTO `permissions` (`ID`, `user_id`, `type`, `object`, `yes_no`) VALUES
(1, 1, 'all', '', 1),
(2, 1, 'module', 'user', 0);

-- --------------------------------------------------------

--
-- Структура на таблица `scripts`
--

DROP TABLE IF EXISTS `scripts`;
CREATE TABLE `scripts` (
  `ID` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `script` text NOT NULL,
  `coment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `scripts`
--

INSERT INTO `scripts` (`ID`, `name`, `script`, `coment`) VALUES
(1, 'ADMINMENU', 'include_once($idir.\"lib/f_adm_links.php\"); $tx = adm_links();', 'Показва линкове за администриране на сайта'),
(2, 'PAGETITLE', '$tx = translate($page_data[\'title\']);', 'Заглавие на страницата, показвано между таговете <h1></h1>.'),
(3, 'CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[\'content\']);\r\n$page_content = $tx;', 'Показване съдържанието на страницата и ли надпис със зададено име.'),
(4, 'MENU', 'include_once($idir.\"lib/f_menu.php\");\r\n$tx = menu($page_data[\'menu_group\']);', 'Показване на група от хипервръзки (меню)'),
(5, 'BODYADDS', '$tx = $body_adds;', 'Вмъква добавките към <body> тага'),
(6, 'PAGEHEADER', '$tx = $page_header;', 'Вмъква добавките към хедъра на страницата'),
(7, 'HEADTITLE', '$tx = translate($page_data[\'title\'],false);', 'Заглавие на страницата, без линк за редактиране, показвано между таговете <title></title>.'),
(8, 'LANGUAGEFLAGS', '$tx = flags();', 'Показва флагчета за смяна на езика');

-- --------------------------------------------------------

--
-- Структура на таблица `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `ID` int NOT NULL,
  `parent` int DEFAULT NULL,
  `template` text NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `templates`
--

INSERT INTO `templates` (`ID`, `parent`, `template`, `comment`) VALUES
(1, 0, '<!DOCTYPE HTML>\r\n<html lang=\"<!--$$_VARIABLE_default_language_$$-->\">\r\n<head>\r\n  <title><!--$$_HEADTITLE_$$--></title>\r\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=<!--$$_VARIABLE_site_encoding_$$-->\">\r\n  <meta name=viewport content=\"width=device-width, initial-scale=1\">\r\n  <meta name=\"description\" content=\"<!--$$_OGDESCRIPTION_$$-->\">\r\n  <meta property=\"og:type\" content=\"article\">\r\n  <meta property=\"fb:app_id\" content=\"1350744361603908\">\r\n  <meta property=\"og:url\" content=\"<!--$$_VARIABLE_SERVER[\'REQUEST_SCHEME\']_$$-->://<!--$$_VARIABLE_SERVER[\'HTTP_HOST\']_$$--><!--$$_VARIABLE_main_index_$$-->?pid=<!--$$_VARIABLE_page_id_$$-->\">\r\n  <meta property=\"og:image\" content=\"<!--$$_FIRSTIMAGE_http://sci.vanyog.com/_images/200x200-fb.png_$$-->\">\r\n  <meta property=\"og:title\" content=\"<!--$$_HTMLVAR_page_title_$$-->\">\r\n  <meta property=\"og:description\" content=\"<!--$$_OGDESCRIPTION_$$-->\">\r\n<!--$$_SITEICONS_$$-->\r\n<!--$$_VARIABLE_page_header_$$-->\r\n<style>\r\n<!--$$_VARIABLE_added_styles_$$-->\r\n</style>\r\n</head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<header>\r\n<!--$$_LANGUAGEFLAGS_$$-->\r\n<!--$$_MENU_$$-->\r\n<!--$$_SITESEARCH2_$$-->\r\n<!--$$_COOKIES_message_$$-->\r\n</header>\r\n<!--$$_OPTION_nav_script_$$-->\r\n<!--$$_MENUTREE_$$-->\r\n<section>\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n</section>\r\n\r\n<footer>\r\n<p id=\"powered_by\"><!--$$_CONTENT_powered_by_$$--><a href=\"https://github.com/vanyog/mycms/wiki\" target=\"_blank\">VanyoG CMS</a> <!--$$_PAGESTAT_$$--></p>\r\n</footer>\r\n<!--$$_UPLOADFILE_A1,style=\"display:none;\"_$$-->\r\n<!--$$_USERMENU_index.php?pid=2&amp;user2=logout_$$-->\r\n</body>\r\n</html>\r\n\r\n', 'Шаблон по подразбиране');

-- --------------------------------------------------------

--
-- Структура на таблица `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int NOT NULL,
  `creator_id` int NOT NULL DEFAULT '0',
  `type` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_time_0` datetime NOT NULL DEFAULT '0000-01-01 00:00:00',
  `date_time_1` datetime NOT NULL DEFAULT '0000-01-01 00:00:00',
  `date_time_2` datetime NOT NULL DEFAULT '0000-01-01 00:00:00',
  `gdpr` tinyint(1) NOT NULL DEFAULT '0',
  `language` enum('English','Български') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'English',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `newpass` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `aemails` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `code` varchar(40) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `secondname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `thirdname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `country` varchar(2) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `institution` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `telephone` varchar(40) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `IP` varchar(15) CHARACTER SET armscii8 COLLATE armscii8_bin DEFAULT NULL,
  `nomessage` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Схема на данните от таблица `users`
--

INSERT INTO `users` (`ID`, `creator_id`, `type`, `date_time_0`, `date_time_1`, `date_time_2`, `gdpr`, `language`, `username`, `password`, `newpass`, `email`, `aemails`, `code`, `firstname`, `secondname`, `thirdname`, `country`, `institution`, `position`, `address`, `telephone`, `IP`, `nomessage`) VALUES
(1, 0, 'admin', '2023-11-01 22:12:19', '2023-11-01 22:12:19', '2023-11-23 19:14:27', 0, 'English', 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, '', NULL, NULL, '', '', '', 'BG', NULL, NULL, NULL, '', '127.0.0.1', 0);

-- --------------------------------------------------------

--
-- Структура на таблица `visit_history`
--

DROP TABLE IF EXISTS `visit_history`;
CREATE TABLE `visit_history` (
  `ID` int NOT NULL,
  `page_id` int NOT NULL,
  `date` date NOT NULL,
  `count` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура на таблица `who_made_change`
--

DROP TABLE IF EXISTS `who_made_change`;
CREATE TABLE `who_made_change` (
  `ID` int NOT NULL,
  `date_time_1` datetime NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `content_id` int NOT NULL,
  `page_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура на таблица `worktime`
--

DROP TABLE IF EXISTS `worktime`;
CREATE TABLE `worktime` (
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

--
-- Indexes for dumped tables
--

--
-- Индекси за таблица `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `date_time_1` (`date_time_1`),
  ADD KEY `date_time_2` (`date_time_2`),
  ADD KEY `name` (`name`),
  ADD KEY `language` (`language`);
ALTER TABLE `content` ADD FULLTEXT KEY `text` (`text`);

--
-- Индекси за таблица `content_history`
--
ALTER TABLE `content_history`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `date` (`date`);

--
-- Индекси за таблица `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `pid` (`pid`),
  ADD KEY `name` (`name`),
  ADD KEY `filename` (`filename`);

--
-- Индекси за таблица `filters`
--
ALTER TABLE `filters`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`);

--
-- Индекси за таблица `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `group` (`group`);

--
-- Индекси за таблица `menu_tree`
--
ALTER TABLE `menu_tree`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `group` (`group`);

--
-- Индекси за таблица `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`);

--
-- Индекси за таблица `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`ID`);

--
-- Индекси за таблица `page_cache`
--
ALTER TABLE `page_cache`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `page_ID` (`page_ID`),
  ADD KEY `page_ID_2` (`page_ID`),
  ADD KEY `language` (`language`),
  ADD KEY `date_time_1` (`date_time_1`);
ALTER TABLE `page_cache` ADD FULLTEXT KEY `text` (`text`);

--
-- Индекси за таблица `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`ID`);

--
-- Индекси за таблица `scripts`
--
ALTER TABLE `scripts`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индекси за таблица `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`ID`);

--
-- Индекси за таблица `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `password` (`password`),
  ADD KEY `date_time_0` (`date_time_0`),
  ADD KEY `date_time_1` (`date_time_1`),
  ADD KEY `date_time_3` (`date_time_2`),
  ADD KEY `code` (`code`),
  ADD KEY `type` (`type`);
ALTER TABLE `users` ADD FULLTEXT KEY `position` (`position`);

--
-- Индекси за таблица `visit_history`
--
ALTER TABLE `visit_history`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `date` (`date`),
  ADD KEY `count` (`count`);

--
-- Индекси за таблица `who_made_change`
--
ALTER TABLE `who_made_change`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `is_admin` (`is_admin`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `date_time_1` (`date_time_1`);

--
-- Индекси за таблица `worktime`
--
ALTER TABLE `worktime`
  ADD PRIMARY KEY (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=370;

--
-- AUTO_INCREMENT for table `content_history`
--
ALTER TABLE `content_history`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `filters`
--
ALTER TABLE `filters`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_tree`
--
ALTER TABLE `menu_tree`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `page_cache`
--
ALTER TABLE `page_cache`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scripts`
--
ALTER TABLE `scripts`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visit_history`
--
ALTER TABLE `visit_history`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `who_made_change`
--
ALTER TABLE `who_made_change`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

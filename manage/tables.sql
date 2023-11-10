-- phpMyAdmin SQL Dump
-- version 5.2.0-rc1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Време на генериране: 10 ное 2023 в 00:26
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

CREATE TABLE `content` (
  `ID` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `nolink` tinyint(1) NOT NULL DEFAULT '0',
  `date_time_1` datetime NOT NULL,
  `date_time_2` datetime NOT NULL,
  `language` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'bg',
  `text` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `content`
--

INSERT INTO `content` (`ID`, `name`, `nolink`, `date_time_1`, `date_time_2`, `language`, `text`) VALUES
(1, 'home_page_title', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Начална страница'),
(2, 'home_page_title', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Home Page'),
(3, 'home_page_content', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '<p>Текст на страницата.</p>'),
(4, 'home_page_content', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', '<p>Content of the Homa Page.</p>'),
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
(34, 'user_homеpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Началната страница'),
(35, 'user_homеpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Home page'),
(36, 'user_institution', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Месторабота:'),
(37, 'user_institution', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Institution:'),
(38, 'user_lastpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Предишната страница'),
(39, 'user_lastpage', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Previous Page'),
(40, 'user_logaut', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Изход'),
(41, 'user_logaut', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Logout'),
(42, 'user_login', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Влизане в системата'),
(43, 'user_login', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'User login'),
(44, 'user_login_button', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', 'Влизане'),
(45, 'user_login_button', 1, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'en', 'Log in'),
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
(102, 'p2_title', 0, '2023-11-01 09:14:59', '2023-11-01 22:20:07', 'bg', 'Редактиране на потребител'),
(103, 'p2_content', 0, '2023-11-01 09:14:59', '2023-11-01 09:14:59', 'bg', '<!--$$_USER_edit_$$-->'),
(104, 'pagestat_total', 0, '2023-11-01 09:34:03', '2023-11-01 09:34:32', 'bg', 'Посещения на страницата: общо '),
(105, 'pagestat_today', 0, '2023-11-01 09:34:36', '2023-11-01 09:34:42', 'bg', ' днес '),
(106, 'powered_by', 0, '2023-11-01 11:15:28', '2023-11-01 11:16:10', 'en', 'Powered by '),
(107, 'powered_by', 0, '2023-11-01 11:16:19', '2023-11-01 17:38:35', 'bg', 'Направено с '),
(108, 'home_page_title', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Начална страница'),
(109, 'home_page_title', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Home Page'),
(110, 'home_page_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>Текст на страницата.</p>'),
(111, 'home_page_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>Content of the Homa Page.</p>'),
(112, 'error_404_title', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Грешен номер на страница'),
(113, 'error_404_title', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Incorrect page number'),
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
(152, 'user_login_button', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Log in'),
(153, 'user_logoutcontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<p>Вие успешно излязохте от системата</p>'),
(154, 'user_logoutcontent', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', '<p>You have successfully logged out of the system</p>'),
(155, 'user_logouttitle', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Изход от системата'),
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
(202, 'usermenu_newpagetitle', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Heading:'),
(203, 'usermenu_texttoedit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Текст:'),
(204, 'usermenu_texttoedit', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', 'Text:'),
(205, 'menu_start_1', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', ''),
(206, 'menu_start_1', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', ''),
(207, 'admin_style', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', ''),
(208, 'admin_style', 1, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'en', ''),
(209, 'p2_title', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', 'Страница за администриране'),
(210, 'p2_content', 0, '2023-11-01 12:23:46', '2023-11-01 12:23:46', 'bg', '<!--$$_USER_edit_$$-->'),
(211, 'pagestat_total', 0, '2023-11-01 12:25:02', '2023-11-01 12:27:14', 'en', 'Page is visited: in total '),
(212, 'pagestat_today', 0, '2023-11-01 12:26:22', '2023-11-01 12:28:23', 'en', ', today '),
(213, 'userreg_nouserlogedin', 0, '2023-11-01 20:06:12', '2023-11-01 20:06:31', 'bg', 'Няма влязъл потребител'),
(214, 'userreg_new', 0, '2023-11-01 21:19:27', '2023-11-01 21:19:40', 'bg', 'Нов потребител'),
(215, 'userreg_create', 0, '2023-11-01 21:20:44', '2023-11-01 21:21:11', 'bg', 'Създаване на потребителя');

-- --------------------------------------------------------

--
-- Структура на таблица `filters`
--

CREATE TABLE `filters` (
  `ID` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `filters` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура на таблица `menu_items`
--

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
(1, 10, 1, 'p1_link', '1');

-- --------------------------------------------------------

--
-- Структура на таблица `menu_tree`
--

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
(1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Структура на таблица `options`
--

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
(8, 'host_web', 'mysite.org'),
(9, 'host_local', 'localhost'),
(10, 'phpmyadmin_web', 'http://localhost/phpmyadmin'),
(11, 'phpmyadmin_local', 'http://localhost/phpmyadmin'),
(12, 'mod_path', '_mod'),
(13, 'cache_time', '10'),
(14, 'css_adm_links', '#adm_links { font-size:200%; opacity:0.1; position:fixed; top:0; margin:0; background-color:white; width:30px; height:100vh; overflow-y:scroll; overflow-x:hidden; }\r\n#adm_links:hover { opacity:1; width:auto; }\r\n#adm_links a { display:block; }'),
(15, 'css_all_pages', 'body { font-family: arial, sans-serif; }\r\na { text-decoration:none; }\r\n.lang_flag img { width:40px; height:20px; vertical-align:middle; }\r\nnav { display:inline-block; }\r\nheader a, header span { margin:0 5px; }\r\nheader { background-color:#00468e; color:#fff; padding: 10px; }\r\nsection { min-height:77vh; max-width:870px; margin:10px auto; box-shadow: 1px 1px 5px 0 rgba(50,50,50,0.4); padding:10px; }\r\n'),
(17, 'css_usermenu', '#user_menu { display:inline-block; position:absolute; top:0; right:0; padding:5px; opacity:0.1; }\r\n#user_menu:hover { background-color:#fff; opacity:1; }\r\n#user_menu a { display:block; }\r\n');

-- --------------------------------------------------------

--
-- Структура на таблица `pages`
--

CREATE TABLE `pages` (
  `ID` int NOT NULL,
  `menu_group` int NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` varchar(50) NOT NULL,
  `template_id` int NOT NULL DEFAULT '1',
  `hidden` tinyint(1) NOT NULL DEFAULT '1',
  `options` varchar(50) DEFAULT NULL,
  `dcount` int NOT NULL DEFAULT '0',
  `tcount` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Схема на данните от таблица `pages`
--

INSERT INTO `pages` (`ID`, `menu_group`, `title`, `content`, `template_id`, `hidden`, `options`, `dcount`, `tcount`) VALUES
(1, 1, 'home_page_title', 'home_page_content', 1, 0, '', 0, 0),
(2, 1, 'p2_title', 'p2_content', 1, 1, '', 0, 0);

-- --------------------------------------------------------

--
-- Структура на таблица `page_cache`
--

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
(1, 1, 'all', '', 1);

-- --------------------------------------------------------

--
-- Структура на таблица `scripts`
--

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
(3, 'CONTENT', 'if (isset($tg[1])) $tx = translate($tg[1]);\r\nelse $tx = translate($page_data[\'content\']);', 'Показване съдържанието на страницата и ли надпис със зададено име.'),
(4, 'MENU', 'include_once($idir.\"lib/f_menu.php\");\r\n$tx = menu($page_data[\'menu_group\']);', 'Показване на група от хипервръзки (меню)'),
(5, 'BODYADDS', '$tx = $body_adds;', 'Вмъква добавките към <body> тага'),
(6, 'PAGEHEADER', '$tx = $page_header;', 'Вмъква добавките към хедъра на страницата'),
(7, 'HEADTITLE', '$tx = translate($page_data[\'title\'],false);', 'Заглавие на страницата, без линк за редактиране, показвано между таговете <title></title>.'),
(8, 'LANGUAGEFLAGS', '$tx = flags();', 'Показва флагчета за смяна на езика');

-- --------------------------------------------------------

--
-- Структура на таблица `templates`
--

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
(1, 0, '<!DOCTYPE HTML>\r\n<html lang=\"<!--$$_VARIABLE_default_language_$$-->\">\r\n<head>\r\n  <title><!--$$_HEADTITLE_$$--></title>\r\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=<!--$$_VARIABLE_site_encoding_$$-->\">\r\n  <meta name=viewport content=\"width=device-width, initial-scale=1\">\r\n<!--$$_VARIABLE_page_header_$$-->\r\n<style>\r\n<!--$$_VARIABLE_added_styles_$$-->\r\n</style>\r\n</head>\r\n<body<!--$$_BODYADDS_$$-->>\r\n<!--$$_ADMINMENU_$$-->\r\n\r\n<header>\r\n<!--$$_LANGUAGEFLAGS_$$-->\r\n<!--$$_MENU_$$-->\r\n</header>\r\n<section>\r\n<h1><!--$$_PAGETITLE_$$--></h1>\r\n<!--$$_CONTENT_$$-->\r\n</section>\r\n\r\n<footer>\r\n<p id=\"powered_by\"><!--$$_CONTENT_powered_by_$$--><a href=\"https://github.com/vanyog/mycms/wiki\" target=\"_blank\">MyCMS</a> <!--$$_PAGESTAT_$$--></p>\r\n</footer>\r\n\r\n<!--$$_USERMENU_/index.php?pid=2&amp;user2=logout_$$-->\r\n</body>\r\n</html>\r\n\r\n', 'Шаблон по подразбиране');

-- --------------------------------------------------------

--
-- Структура на таблица `users`
--

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
(1, 0, '', '2023-11-01 22:12:19', '2023-11-01 22:12:19', '0000-01-01 00:00:00', 0, 'English', 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, '', NULL, NULL, '', '', '', 'BG', NULL, NULL, NULL, '', '127.0.0.1', 0);

-- --------------------------------------------------------

--
-- Структура на таблица `visit_history`
--

CREATE TABLE `visit_history` (
  `ID` int NOT NULL,
  `page_id` int NOT NULL,
  `date` date NOT NULL,
  `count` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура на таблица `worktime`
--

CREATE TABLE `worktime` (
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `time` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=armscii8 COLLATE=armscii8_bin;

--
-- Схема на данните от таблица `worktime`
--

INSERT INTO `worktime` (`name`, `time`) VALUES
('USAGE.txt', 1933),
('content.102', 180),
('content.104', 29),
('content.105', 6),
('content.106', 42),
('content.107', 29),
('content.211', 80),
('content.212', 61),
('content.213', 19),
('content.214', 13),
('content.215', 27),
('mod/userreg/new_user.php', 3843),
('options.1', 19),
('options.10', 12),
('options.15', 2530),
('options.16', 213),
('options.17', 994),
('options.8', 78),
('options.9', 18),
('templates.1', 854);

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
  ADD PRIMARY KEY (`ID`);

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
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `filters`
--
ALTER TABLE `filters`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_cache`
--
ALTER TABLE `page_cache`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_tree`
--
ALTER TABLE `menu_tree`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `page_cache`
--
ALTER TABLE `page_cache`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visit_history`
--
ALTER TABLE `visit_history`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

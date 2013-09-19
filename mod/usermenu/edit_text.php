<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Редактиране на текст, след щракване върху линка * зад този текст в режим на редактиране

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");
include_once($idir."lib/translation.php");
include_once($idir."lib/f_edit_record_form.php");

// Номер на страницата, на която е текста
$page_id = 1*$_GET['pid'];

// Данни за страницата
$page_data = db_select_1('*', 'pages', "`ID`=$page_id");

// Проверка на правата на потребителя
usermenu(true);

// Край ако няма право да редактира
if (!$can_edit) die('You have no permission to edit this text');

$page_header = '<link href="'.$pth.'_style.css" rel="stylesheet" type="text/css">'."\n";

$cp = array(
'ID' => 1*$_GET['i'],
'text' => translate('usermenu_texttoedit')
);

$page_content = '<h1>'.translate('usermenu_edittext')."</h1>\n";

// Обработване на изпратени данни
if (count($_POST)){ 
  $page_content .= process_record($cp, 'content');
  header('Location: '.$_SESSION['http_referer']);
}
else if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];

// Форма за редактиране на текста
$page_content .= edit_record_form($cp, 'content');

$pt = $_SESSION['http_referer'];

$page_content .= '<p><a href="'.$pt.'">'.translate('usermenu_back').'</a></p>';

include($idir."lib/build_page.php");

?>

<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2020  Vanyo Georgiev <info@vanyog.com>

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


// Изтриване на потребител чрез изпращане на код, равен на SHA2(`email`,224)

if(!isset($_GET['code'])) die('Nothing to do.');

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_delete_from.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_set_self_query_var.php');

// Четене на записа на потребителя с кодирания имейл
$d = db_select_1('*', 'users', "SHA2(`email`,224)='".addslashes($_GET['code'])."'");

// Ако не се открие такъв - изход
if(!$d) die('Incorrect code.');

// Език на потребитела. На този език се показва страницата
$language = array_search($d['language'], $languages);

// Заглавие на страницата
$page_title = translate('userreg_delEmailTitle');

$page_content = "<h1>$page_title</h1>
<p>".$d['email']."</p>\n";

// Ако потребителат има име и парола
if($d['username'] && $d['password']){
  $lp = stored_value('userreg_login_'.$d['type']);
  // се показва надпис, че трябва да влезе в профила си
  $page_content .= '<p>'.translate('userreg_delEmailLogin')." <a href=\"$lp&lang=$language\">$lp</a></p>\n";
}
else{ // Иначе:
  // Ако питребителят е щракнал линка, че потвърждава изтриването
  if(isset($_GET['confirm']) && ($_GET['confirm']=='yes')) {
    // Изтриване
    db_delete_from('users', $d['ID']);
    // Показване на надпис, че профилът е изтрит
    $page_content .= '<p><b>'.translate('userreg_delEmailDone')."</b></p>\n";
    // и линк "продължаване" към началната страница на сайта
    $page_content .= '<p><a href="/">'.translate('userreg_delEmailContinue')."</a></p>\n";
  }
  else {
    // Показване на линк за потвърждаване на изтриването
    $page_content .= '<p><a href="'.set_self_query_var('confirm','yes').'">'.translate('userreg_delEmailYes')."</a></p>\n";
    // и линк "отказ", който препраща към началната страница на сейта.
    $page_content .= '<p><a href="/">'.translate('userreg_delEmailNo')."</a></p>\n";
  }
}

include_once($idir.'lib/build_page.php');

?>

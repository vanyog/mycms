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

include_once($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_db_table_field.php');

// Функцията user() проверява дали има влязъл с парола потребител.
// Ако няма такъв, показва форма за влизане.
// Ако потребителското име или паролата са невалидни връща "Access denied."
// Ако потребителското име и паролата са валидни връща празен низ.
// Ако предаденията на функцията параметър $a има непразна стойност,
// се извършват други действие относно потребителите

session_start();

function user($a = ''){
// Ако е изпратен непразен параметър $a се, извикват други функции
switch ($a){
case 'new': return create_user(); // Създаване на нов потребител
}
// Ако няма влязъл потребител се отваря страница за влизане
if (!isset($_SESSION['user_username'])) get_user();
// Извлича се номера на потребителя от базата данни
$rz = db_table_field('ID','users',
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");
// Ако в базата данни няма такива потребителско име и пароле - Access denied
if (!$rz) { session_destroy(); header("Status: 403"); die("Access denied."); }
return '';
}

// Функцията get_user() връща HTML код с форма за влизане на потребител

function get_user(){
// Ако формата за влизане вече е попълнена, се обработват изпратените с нея данни
if (isset($_POST['username'])){ process_user(); return; }
global $idir;
$guf = new HTMLForm('login_form');
$guf->add_input( new FORMInput(translate('user_username'),'username','text') );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
$guf->add_input( new FORMInput('','','submit',translate('user_login_button')) );
$page_title = translate('user_login');
$page_content = "<h1>$page_title</h1>".$guf->html().'';
include($idir.'lib/build_page.php');
die;
}

// Функцията process_user() обработва данните за влизане на потребителя - 
// присвоява ги на съответните променливи на сесията

function process_user(){
$_SESSION['user_username'] = $_POST['username'];
if (isset($_POST['password'])) $_SESSION['user_password'] = sha1($_POST['password']); else $_SESSION['user_password'] = '';
}

// Функцията create_user() връща HTML код с форма за създаване на нов потребител

function create_user(){
// Ако формата за създаване на нов потребител вече е попълнена, се обработват изпратените с нея данни
$r = ''; // Съобщение за евентуална нередност
$u = ''; // Име на потребител
if (isset($_POST['username'])){ 
  $r = process_new_user(); // Резултат от обработката
  $u = $_POST['username'];
  if ($r=='ok') return translate('user_created')." $u";
}
global $idir;
$guf = new HTMLForm('newuser_form');
$guf->add_input( new FORMInput(translate('user_username'),'username','text',$u) );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
$guf->add_input( new FORMInput(translate('user_password2'),'password2','password') );
$guf->add_input( new FORMInput('','','submit',translate('user_new_button')) );
return $r.$guf->html();
}

// Функцията process_new_user() обработва данните за създаване на нов потребител

function process_new_user(){
global $tn_prefix, $db_link;
// Проверява се дали потребителското име съществува
$u = addslashes($_POST['username']);
$i = db_table_field('ID','users',"`username`='$u'");
if ($i) return translate('user_exists');
// Проверява се дали двете въвеждания на паролата съвпадат
if ($_POST['password']!=$_POST['password2']) return translate('user_diff_pass');
// SQL заявка за вмъкване на нов запис
$q = "INSERT INTO `$tn_prefix"."users` SET `date_time_0`=NOW(), `date_time_1`=NOW(), `username`='$u', `password`='".
     sha1($_POST['password'])."';";
// Изпълнение на заявката
mysql_query($q,$db_link);
// Връщане на резултат 'ok'
return 'ok'; 
}

?>

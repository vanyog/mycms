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
include_once($idir.'lib/f_set_self_query_var.php');
include_once($idir.'lib/f_unset_self_query_var.php');
include_once($idir.'lib/f_edit_record_form.php');

// Функцията user() проверява дали има влязъл с парола потребител.
// Ако няма такъв, показва форма за влизане и
// ако потребителското име или паролата са невалидни предизвиква "Access denied."
// Ако потребителското име и паролата са валидни връща препратка "Изход".
// Ако е изпратен параметър $_GET['user']='newreg' се показва форма за въвеждане на данни за нов потребител,
// при условия, че има влязъл потребител с право да създава други потребители.
// Ако е изпратен параметър $_GET['user']='logout' се предизвиква излизане на потребителя. 
// $a = 'login' означава, че страницата, от която се вика user, е специална страница за влизане и след успешно
// влизане става препращане към адрес stored_value('user_loginpage',''), ако е зададен такъв.
// $a = 'edit' означава, че страницата, от която се вика user, е страница за редактиране данните за
// потребителя и връща форма за редактиране на тези данни.
// $a = 'enter' означава, ако няма влязъл потребител да се показва линк "Вход".
// $a = 'create' връща форма за създаване на нов потребител, при условие, че има влязъл потребите,
// с право да създава други потребители.

if (!session_id()) session_start();


function user($a = ''){
global $tn_prefix, $db_link, $user_table;
//if (show_adm_links()) return '';
// Ако е натиснат линк "Изход"
if (isset($_GET['user'])&&($_GET['user']=='logout')) logout_user();
$rz = '';
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
// $c - брой на записите с потребители в таблица $user_table
$c = db_table_field('COUNT(*)',$user_table,'1');// print_r($c); die;
// Грешка - значи няма таблица $user_table.
if ($c===false) die("Table '$user_table' is not set up.");
// Ако няма потребители, се създава нов потребител.
if ( !$c && (!isset($_GET['user']) || ($_GET['user']!='newreg')) ){ return new_user($a); }
// Ако няма влязъл потребител се отваря страница за влизане.
if (!isset($_SESSION['user_username'])){
  // но ако $a == 'enter' се показва хипервръзка "Вход"
  // освен ако не е изпратен параметър user=enter или все още няма регистрирани потребители.
  if ( ($a == 'enter') && $c && (!isset($_GET['user'])||($_GET['user']!='enter')) ) return enter_link();
  $rz = get_user($a,$c);
  if ($rz) return $rz;
}
// Четене на номера на потребител с име $_SESSION['user_username'] и парола $_SESSION['user_password'].
$rz = db_select_1('ID',$user_table,
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");
// Ако няма такъв потребител - Access denied
if (!$rz) { session_destroy(); header("Status: 403"); die("Access denied."); }
else{
  // Ако се редактират данните на потребителя.
  if ($a == 'edit') return edit_user($rz['ID']);
  // Отбилязване часа на влизане
  $tm = date('Y-m-d H:m:s', $_SESSION['session_start']);
  mysqli_query($db_link, "UPDATE `$tn_prefix"."users` SET `date_time_2`='$tm' WHERE `ID`=".$rz['ID'].";");
  // Ако е изпратен параметър за създаване на нов потребител.
  if ( ($a=='create') || (isset($_GET['user']) && ($_GET['user']=='newreg')) ) create_user();
  // Адрес на страницата, на която да се отиде след влизане.
  $lp = stored_value('user_loginpage',''); 
  // Ако е зададена се извършва препращане.
  if ($lp && ($a=='login')){
    header("Location: $lp");
    die;
  }
  // Ако не е зададена се връща линк "Изход".
  else $rz = '<span class="user">'.$_SESSION['user_username'].
       ' <a href="'.set_self_query_var('user','logout').'">'.translate('user_logaut').'</a></span>';
}
return $rz;
}

// Функцията get_user() връща HTML код с форма за влизане/регистриране на потребител

function get_user($a,$c){
// Ако формата за влизане вече е попълнена, се обработват изпратените с нея данни
if (isset($_POST['username'])){ process_user(); return ''; }
global $idir;
// Заглавие на страницата за влизане/създаване на потребител
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg')) $page_title = translate('user_newreg');
else $page_title = translate('user_login');
// Ако няма още нито един потребител - надпис, който съобщава това
$m = '';
if (!$c) $m = translate('user_firstuser');
// Съдържание на страницата
$page_content = '<div id="user_login">'."\n<h1>$page_title</h1>\n$m\n".user_form($c)->html();
if (stored_value('user_showreglink', 'false')=='true')
   $page_content .= '<p><a href="'.set_self_query_var('user','newreg').'">'.translate('user_newreg')."</a></p>";
$page_content .= "\n</div>";
// Ако страницата не се вмъква в шаблон се показва с build_page.php,
if ($a != 'login'){ include($idir.'lib/build_page.php'); die; }
// иначе се връща нейното съдържание, за да се вмъкне в шаблона.
else return $page_content;
}

// Функцията process_user() обработва данните за влизане на потребителя - 
// присвоява ги на съответните променливи на сесията

function process_user(){
if (isset($_POST['password2'])) save_user();
$_SESSION['user_username'] = $_POST['username'];
if (isset($_POST['password'])) $_SESSION['user_password'] = pass_encrypt($_POST['password']); else $_SESSION['user_password'] = '';
$_SESSION['session_start'] = time();
// Премахване на параметър $_GET['user']=='enter' и презареждане на страницата
if (isset($_GET['user'])&&($_GET['user']=='enter')){
  $l = unset_self_query_var('user',true); //echo $l; die;
  header('Location: '.$l);
}
}

// Кодиране на паролата по един от два начина

function pass_encrypt($p){
if (stored_value('user_mysqlpass','')=='yes') return '*'.strtoupper(sha1(sha1($p,true)));
else return sha1($p);
}

// Запазване на данните за нов потребител

function save_user(){
global $tn_prefix, $db_link;
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
if ( !isset($_GET['user']) || ($_GET['user']!='newreg') || ($_POST['password2']!=$_POST['password']) || !$_POST['username'] )
   return;
$u = db_table_field('username', $user_table, "`username`='".addslashes($_POST['username'])."'");
if ($u) return;
$q = "INSERT INTO `$tn_prefix".
     "users` SET `date_time_0`=NOW(), `date_time_1`=NOW(), `username`= '".addslashes($_POST['username']).
     "', `password`='".pass_encrypt($_POST['password'])."';";
return mysqli_query($db_link,$q);
}

// Връща обект форма за влизане/регистриране на потребител

function user_form($c){
$guf = new HTMLForm('login_form');
$guf->add_input( new FORMInput(translate('user_username'),'username','text') );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg'))
  $guf->add_input( new FORMInput(translate('user_passwordconfirm'),'password2','password') );
$guf->add_input( new FORMInput('','','submit',translate('user_login_button')) );
return $guf;
}


// Унищожава сесията и пренасочва към страницата след излизане

function logout_user(){
// Адрес на страницата, която се показва след излизане
$lp = current_pth(__FILE__).'logout.php';
// Евентуално в настройките може да е зададена друга 
$lp = stored_value('user_logoutpage',$lp); //print_r($lp); die;
// Прекратяване на сесията
session_destroy();
// Пренасочване към страницата след излизане
header("Location: $lp");
}

// Връща форма за редактиране данните на потребителя

function edit_user($id){
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
$cp = array(
'ID'=>$id,
'username'=>translate('user_username'),
'password'=>translate('user_password'),
'email'=>translate('user_email'),
'firstname'=>translate('user_firstname'),
'secondname'=>translate('user_secondname'),
'thirdname'=>translate('user_thirdname'),
'telephone'=>translate('user_telephone')
);
$rz = '';
if (count($_POST)) $rz .= process_record($cp, $user_table);
return $rz.edit_record_form($cp, $user_table);
}

//
// Функция new_user() се извиква, когато все още няма нито един потребител.
// Тя презарежда страницата, като добавя в адреса и праметър user=newreg и това предизвиква показване на форма за 
// редактиране на нов потребител.
//
function new_user($a){// print_r($_SESSION); die;
  // Ако трябва да се показва само линк "Вход", се връща този линк
  if (($a=='enter')&&!(isset($_GET['user'])&&($_GET['user']=='enter'))) return enter_link();
  // В противен случай се изпраща параметър user=newreg и страницата се презарежда
  $l = set_self_query_var('user','newreg',false);
  header("Location: $l");
}

//
// Функция create_user() се изпълнява, когато в адреса на страницата има параметър user=newreg
// за да покаже форма за създаване на нов потребител.
//
function create_user(){
if (count($_POST)) save_user();
global $idir;
// Име на таблизата с данни за потребители
$user_table = stored_value('user_table','users');
// Номер на потребителя
$i = db_table_field('ID',$user_table,"`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
// Проверка дали потребителят има всички права
$p = db_table_field('yes_no','permissions',"`type`='all' AND `user_id`=$i");
// Ако няма всички права, проверка дали няма право над модул user
if (!$p) $p = db_table_field('yes_no','permissions',"`type`='module' AND `object`='user' AND `user_id`=$i");
// Ако няма и това право - надпис
if (!$p) die(translate('user_cnnotcreate'));
$page_title = translate('user_newreg');
$page_content = '<div id="user_login">'."\n<h1>$page_title</h1>\n".user_form(0)->html();
include($idir.'lib/build_page.php');
die;
}

// Функция, която връща html код на хипервръзка "Вход"
//
function enter_link(){
// Адрес на страницата за влизане
$ep = stored_value('user_loginpage');
if (!$ep) $ep = set_self_query_var('user','enter');
return '<a href="'.$ep.'">'.translate("user_enter").'</a>';
}

?>

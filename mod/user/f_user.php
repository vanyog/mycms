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

global $can_visit;

include_once($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_delete_from.php');
include_once($idir.'lib/f_set_self_query_var.php');
include_once($idir.'lib/f_unset_self_query_var.php');
include_once($idir.'lib/f_edit_record_form.php');

// Функцията user() проверява дали има влязъл с парола потребител.
// Ако няма такъв, показва форма за влизане и
// ако потребителското име или паролата не са валидни предизвиква "Access denied."
// Ако потребителското име и паролата са валидни връща потребителското име и препратка "Изход".

// След успешното разпознаване на потребител, функцията задава стойност на $can_visit според
// зададените в таблица pеrmissions права и стойността на настройката user_can_visit,
// така че, дори и да е влязъл, потребителят може да види резултат Access denied,
// ако няма други права за работа със страницата.

// $_GET['user']='newreg' - показва форма за въвеждане на данни за нов потребител,
// при условие, че има влязъл потребител с право да създава други потребители. (`type`='module', `object`='user')
// $_GET['user']='logout' - предизвиква излизане на потребителя.
// $_GET['user']='delete' - показва форма за изтриване на потребител.

// $a = 'login' означава, че страницата, от която се вика user, е специална страница за влизане и след успешно
// влизане става препращане към адрес stored_value('user_loginpage',''), ако е зададен такъв.
// $a = 'edit' означава, че страницата, от която се вика user, е страница за редактиране данните за
// потребителя и връща форма за редактиране на тези данни.
// $a = 'enter' означава, ако няма влязъл потребител да се показва линк "Вход".
// $a = 'create' връща форма за създаване на нов потребител, при условие, че има влязъл потребите,
// с право да създава други потребители.

function user($a = ''){

global $tn_prefix, $db_link, $user_table, $mod_apth, $can_visit;

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

if (!session_id()) session_start();

// Ако няма влязъл потребител се отваря страница за влизане.
if (!isset($_SESSION['user_username'])){
  // но ако $a == 'enter' се показва хипервръзка "Вход"
  // освен ако не е изпратен параметър user=enter или все още няма регистрирани потребители,
  // тогава също се показва форма за влизане.
  if ( ($a == 'enter') && $c && (!isset($_GET['user'])||($_GET['user']!='enter')) ) return enter_link();
  $rz = get_user($a,$c);
  if ($rz) return $rz;
}

// Четене на номера на потребител с име $_SESSION['user_username'] и парола $_SESSION['user_password'].
$rz = db_select_1('ID',$user_table,
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");

// Ако няма такъв потребител - пробване на други функции за влизане
if (!$rz) {
  // Четене на списъка с други функции за четене данни на потребители
  $ts = stored_value('user_types');
  if ($ts){
    $ta = explode(',',$ts);
    foreach($ta as $f){
      $fn = $mod_apth.'f_'.$f.'.php';
      if (file_exists($fn)){
         include_once($fn);
         eval('$rz = '."$f();");
         if ($rz){ $can_visit = stored_value('user_can_visit',false); break; }
      }
    }
  }
  // Ако никоя функция не е намерила номер на валиден потребител - Access denied.
  if (!$rz) { session_destroy(); header("Status: 403"); die("Access denied by user module."); } 
}
else{
  // Ако се редактират данните на потребителя.
  if ($a == 'edit') return edit_user($rz['ID']);

  // Отбелязване на часа и IP адреса на влизане
  if (!isset($_SESSION['session_start'])) $_SESSION['session_start'] = time();
  $tm = date('Y-m-d H:m:s', $_SESSION['session_start']);
  $q = "UPDATE `$tn_prefix"."$user_table` SET `date_time_2`='$tm', `IP`='".$_SERVER['REMOTE_ADDR'].
       "' WHERE `ID`=".$rz['ID'].";";
  mysqli_query($db_link, $q);

  // Ако е изпратен параметър за създаване на нов потребител.
  if ( ($a=='create') || (isset($_GET['user']) && ($_GET['user']=='newreg')) ) return create_user($a);

  // Ако е изпратен параметър за изтриване на потребител.
  if ( ($a=='delete') || (isset($_GET['user']) && ($_GET['user']=='delete')) ) delete_user($a);

  // Адрес на страницата, на която да се отиде след влизане.
  $lp = stored_value('user_loginpage','');

  // Ако е зададена се извършва препращане.
  if ($lp && ($a=='login')){
    header("Location: $lp");
    die;
  }
  else{ // Ако не е зададена се връща линк "Изход".
    $can_visit = user_can_visit($rz['ID']);
    $rz = user_logout_link();
  }
}
return $rz;
} // function user($a = '')

//
// Връща потребителското име на влезлия потребител и линк "Изход"

function user_logout_link(){
return '<span class="user">'.$_SESSION['user_username'].
       ' <a href="'.set_self_query_var('user','logout').'">'.translate('user_logaut').'</a></span>';
}

//
// Функцията get_user() връща HTML код с форма за влизане/регистриране на потребител
//

function get_user($a,$c){
// Ако формата за влизане вече е попълнена, се обработват изпратените с нея данни
if (isset($_POST['username'])){ process_user(); return ''; }
global $idir, $site_encoding;
// Заглавие на страницата за влизане/създаване на потребител
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg')) $page_title = translate('user_newreg');
else $page_title = translate('user_login');
// Ако няма още нито един потребител - надпис, който съобщава това
$m = '';
if (!$c) $m = translate('user_firstuser');
// Съдържание на страницата
$page_content = '<div id="user_login">'."\n".user_form($c,"<h1>$page_title</h1>\n$m\n")->html();
if (stored_value('user_showreglink', 'false')=='true')
   $page_content .= '<p><a href="'.set_self_query_var('user','newreg').'">'.translate('user_newreg')."</a></p>";
$page_content .= "\n</div>";
// Ако страницата не се вмъква в шаблон се показва с build_page.php,
if ($a != 'login'){ include($idir.'lib/build_page.php'); die; }
// иначе се връща нейното съдържание, за да се вмъкне в шаблона.
else return $page_content;
}

// Функцията process_user() обработва данните за влизане на потребителя - 
// присвоява ги на съответните променливи на сесията и презарежда страницата

function process_user(){
if (isset($_POST['password2'])) save_user();
$_SESSION['user_username'] = $_POST['username'];
if (isset($_POST['password'])){
  $_SESSION['user_password']     = pass_encrypt($_POST['password']);
  $_SESSION['user_password_raw'] = $_POST['password'];
}
else{
  $_SESSION['user_password'] = '';
  $_SESSION['user_password_raw'] = '';
}
$_SESSION['session_start'] = time();
// Премахване на параметър $_GET['user']=='enter' и презареждане на страницата
if (isset($_GET['user'])&&($_GET['user']=='enter')){
  $l = unset_self_query_var('user',true); //echo $l; die;
  header('Location: '.$l);
  die;
}
}

// Кодиране на паролата по един от два начина

function pass_encrypt($p){
// Ако е зададена опция - както се кодират с mysql функцията password()
if (stored_value('user_mysqlpass','')=='yes') return '*'.strtoupper(sha1(sha1($p,true)));
// Иначе така:
else return sha1($p);
}

// Запазване на данните за нов потребител

function save_user(){
global $tn_prefix, $db_link;
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
if ( !isset($_GET['user']) || ($_GET['user']!='newreg') || !isset($_POST['password2']) || ($_POST['password2']!=$_POST['password']) ||
     !$_POST['username']
   ) return;
$u = db_table_field('username', $user_table, "`username`='".addslashes($_POST['username'])."'");
if ($u) return;
$q = "INSERT INTO `$tn_prefix".
     "$user_table` SET `date_time_0`=NOW(), `date_time_1`=NOW(), `username`= '".addslashes($_POST['username']).
     "', `password`='".pass_encrypt($_POST['password'])."';";
mysqli_query($db_link,$q);
$l = unset_self_query_var('user','newreg');
header("Location: $l");
die;
}

// Връща обект форма за влизане/регистриране на потребител

function user_form($c,$t = ''){
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg')) $sb = translate('user_savenew');
else $sb = translate('user_login_button');
$guf = new HTMLForm('login_form',true,$t);
$guf->add_input( new FORMInput(translate('user_username'),'username','text') );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg'))
  $guf->add_input( new FORMInput(translate('user_passwordconfirm'),'password2','password') );
$guf->add_input( new FORMInput('','','submit',$sb) );
return $guf;
}


// Унищожава сесията и пренасочва към страницата след излизане

function logout_user(){
// Адрес на страницата, която по подразбиране се показва след излизане
$lp = current_pth(__FILE__).'logout.php';
// Евентуално в настройките може да е зададена друга 
$lp = stored_value('user_logoutpage',$lp);
if (!session_id()) session_start();
// Унищожаване променливите на сесията
unset($_SESSION['user_username']);
unset($_SESSION['user_password']);
unset($_SESSION['user_password_raw']);
unset($_SESSION['session_start']);
//die(print_r($_SESSION,true));
if (!count($_SESSION)) setcookie('PHPSESSID','',time()-60,'/');
// Запазване на страницата за връщане
if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['user_returnpage'] = $_SERVER['HTTP_REFERER'];
// Пренасочване към страницата след излизане
header("Location: $lp");
die;
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
'country'=>translate('user_country'),
'telephone'=>translate('user_telephone')
);// print_r($cp); die;
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
  die;
}

//
// Функция create_user() се изпълнява, когато в адреса на страницата има параметър user=newreg
// за да покаже форма за създаване на нов потребител.
//
function create_user($a){
if (count($_POST)) save_user();
global $idir;
if (!can_manage_users()) die(translate('user_cnnotcreate'));
$page_title = translate('user_newreg');
$page_content = '<div id="user_login">'."\n<h1>$page_title</h1>\n".user_form(0)->html();
if ($a != 'login'){
  include($idir.'lib/build_page.php');
  die;
}
else return $page_content;
}

//
// Функция, която връща html код на хипервръзка "Вход"
//
function enter_link(){
// Адрес на страницата за влизане
$ep = stored_value('user_loginpage');
if (!$ep) $ep = set_self_query_var('user','enter');
return '<a href="'.$ep.'">'.translate("user_enter").'</a>';
}

//
// Функция за показване форма за изтриване на потребител
//
function delete_user($a){
global $idir;
if (!can_manage_users()) die(translate('user_cnnotcreate'));
//if (isset($_SESSION['user_to_delete'])) { do_delete_user(); return ''; }
$ms = '';
if (count($_POST)){ $ms = process_delete_user(); }
$f = new HTMLForm('del_user_form');
$f->add_input( new FormInput(translate('user_username'),'username','text') );
$f->add_input( new FormInput('','','submit',translate('user_delete')) );
$page_content = '<p class="message">'.$ms.'</p>'.$f->html().
'<a href="'.unset_self_query_var('user').'">'.translate('user_finish').'</a>';
if ($a=='delete') return $page_content;
else { include($idir.'lib/build_page.php'); die; }
}

//
// Изпълнява се, когато е изпратено потребителско име на потребител за изтриване
//
function process_delete_user(){
// Прекратяване ако:
if (!isset($_POST['username']) // Не е азпратено потребителско име за изтриване.
  || ($_POST['username']==$_SESSION['user_username']) // Влезлият потребител иска да изтрие себе си.
) return;
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
// Четене на номера на потребителя за изтриване
$p = db_table_field('`ID`', $user_table, "`username`='".addslashes($_POST['username'])."'",0);
// Прекратявана, ако номера е невалиден
if (!$p) return translate('user_nutodelete');
// Изтриване на потребителя
$r = db_delete_from($user_table, $p);
if ($r===false) return translate('user_ddeletefaild').' "'.$_POST['username'].'".';
// Изтроване на правата на потребителя
db_delete_from('permissions', "`user_id`=$p");
return translate('user_deleteok').' "'.$_POST['username'].'".';
}

//
// Връща true ако влезлият в момента потребител има право да управлява регистрациите на други потребители.
//
function can_manage_users(){
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
// Номер на потребителя
$i = db_table_field('ID',$user_table,"`username`='".$_SESSION['user_username'].
        "' AND `password`='".$_SESSION['user_password']."'");
// Проверка дали потребителят има всички права
$p = db_table_field('yes_no','permissions',"`type`='all' AND `user_id`=$i");
if ($p) return true;
// Ако няма всички права, проверка дали има право за модул user
$p = db_table_field('yes_no','permissions',"`type`='module' AND `object`='user' AND `user_id`=$i");
if ($p) return true; else return false;
}

//
// В момента тази функция не се използва
// Изтрива потребител с потребителско име 
//
function do_delete_user(){
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
db_delete_from($user_table, $_SESSION['user_to_delete']);
unset($_SESSION['user_to_delete']);
$l = unset_self_query_var('user');
header('Location: '.$l);
die;
}

//
// Установява дали потребителят има право да види съдържанието на страницата
//
function user_can_visit($i){
global $page_id;
$rz = db_table_field('yes_no','permissions',"`user_id`=$i AND `type`='all'", false);
if (!($rz===false)) return $rz;
$rz = db_table_field('yes_no','permissions',"`user_id`=$i AND `type`='visit' AND `object`=$page_id", false);
if (!($rz===false)) return $rz;
$rz = db_table_field('yes_no','permissions',"`user_id`=0 AND `type`='visit' AND `object`=$page_id", false);
if (!($rz===false)) return $rz;
return stored_value('user_can_visit',false);
}

//
// Помощна функция, която връща трите имена на потребител по ID.
// Ако няма такъв потребител, връща празен стринг.

function user_names($id){
// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');
// Четене на имената
$n = db_select_1('`firstname`,`secondname`,`thirdname`', $user_table, "`ID`=$id");
if (!$n) return '';
else return $n['firstname'].' '.$n['secondname'].' '.$n['thirdname'];
}

?>

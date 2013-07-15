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
include_once($idir.'lib/f_edit_record_form.php');

// Функцията user() проверява дали има влязъл с парола потребител.
// Ако няма такъв, показва форма за влизане и
// ако потребителското име или паролата са невалидни връща "Access denied."
// Ако потребителското име и паролата са валидни връща препратка "Изход".
// Ако е изпратен параметър $_GET['user']='newreg' се показва форма за въвеждане на данни за нов потребител.
// $a = 'login' означава, че страницата, от която се вика user, е специална страница за влизане и след успешно
// влизане става препращане към адрес stored_value('user_loginpage',''), ако е зададен такъв.
// $a = 'edit' означава, че страницата, от която се вика user, е страница за редактиране данните за
// потребителя и кяеи фръща форма за редактиране на тези данни.

session_start();

function user($a = ''){
global $tn_prefix, $db_link;
//if (show_adm_links()) return '';
// Ако е натиснат линк "Изход"
if (isset($_GET['user'])&&($_GET['user']=='logout')) logout_user();
$rz = '';
// Ако няма влязъл потребител се отваря страница за влизане.
if (!isset($_SESSION['user_username'])){
  $rz = get_user($a);
  if ($rz) return $rz;
}
// Четене на номера на потребител с име $_SESSION['user_username'] и парола $_SESSION['user_password'].
$rz = db_select_1('ID','users',
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");
// Ако няма такъв потребител - Access denied
if (!$rz) { session_destroy(); header("Status: 403"); die("Access denied."); }
else{
  //
  // Ако се редактират данните на потребителя
  if ($a == 'edit') return edit_user($rz['ID']);
  // Адрес на страницата, на която да се отиде след влизане.
  $lp = stored_value('user_loginpage','');
  // Ако е зададена се отбелязва часа на влизане и се извършва препращане.
  if ($lp && ($a=='login')){
    $tm = date('Y-m-d H:m:s', $_SESSION['session_start']);
    mysql_query("UPDATE `$tn_prefix"."users` SET `date_time_2`='$tm' WHERE `ID`=".$rz['ID'].";", $db_link);
    header("Location: $lp");
    die;
  }
  // Ако не е зададена се връща линк "Изход".
  else $rz = '<p class="user">'.$_SESSION['user_username'].
       ' <a href="'.set_self_query_var('user','logout').'">'.translate('user_logaut').'</a></p>';
}
return $rz;
}

// Функцията get_user() връща HTML код с форма за влизане/регистриране на потребител

function get_user($a){
// Ако формата за влизане вече е попълнена, се обработват изпратените с нея данни
if (isset($_POST['username'])){ process_user(); return ''; }
global $idir;
if (isset($_GET['user']) && ($_GET['user']!='newreg')) $page_title = translate('user_newreg');
else $page_title = translate('user_login');
$page_content = '<div id="user_login">'."\n<h1>$page_title</h1>\n".user_form()->html();
if (stored_value('user_showreglink', 'false')=='true')
   $page_content .= '<p><a href="'.set_self_query_var('user','newreg').'">'.translate('user_newreg')."</a></p>";
$page_content .= "\n</div>";
if ($a != 'login'){ include($idir.'lib/build_page.php'); die; }
else return $page_content;
}

// Функцията process_user() обработва данните за влизане на потребителя - 
// присвоява ги на съответните променливи на сесията

function process_user(){
if (isset($_POST['password2'])); save_user();
$_SESSION['user_username'] = $_POST['username'];
if (isset($_POST['password'])) $_SESSION['user_password'] = sha1($_POST['password']); else $_SESSION['user_password'] = '';
$_SESSION['session_start'] = time();
}

// Запазване на данните за нов потребител

function save_user(){
global $tn_prefix, $db_link;
if ( !isset($_GET['user']) || ($_GET['user']!='newreg') || ($_POST['password2']!=$_POST['password']) || !$_POST['username'] )
   return;
$u = db_table_field('username', 'users', "`username`='".addslashes($_POST['username'])."'");
if ($u) return;
$q = "INSERT INTO `$tn_prefix".
     "users` SET `date_time_0`=NOW(), `date_time_1`=NOW(), `username`= '".addslashes($_POST['username']).
     "', `password`='".sha1($_POST['password'])."';";
mysql_query($q,$db_link);
}

// Връща обект форма за влизане/регистриране на потребител

function user_form(){
$guf = new HTMLForm('login_form');
$guf->add_input( new FORMInput(translate('user_username'),'username','text') );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
if (isset($_GET['user']) && ($_GET['user']=='newreg'))
  $guf->add_input( new FORMInput(translate('user_passwordconfirm'),'password2','password') );
$guf->add_input( new FORMInput('','','submit',translate('user_login_button')) );
return $guf;
}


// Унищожава сесията и пренасочва към страницата за излизане

function logout_user(){
// Адрес на страницата, която се показва след излизане
$lp = current_pth(__FILE__).'/logout.php';
$lp = stored_value('user_logoutpage',$lp);
session_destroy();
header("Location: $lp");
}

// Връща форма за редактиране данните на потребителя

function edit_user($id){
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
if (count($_POST)) $rz .= process_record($cp, 'users');
return $rz.edit_record_form($cp, 'users');
}

?>

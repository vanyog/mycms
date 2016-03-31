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

// ��������� user() ��������� ���� ��� ������ � ������ ����������.
// ��� ���� �����, ������� ����� �� ������� �
// ��� ��������������� ��� ��� �������� �� �� ������� ����������� "Access denied."
// ��� ��������������� ��� � �������� �� ������� ����� ��������������� ��� � ��������� "�����".

// ���� ��������� ������������ �� ����������, ��������� ������ �������� �� $can_visit ������
// ���������� � ������� p�rmissions ����� � ���������� �� ����������� user_can_visit,
// ���� ��, ���� � �� � ������, ������������ ���� �� ���� �������� Access denied,
// ��� ���� ����� ����� �� ������ ��� ����������.

// $_GET['user']='newreg' - ������� ����� �� ��������� �� ����� �� ��� ����������,
// ��� �������, �� ��� ������ ���������� � ����� �� ������� ����� �����������. (`type`='module', `object`='user')
// $_GET['user']='logout' - ����������� �������� �� �����������.
// $_GET['user']='delete' - ������� ����� �� ��������� �� ����������.

// $a = 'login' ��������, �� ����������, �� ����� �� ���� user, � ��������� �������� �� ������� � ���� �������
// ������� ����� ���������� ��� ����� stored_value('user_loginpage',''), ��� � ������� �����.
// $a = 'edit' ��������, �� ����������, �� ����� �� ���� user, � �������� �� ����������� ������� ��
// ����������� � ����� ����� �� ����������� �� ���� �����.
// $a = 'enter' ��������, ��� ���� ������ ���������� �� �� ������� ���� "����".
// $a = 'create' ����� ����� �� ��������� �� ��� ����������, ��� �������, �� ��� ������ ���������,
// � ����� �� ������� ����� �����������.

function user($a = ''){

global $tn_prefix, $db_link, $user_table, $mod_apth, $can_visit;

// ��� � �������� ���� "�����"
if (isset($_GET['user'])&&($_GET['user']=='logout')) logout_user();
$rz = '';

// ��� �� ��������� � ����� �� �����������
$user_table = stored_value('user_table','users');

// $c - ���� �� �������� � ����������� � ������� $user_table
$c = db_table_field('COUNT(*)',$user_table,'1');// print_r($c); die;

// ������ - ����� ���� ������� $user_table.
if ($c===false) die("Table '$user_table' is not set up.");

// ��� ���� �����������, �� ������� ��� ����������.
if ( !$c && (!isset($_GET['user']) || ($_GET['user']!='newreg')) ){ return new_user($a); }

if (!session_id()) session_start();

// ��� ���� ������ ���������� �� ������ �������� �� �������.
if (!isset($_SESSION['user_username'])){
  // �� ��� $a == 'enter' �� ������� ����������� "����"
  // ����� ��� �� � �������� ��������� user=enter ��� ��� ��� ���� ������������ �����������,
  // ������ ���� �� ������� ����� �� �������.
  if ( ($a == 'enter') && $c && (!isset($_GET['user'])||($_GET['user']!='enter')) ) return enter_link();
  $rz = get_user($a,$c);
  if ($rz) return $rz;
}

// ������ �� ������ �� ���������� � ��� $_SESSION['user_username'] � ������ $_SESSION['user_password'].
$rz = db_select_1('ID',$user_table,
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");

// ��� ���� ����� ���������� - �������� �� ����� ������� �� �������
if (!$rz) {
  // ������ �� ������� � ����� ������� �� ������ ����� �� �����������
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
  // ��� ����� ������� �� � �������� ����� �� ������� ���������� - Access denied.
  if (!$rz) { session_destroy(); header("Status: 403"); die("Access denied by user module."); } 
}
else{
  // ��� �� ���������� ������� �� �����������.
  if ($a == 'edit') return edit_user($rz['ID']);

  // ����������� �� ���� � IP ������ �� �������
  if (!isset($_SESSION['session_start'])) $_SESSION['session_start'] = time();
  $tm = date('Y-m-d H:m:s', $_SESSION['session_start']);
  $q = "UPDATE `$tn_prefix"."$user_table` SET `date_time_2`='$tm', `IP`='".$_SERVER['REMOTE_ADDR'].
       "' WHERE `ID`=".$rz['ID'].";";
  mysqli_query($db_link, $q);

  // ��� � �������� ��������� �� ��������� �� ��� ����������.
  if ( ($a=='create') || (isset($_GET['user']) && ($_GET['user']=='newreg')) ) return create_user($a);

  // ��� � �������� ��������� �� ��������� �� ����������.
  if ( ($a=='delete') || (isset($_GET['user']) && ($_GET['user']=='delete')) ) delete_user($a);

  // ����� �� ����������, �� ����� �� �� ����� ���� �������.
  $lp = stored_value('user_loginpage','');

  // ��� � �������� �� �������� ����������.
  if ($lp && ($a=='login')){
    header("Location: $lp");
    die;
  }
  else{ // ��� �� � �������� �� ����� ���� "�����".
    $can_visit = user_can_visit($rz['ID']);
    $rz = user_logout_link();
  }
}
return $rz;
} // function user($a = '')

//
// ����� ��������������� ��� �� ������� ���������� � ���� "�����"

function user_logout_link(){
return '<span class="user">'.$_SESSION['user_username'].
       ' <a href="'.set_self_query_var('user','logout').'">'.translate('user_logaut').'</a></span>';
}

//
// ��������� get_user() ����� HTML ��� � ����� �� �������/������������ �� ����������
//

function get_user($a,$c){
// ��� ������� �� ������� ���� � ���������, �� ���������� ����������� � ��� �����
if (isset($_POST['username'])){ process_user(); return ''; }
global $idir, $site_encoding;
// �������� �� ���������� �� �������/��������� �� ����������
if (!$c && isset($_GET['user']) && ($_GET['user']=='newreg')) $page_title = translate('user_newreg');
else $page_title = translate('user_login');
// ��� ���� ��� ���� ���� ���������� - ������, ����� �������� ����
$m = '';
if (!$c) $m = translate('user_firstuser');
// ���������� �� ����������
$page_content = '<div id="user_login">'."\n".user_form($c,"<h1>$page_title</h1>\n$m\n")->html();
if (stored_value('user_showreglink', 'false')=='true')
   $page_content .= '<p><a href="'.set_self_query_var('user','newreg').'">'.translate('user_newreg')."</a></p>";
$page_content .= "\n</div>";
// ��� ���������� �� �� ������ � ������ �� ������� � build_page.php,
if ($a != 'login'){ include($idir.'lib/build_page.php'); die; }
// ����� �� ����� ������� ����������, �� �� �� ������ � �������.
else return $page_content;
}

// ��������� process_user() ��������� ������� �� ������� �� ����������� - 
// ��������� �� �� ����������� ���������� �� ������� � ���������� ����������

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
// ���������� �� ��������� $_GET['user']=='enter' � ������������ �� ����������
if (isset($_GET['user'])&&($_GET['user']=='enter')){
  $l = unset_self_query_var('user',true); //echo $l; die;
  header('Location: '.$l);
  die;
}
}

// �������� �� �������� �� ���� �� ��� ������

function pass_encrypt($p){
// ��� � �������� ����� - ����� �� ������� � mysql ��������� password()
if (stored_value('user_mysqlpass','')=='yes') return '*'.strtoupper(sha1(sha1($p,true)));
// ����� ����:
else return sha1($p);
}

// ��������� �� ������� �� ��� ����������

function save_user(){
global $tn_prefix, $db_link;
// ��� �� ��������� � ����� �� �����������
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

// ����� ����� ����� �� �������/������������ �� ����������

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


// ��������� ������� � ���������� ��� ���������� ���� ��������

function logout_user(){
// ����� �� ����������, ����� �� ������������ �� ������� ���� ��������
$lp = current_pth(__FILE__).'logout.php';
// ���������� � ����������� ���� �� � �������� ����� 
$lp = stored_value('user_logoutpage',$lp);
if (!session_id()) session_start();
// ����������� ������������ �� �������
unset($_SESSION['user_username']);
unset($_SESSION['user_password']);
unset($_SESSION['user_password_raw']);
unset($_SESSION['session_start']);
//die(print_r($_SESSION,true));
if (!count($_SESSION)) setcookie('PHPSESSID','',time()-60,'/');
// ��������� �� ���������� �� �������
if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['user_returnpage'] = $_SERVER['HTTP_REFERER'];
// ������������ ��� ���������� ���� ��������
header("Location: $lp");
die;
}

// ����� ����� �� ����������� ������� �� �����������

function edit_user($id){
// ��� �� ��������� � ����� �� �����������
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
// ������� new_user() �� �������, ������ ��� ��� ���� ���� ���� ����������.
// �� ���������� ����������, ���� ������ � ������ � �������� user=newreg � ���� ����������� ��������� �� ����� �� 
// ����������� �� ��� ����������.
//
function new_user($a){// print_r($_SESSION); die;
  // ��� ������ �� �� ������� ���� ���� "����", �� ����� ���� ����
  if (($a=='enter')&&!(isset($_GET['user'])&&($_GET['user']=='enter'))) return enter_link();
  // � �������� ������ �� ������� ��������� user=newreg � ���������� �� ����������
  $l = set_self_query_var('user','newreg',false);
  header("Location: $l");
  die;
}

//
// ������� create_user() �� ���������, ������ � ������ �� ���������� ��� ��������� user=newreg
// �� �� ������ ����� �� ��������� �� ��� ����������.
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
// �������, ����� ����� html ��� �� ����������� "����"
//
function enter_link(){
// ����� �� ���������� �� �������
$ep = stored_value('user_loginpage');
if (!$ep) $ep = set_self_query_var('user','enter');
return '<a href="'.$ep.'">'.translate("user_enter").'</a>';
}

//
// ������� �� ��������� ����� �� ��������� �� ����������
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
// ��������� ��, ������ � ��������� ������������� ��� �� ���������� �� ���������
//
function process_delete_user(){
// ������������ ���:
if (!isset($_POST['username']) // �� � ��������� ������������� ��� �� ���������.
  || ($_POST['username']==$_SESSION['user_username']) // �������� ���������� ���� �� ������ ���� ��.
) return;
// ��� �� ��������� � ����� �� �����������
$user_table = stored_value('user_table','users');
// ������ �� ������ �� ����������� �� ���������
$p = db_table_field('`ID`', $user_table, "`username`='".addslashes($_POST['username'])."'",0);
// ������������, ��� ������ � ���������
if (!$p) return translate('user_nutodelete');
// ��������� �� �����������
$r = db_delete_from($user_table, $p);
if ($r===false) return translate('user_ddeletefaild').' "'.$_POST['username'].'".';
// ��������� �� ������� �� �����������
db_delete_from('permissions', "`user_id`=$p");
return translate('user_deleteok').' "'.$_POST['username'].'".';
}

//
// ����� true ��� �������� � ������� ���������� ��� ����� �� ��������� ������������� �� ����� �����������.
//
function can_manage_users(){
// ��� �� ��������� � ����� �� �����������
$user_table = stored_value('user_table','users');
// ����� �� �����������
$i = db_table_field('ID',$user_table,"`username`='".$_SESSION['user_username'].
        "' AND `password`='".$_SESSION['user_password']."'");
// �������� ���� ������������ ��� ������ �����
$p = db_table_field('yes_no','permissions',"`type`='all' AND `user_id`=$i");
if ($p) return true;
// ��� ���� ������ �����, �������� ���� ��� ����� �� ����� user
$p = db_table_field('yes_no','permissions',"`type`='module' AND `object`='user' AND `user_id`=$i");
if ($p) return true; else return false;
}

//
// � ������� ���� ������� �� �� ��������
// ������� ���������� � ������������� ��� 
//
function do_delete_user(){
// ��� �� ��������� � ����� �� �����������
$user_table = stored_value('user_table','users');
db_delete_from($user_table, $_SESSION['user_to_delete']);
unset($_SESSION['user_to_delete']);
$l = unset_self_query_var('user');
header('Location: '.$l);
die;
}

//
// ���������� ���� ������������ ��� ����� �� ���� ������������ �� ����������
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
// ������� �������, ����� ����� ����� ����� �� ���������� �� ID.
// ��� ���� ����� ����������, ����� ������ ������.

function user_names($id){
// ��� �� ��������� � ����� �� �����������
$user_table = stored_value('user_table','users');
// ������ �� �������
$n = db_select_1('`firstname`,`secondname`,`thirdname`', $user_table, "`ID`=$id");
if (!$n) return '';
else return $n['firstname'].' '.$n['secondname'].' '.$n['thirdname'];
}

?>

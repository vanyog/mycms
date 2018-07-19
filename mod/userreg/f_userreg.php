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

// ����� �� ���������������� �� �����������
// ����� ���� README.txt.

//
// �������� ������� �� ������ � userreg($t = '')

// ��� � ������� 'content' ��� ����� � ��� "USERREG_$t" ������� �� ����� �������� �� ���� ����������� $t.
// ������ ������� ������ ���� � ����� �� ����������� �� �����, � � �������� ����� ������� ������� �� ������� �� ��� "USERREG_$t".

// ����������� $t � ������, ����� ���� �� � �������� ��� ������ | �� �����.

// ������� ���� � ������������ � � �������� �� ������ `type` � ��������� � ����� �� �������������.

// ������� ����, ��� ��� ������, ���� �� ��� ��������:
// 'logout' �� �� �� �� ������ ��������������� ��� � ���� "�����", ��� ����, ��� ���� ������ ����������.
// 'name' �� �� �� ������� ����� ����� �� ������� ����������
// 'return' �� �� �� ��������� ������� �� ���������� ��������, ���� ������� �� �����������.
// ��� � ����� �� ��������, ��� ���� �������� �� �������� ����������, ��� ������������ � ������� ������.

// ���������� �� ��������� �� �������� � �� ���������� �� ���������� $_GET['user2']
// 'newreg' - ���� ����������� ��� ����� �� ��������
// 'login' - �������
// 'logout' - ��������
// 'edit' - ����������� �� �������

// ��� ���� ��������� $_GET['user2'], ��� ������� �� ������ ������� ���������� �� ���������� ���
// ��������� ����� ������ ������, � � �������� ������ �������� ��� ���������� �� �������.

include_once($idir.'lib/f_rand_string.php');
include_once($idir.'lib/f_db_insert_or_1.php');
include_once($idir.'lib/f_db_update_record.php');
include_once($idir.'lib/f_view_record.php');
include_once($idir.'lib/f_edit_record_form.php');
include_once($idir.'mod/user/f_user.php');

global $user_table, $userreg_altt;

// ��� �� ��������� � ����� �� �������������
$user_table = stored_value('user_table','users');

function userreg($t = ''){
global $main_index, $can_visit, $userreg_altt;
if (!$t) die('No user type specified in userreg module.');
$ta = explode('|', $t);
$n = "USERREG_".$ta[0];
$altt = translate($n,false);
if( $altt && ($altt!=$n) && !in_edit_mode() && !empty($ta[1]) ){
   if(empty($userreg_altt)) {
      $userreg_altt = true;
      return $altt;
   }
   else return '';
}
if (isset($ta[1])) switch($ta[1]){
case 'logout': return userreg_outlink($ta[0]); break;
case 'mydata': return userreg_mydata($ta[0]); break;
case 'edit'  : if(!isset($_GET['user2']) || ($_GET['user2']=='edit')) return userreg_edit($ta[0]);   break;
case 'name'  : return userreg_name($ta[0]);    break;
default: if(!(1*$ta[1])&&($ta[1]!='edit')) die("Undefined parameter '".$ta[1]."' for USERREG module"); break;
}
if (isset($_GET['user2'])) switch ($_GET['user2']){
case 'newreg': return userreg_newform($ta[0]); break;
case 'login' : return userreg_login($ta[0]);   break;
case 'logout': return userreg_logout($ta[0]);  break;
case 'edit'  : return userreg_edit($ta[0]);    break;
}
else{
  if (isset($ta[1])){
    $pid = 1*$ta[1];
    if ($pid){ header('Location: '.$main_index.'?pid='.$pid); die; }
  }
  return userreg_check($ta[0]);
}
}

function json_to_array($js){
$rz = array();
$l = strlen($js);
$s = substr($js, 2, $l-4);
$a = explode(',', $s);
foreach($a as $v){
  $p = explode(':',$v);
  if(count($p)!=2) continue;
  $k = trim($p[0]);
  $k = substr($k, 1, strlen($k)-2);
  $v = trim($p[1]);
  if($v[0]=='"') $v = substr($v, 1, strlen($v)-2);
  $rz[$k]=$v;
}
return $rz;
}

//
// ����� html ��� - ����� �� ����������������/����� �� �������� �� ����������

function userreg_newform($t){
if (isset($_GET['code'])) return confirm_userreg($t);
userreg_check($t);
// ������������ ��� https, ��� �� �������
if((stored_value('userreg_https')=='on') && !(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) ){
  header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
  die();
}
$message = '';
$email = ''; 
if (count($_POST)){
 //print_r($_POST); die;
 $message = userreg_newprocess();
 if ($message=='OK') return '<p class="message">'.translate('userreg_emailsent').'</p>';
 $email = $_POST['email'];
}
$f = userreg_new_form($t, $email);
$fb = userreg_facebook();
return translate('userreg_newregtext').
' <a href="'.stored_value("userreg_login_$t").'">'.translate('userreg_login').'</a></p>
<p class="message">'.$message.'</p>
'.$f->html().$fb.translate('userreg_newhelp');
}

function userreg_facebook(){
if (stored_value('userreg_facebook')!='allowed') return '';
return "
<script>

// This code is from https://developers.fac book.com/docs/facebook-login/web#checklogin
// modified by the needs of MyCMS project

  function statusChangeCallback(response) {
    if (response.status === 'connected') {
      document.getElementsByTagName('fb:login-button')[0].innerHTML = '';
      testAPI();
    } else if (response.status === 'not_authorized') {
      document.getElementById('status').innerHTML = '".translate('userreg_byFacebookNA')."';
    } else {
      document.getElementById('status').innerHTML = '".translate('userreg_byFacebookNC')."';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1350744361603908',
    cookie     : true,  // enable cookies to allow the server to access the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.8' // use graph api version 2.8
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = \"//connect.facebook.net/en_US/sdk.js\";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  function testAPI() {
    FB.api('/me', {fields:'name, email'}, function(response) {
      noEmptyCheck = '".translate('userreg_confirmByFB')."';
      document.forms['userreg_form'].email.value = response.email;
      document.forms['userreg_form'].password.value = 'fromFacebook';
      document.getElementById('status').innerHTML = '".translate('userreg_continueByFB')."';
    });
  }
</script>

<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->

<fb:login-button scope=\"public_profile,email\" onlogin=\"checkLoginState();\">
</fb:login-button>

<div id=\"status\">
</div>

";
}

//
// ����� ����� �� ��� HTMLForm - ����� �� ���������������� �� ����������

function userreg_new_form($t, $email){
$f = new HTMLForm('userreg_form');
$f->add_input( new FormInput('', 'type', 'hidden', $t) );
$f->add_input( new FormInput(translate('user_email'),'email','text',$email) );
$f->add_input( new FormInput(translate('user_password'),'password','password'));
$f->add_input( new FormInput(translate('user_passwordconfirm'),'password2','password'));
if(!is_local()) $f->add_input( new FormReCaptcha(translate('userreg_recaptcha'), stored_value('recaptcha_pub') ) );
$fi = new FormInput('','','button',translate('userreg_regsubmit'));
$fi->js = ' onclick="ifNotEmpty_userreg_form();"';
$f->add_input( $fi );
return $f;
}

//
// ����������� �� ����� �� ����� �� ���������������� �� ����������
//
function userreg_newprocess(){
global $site_encoding;
// ��� �������� � ��-���� �� 8 �������
if ( !isset($_POST['password']) || (strlen($_POST['password'])<8) ) return translate('userreg_pshort');
// ��� �������� � ������������ � �� ��������
if ($_POST['password']!=$_POST['password2']) return translate('user_passwordinvalid');
// �������� �� reCAPTCHA
if (isset($_POST["g-recaptcha-response"])){
  $secret = stored_value("recaptcha_private");
  if(!$secret) die("No 'recaptcha_private' option is set.");
  $erl = error_reporting(0);
  $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.
                    '&response='.$_POST['g-recaptcha-response']);
  error_reporting($erl);
  $rd = json_to_array($verifyResponse);
  if ( !isset($rd['success']) || ($rd['success']!='true') )
    return translate('reCAPTCHA_error');
}
// ����� �� ���������
$ea = explode(' ',trim($_POST['email'])); $e = trim($ea[0]);
$ea = explode("\n",$e); $e = trim($ea[0]);
$ea = explode("\r",$e); $e = trim($ea[0]);
// ����� �� ��� ����������
$d = array(
  'type'=>addslashes($_POST['type']),
  'date_time_0'=>'NOW()',
  'date_time_1'=>'NOW()',
  'email'=>addslashes($e),
  'newpass'=>pass_encrypt($_POST['password']),
  'code'=>rand_string(40),
  'IP'=>$_SERVER['REMOTE_ADDR']
);
// �������� ��� ������������� �� ������
$r = db_insert_or_1($d, stored_value('user_table','users'), "`email`='".$d['email']."'",'b');
// ����� �� ������
$ms = translate('userreg_regmess').'http://'.$_SERVER['HTTP_HOST'].set_self_query_var('code',$d['code'],false);
$sb = translate('userreg_regsub');
$sb = '=?'.$site_encoding.'?B?'.base64_encode($sb).'?=';
$hd = 'Content-type: text/plain; charset='.$site_encoding."\r\n".
      'From: '.stored_value('site_owner_email','site@vsu.bg')."\r\n";
mail($e,$sb,$ms,$hd);
return 'OK';
}

//
// ���������� �� �������������/������� �� ��������

function confirm_userreg($t){
global $user_table;
// ������ �� ������, �������� ���������� ��� �� �����������
$d = db_select_1('*', $user_table, "`code`='".addslashes($_GET['code'])."'");
// ��� ����� �� � ������� - ���������
if (!$d) return translate('userreg_invalidcode').
                ' <a href="'.stored_value("userreg_newreg_$t").'">'.translate('userreg_newreg').'</a>';
unset($d['date_time_0']);
$d['date_time_1'] = 'NOW()';
unset($d['date_time_2']);
$d['username'] = $d['email'];
$d['password'] = $d['newpass'];
$d['newpass'] = '';
$d['code'] = '';
unset($d['email']);
unset($d['firstname']);
unset($d['secondname']);
unset($d['thirdname']);
unset($d['telephone']);
unset($d['IP']);
db_update_record($d,$user_table);
return '<p class="message">'.translate('userreg_emcofirmed').' <a href="'.stored_value("userreg_login_$t").'">'.translate('userreg_login').'</a></p>';
}

//
// ��� ��� ������ ����������, ����� ������ ������ ���
// ���������� ��� ���������� �� �������, ��� ���� ������ ����������

function userreg_check($t){
global $page_id, $can_visit;
if (!session_id()) session_start();
// ����� �� ���������� �� �������
$lp = stored_value("userreg_login_$t");
if(!$lp) die("'userreg_login_$t' option is not set.");
// ��� � �������� https
if(stored_value('userreg_https')=='on') $lp = 'https://'.$_SERVER['HTTP_HOST'].$lp;
// ��������� �� �������� ��������, ���� �������� �� �������
$_SESSION['user2_returnpage'] = $_SERVER['REQUEST_URI'];
// ����� �� ������� ����������
$id = userreg_id($t);
// ��� ���� ����� �� ������ ���������� � �� ��� �� ���������� �� ������� - ������������
// ��� ���������� �� �������
if (!$id)
   if (strpos($lp,'pid='.$page_id)===false) { header('Location: '.$lp); die; }
// ��� ������������ ����������, �� ����� ������ ������.
$can_visit = 1;
return '';
}

//
// ����� ������ �� ������� ���������� ��� 0 ��� ���� �����

function userreg_id($t){
global $can_visit, $user_table;
if (!session_id() && isset($_COOKIE['PHPSESSID'])) session_start();// die("$t");
if (!isset($_SESSION)) return 0;
if (!isset($_SESSION['user_username'])){
   unset($_SESSION['user_password']);
   unset($_SESSION['session_start']);
   return 0;
}
if (!isset($_SESSION['user_password'])){
   unset($_SESSION['user_username']);
   unset($_SESSION['session_start']);
   return 0;
}
$id = db_table_field('ID', $user_table, "`username`='".$_SESSION['user_username'].
      "' AND `password`='".$_SESSION['user_password']."' AND `type`='$t'", 0);
$_SESSION['userreg_message'] = translate('userreg_wrong');
if (!$id){ unset($_SESSION['user_username']); return 0; }
unset($_SESSION['userreg_message']);
$can_visit = 1;
// ����������� �� ���� � IP ������ �� �������
if (!isset($_SESSION['session_start'])) $_SESSION['session_start'] = time();
$tm = date('Y-m-d H:m:s', $_SESSION['session_start']);
db_update_record( array( 'ID'=>$id, 'date_time_2'=>$tm, 'IP'=>$_SERVER['REMOTE_ADDR']), $user_table);
return $id;
}

//
// ����� ��������������� ��� � ���� "�����"

function userreg_outlink($t){
if (!userreg_id($t)) return '';
// ����� �� ���������� �� ��������
$lp = stored_value("userreg_logout_$t");
if (!$lp) die("'userreg_logout_$t' option is not set.");
$_SESSION['user2_returnpage'] = $_SERVER['REQUEST_URI'];
return '<p class="user">'.$_SESSION['user_username'].' <a href="'.$lp.'">'.translate('user_logaut').'</a></p>';
}

//
// ����� ����� ����� �� �����������

function userreg_name($t){
global $user_table;
$id = userreg_id($t);
if (!$id) return '';
$d = db_select_1('*', $user_table, "`ID`=$id");
$rz = $d['firstname'].' '.$d['secondname'].' '.$d['thirdname'];
if(strlen($rz)<3) $rz = $d['email'];
return $rz;
}

//
// ����� �� ������� �� ����������

function userreg_login($t){
if (count($_POST)) return userreg_loginprocess($t);
userreg_check($t);
// ����� �� ���������� �� ��������
$lp = stored_value("userreg_logout_$t");
// ����� �� ������� ����������
$id = userreg_id($t);
// ��� ���� ��� ������ ���������� - ������ "��� ��� ������ ����: ��� "�����"
if ($id)
    return '<p class="message">'.translate('userreg_yourin').$_SESSION['user_username'].
           ' <a href="'.$lp.'">'.translate('user_logaut').'</a></span>';
// �������� �� ����������, ��� ����� �� ����� �������
if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['user2_returnpage'])) 
   $_SESSION['user2_returnpage'] = $_SERVER['HTTP_REFERER'];
// ����� �� �������
$guf = new HTMLForm('userreg_login');
$guf->add_input( new FORMInput(translate('user_username'),'username','text') );
$guf->add_input( new FORMInput(translate('user_password'),'password','password') );
$guf->add_input( new FORMInput('','','submit', translate('user_login_button')) );
$rp = stored_value("userreg_newreg_$t");
if (!$rp) die("'userreg_newreg_$t' option is not set.");
$altt = '';
if(in_edit_mode()) $altt = translate("USERREG_$t");
$lk = '<a href="'.$rp.'">'.translate('userreg_newreg')."</a>";
if(!empty($_SESSION['userreg_message'])) $altt .= '<p class="message">'.$_SESSION['userreg_message']." $lk</p>\n";
unset($_SESSION['userreg_message']);

return $altt.translate('userreg_logintext').$lk."</p>\n".$guf->html();
}

//
// ����������� �� ������� �� ������������� ��� � ������,
// ��������� � ������� �� �������

function userreg_loginprocess($t){
if (isset($_POST['username'])){
  if (!session_id()) session_start();
  $_SESSION['user_username'] = addslashes($_POST['username']);
  $_SESSION['user_password'] = pass_encrypt($_POST['password']);
  $_SESSION['session_start'] = time();
}
// ��� � ���������� �������� �� ������� - ������������ ��� ���
if (!isset($_SESSION['user2_returnpage'])) $h = $_SERVER['REQUEST_URI'];
// ����� �� ���������� �������� ��������
else $h = $_SESSION['user2_returnpage'];
// �� ������ �� ���������� �� �������� ��������� user2=logout � user2=login.
// ����� ������������ �� ������ �� ��������� ��� ��� �� � ����� �� ����� ���
// �� �� ������� ������ ������� ��� �������.
$h = str_replace('&user2=logout','',$h);
$h = str_replace('&user2=login','',$h);
header('Location: '.$h);
die;
}

//
// �������� �� ���������� �� ���������

function userreg_logout($t){
userreg_check($t);
if (!session_id()) session_start();
//die(print_r($_SESSION, true));
unset($_SESSION['user_username']);
unset($_SESSION['user_password']);
$rz = '<p>'.translate('userreg_logoutcontent').
       ' <a href="'.stored_value("userreg_login_$t").'">'.translate('userreg_login').'</a></p>
';
if (isset($_SESSION['user2_returnpage']))
   if (isset($_SERVER['HTTP_REFERER'])) $rf = $_SERVER['HTTP_REFERER'];
   else $rf = $_SESSION['user2_returnpage'];
   $rz .= '<p><a href="'.$rf.'">'.translate('userreg_backto').'</a>.</p>';
unset($_SESSION['user2_returnpage']);
unset($_SESSION['session_start']);
if (!count($_SESSION)) setcookie('PHPSESSID','',time()-60,'/');
return $rz;
}

//
// ����� �� ����������� ������� �� ����������

function userreg_edit($t){
global $user_table, $idir;
// ����������� ���� ��� ������ ����������
$r = userreg_id($t);
//if (!isset($_SESSION['user_username'])){
if (!$r){
   $lp = stored_value("userreg_login_$t");
   return '<p class="message">'.translate('userreg_mustlogin').' <a href="'.$lp.'">Log in</a></p>';
}
// ������������ ��� https, ��� �� �������
if((stored_value('userreg_https')=='on') && !(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) ){
  header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
  die();
}
// ����� � ������� �� ����������� ������
$cp = array(
'username'=>translate('user_username'),
'password'=>translate('user_password'),
'email'=>translate('user_email'),
'firstname'=>translate('user_firstname'),
'secondname'=>translate('user_secondname'),
'thirdname'=>translate('user_thirdname'),
'country'=>translate('user_country'),
'institution'=>translate('user_institution'),
'address'=>translate('user_address'),
'telephone'=>translate('user_telephone')
);
// ��� � �������� ��������� $_GET['uid'] - ���� ����� �� ����������, ����� ����� �� �� ����������
$id2 = 0;
if (isset($_GET['uid'])) $id2 = 1*$_GET['uid'];
// ������ ������ �� ������� ����������
$id1 = db_table_field('ID', $user_table, "`username`='".$_SESSION['user_username'].
       "' AND `password`='".$_SESSION['user_password']."' AND `type`='$t'", 0);
global $can_manage;
$rz = ''; // ������ ��������
if ( $id2 && ($id1!=$id2) ){
 if(!function_exists('usermenu')){
   include_once($idir.'mod/usermenu/f_usermenu.php');
   usermenu(true);
 }
 if (isset($can_manage['userreg']) && $can_manage['userreg'] ){
   // �������� ���� ���������� $id2 � �� ����� ���
   $ty = db_table_field('type', $user_table, "`ID`=$id2");
   // ���������: �� ���� �� �� ���������� ����� �� ���������� �� ���� ���
   if ($ty!=$t) return '<p class="message">'.translate('userreg_othertype').'</p>';
   $cp['ID'] = $id2;
   // ��������� �� ��������, �� �� ���������� ����� �� ���� ����������
   $rz .= '<p class="message">'.translate('userreg_editother').'</p>';
 }
 // ���������: ���� ����� �� ���������.
 else return '<p class="message">'.translate('userreg_cnnotedit').'</p>';
}
else $cp['ID'] = $id1;
if (count($_POST)){ $rz .= userreg_editprocess($user_table, $t); }
// �������� �� ���������
$h = translate('userreg_egithelp');
return $rz.edit_record_form($cp, $user_table, false).$h;
}

//
// ��������� ������� �� ����������� ���� �����������

function userreg_editprocess($user_table, $t){//print_r($_POST); die;
$rz = '';
if ( !$_POST['password'] || ($_POST['password']!=$_POST['password2']) ){
  if ($_POST['password2']) $rz .= '<p class="message">'.translate('user_passwordinvalid').'</p>';
  unset($_POST['password']);
}
unset($_POST['password2']);
// ������ ������ �� ������� ����������
$id1 = db_table_field('ID', $user_table, "`username`='".$_SESSION['user_username'].
       "' AND `password`='".$_SESSION['user_password']."' AND `type`='$t'", 0);
// ����� �� �����������, ����� ����� �� ���������
$id2 = 1*$_POST['ID'];
if (isset($_POST['password'])){
  $_POST['password'] = pass_encrypt($_POST['password']);
  if ($id1==$id2) $_SESSION['user_password'] = $_POST['password'];
}
// ��� username � ������ ������ ��� ������� � ���� �� ���� ����������
// �� �������� �� �� �� �� �������
$un = trim($_POST['username']);
if (!$un) {
  unset($_POST['username']);
  $rz .= '<p class="message">'.translate('userreg_emptyun').'</p>';
}
else {
  $id = db_table_field('ID', $user_table, 
        "`username`='".addslashes($_POST['username'])."' AND `ID`<>$id2", 0);
  if ($id){
    unset($_POST['username']);
    $rz .= '<p class="message">'.translate('userreg_sameun').'</p>';
  }
}
$i = db_update_record($_POST, $user_table);
if ($i){
  $rz .= '<p class="message">'.translate('dataSaved').'</p>';
  if (isset($_POST['username']) && ($id1==$id2)) $_SESSION['user_username'] = $_POST['username'];
}
return $rz;
}

// ��������� �� ������� � ������� ����� �� �����������

function userreg_mydata($t){
global $userreg_locked, $user_table;
$uid = userreg_id($t);
// ����� ����� �� �����������
$d = db_select_1('*', $user_table, "`ID`=$uid");
$cp = array(
'username'=>translate('user_username'),
'email'=>translate('user_email'),
'firstname'=>translate('user_firstname'),
'secondname'=>translate('user_secondname'),
'thirdname'=>translate('user_thirdname'),
'country'=>translate('user_country'),
'institution'=>translate('user_institution'),
'address'=>translate('user_address'),
'telephone'=>translate('user_telephone')
);
$rz = '<h2>'.translate('conference_mydata').'</h2>
'.view_record($d, $cp);
if(!isset($userreg_locked) || ($userreg_locked!==true)){
  // ����� �� ���������� �� �������
  $plogin = stored_value('userreg_login_'.$t);
  // ����� �� ���������� �� ����������� �� ������� �����
  $pedit = str_replace('&user2=login', '&user2=edit', $plogin);
  $rz .= '<p><a href="'.$pedit.'">'.translate('conference_mypersonal').'</a></p>';
}
return $rz;
}

?>
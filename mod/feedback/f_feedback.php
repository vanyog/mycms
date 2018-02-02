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

// ����� �� ������� ����������.
// ���������� �� ��������� ��������� �� ������� � ������� feedback � �� ������� �� ����� translate("feedback_to_$page_id")
// ��� �� ������ �� ����������� � ����� $_GET['uid'].
// ��������� $t � ����� �����������.
// �� ��������� �� ������������ � ���-������� ������ �� ���� ������, ����� �� ��������� ���������,
// ���������� �� ������������ �� ���� ��������� � ������ ������.

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_db_insert_1.php');

function feedback($t = ''){

global $page_id, $language;

// �� ���� � ���������� �����������
$to = feedback_to();

// ��� �� � ������� �� ������� ��������� ������ ����� �� ���������
if(!$to) return "<p class=\"message\">No 'feedback_to_$page_id' setting or 'uid' parameter found by FEEDBACK module.</p>";

// ��� � 'no' �� ������� ���������, �� ������� �� � �������
if($to == 'no') return '<p class="message">'.translate('feedback_disabled').'</p>';

if (count($_POST)) return feedback_process($to);

if (!session_id()) session_start();

$ms = ''; // ���������
$nm = ''; // ��� �� ���������
$em = ''; // ����� �� ���������
$sb = ''; // �������. ���� �� �� ������ � ������� 'content'.
$rf = ''; // �� ���� �� ���� �� �������� ��������

$sb = db_table_field('name', 'content', "`name`='feedback_subject_$page_id'");
if ($sb) $sb = translate($sb);

if (isset($_SERVER['HTTP_REFERER'])) $rf = $_SERVER['HTTP_REFERER'];

// ��� ��� ������ ���������� ����� � ������ �� ��������� �� ��������
if (isset($_SESSION['user_username'])&&isset($_SESSION['user_password'])){
  $ud = db_select_1('*', stored_value('user_table', 'users'), 
       "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'" );
  $nm = $ud['firstname'].' '.$ud['secondname'].' '.$ud['thirdname'];
  if (strlen($nm)<3) $nm = '';
  $em = $ud['email'];
  $recapthca = false;
}
else {
  // ���������� �� ������� �� ���� �����������
  $lp = stored_value("userreg_login_$t");
  $recapthca = true;
  if($lp){
     // ��� � �������� https
     if(stored_value('userreg_https')=='on') $lp = 'https://'.$_SERVER['HTTP_HOST'].$lp;
     $ms = translate('feedback_tologin').'<a href="'.$lp.'"><strong>'.translate('userreg_login').'</strong></a>.';
  }
}

$rz = '<h2>'.translate('feedback_to')."$to</h2>\n";

$f = new HTMLForm('feedback_form');

$ti = new FormInput(translate('feedback_yourname'), 'name', 'text', $nm );
$ti->js = ' style="width:99%"';
$f->add_input( $ti );

$ti = new FormInput('', 'referer', 'hidden', $rf );
$ti->js = ' style="width:99%"';
$f->add_input( $ti );

$ti = new FormInput(translate('feedback_youremail'), 'email', 'text', $em );
$ti->js = ' style="width:99%"';
$f->add_input( $ti );

$ti = new FormInput(translate('feedback_subject'), 'subject', 'text', $sb );
$ti->js = ' style="width:99%"';
$f->add_input( $ti );

if(substr($t, 0, 5)=='vsu20'){
  $ti = new FormInput(translate('feedback_publish'), 'publish', 'checkbox', '1', translate('feedback_publish2') );
  $f->add_input( $ti );
}

$ta = new FormTextArea(translate('feedback_text'), 'text' );
$ta->size = false;
$ta->js = ' style="width:99%; height:200px;"';
$ta->ckbutton = '';
$f->add_input( $ta );

if ($recapthca) $f->add_input( new FormRecaptcha( translate('feedback_recaptcha') ) );

$fb = new FormInput('', '', 'button', translate('feedback_submit') );
$fb->js = ' onclick="ifNotEmpty_feedback_form();"';
$f->add_input( $fb );

if ($ms) $ms = '<p class="message">'.$ms."</p>\n";

return $rz.$ms.$f->html();

}

// ��������� ����������� ����� �� ��������� �����.

function feedback_process($to = ''){
global $page_id, $site_encoding;
$d = Array(
'page_id'=>$page_id,
'date_time_1'=>'NOW()',
'name'   => addslashes($_POST['name']),
'email'  => addslashes($_POST['email']),
'subject'=> addslashes($_POST['subject']),
'text'   => addslashes($_POST['text']),
'referer'   => addslashes($_POST['referer']),
'IP'=>$_SERVER['REMOTE_ADDR']
);
if (isset($_POST['publish'])) $d['publish'] = 1*$_POST['publish'];
if ($to){ // ��������� �� ������
  $ea = explode(' ',$_POST['email']); $e = $ea[0];
  $ea = explode("\n",$e); $e = $ea[0];
  $ea = explode("\r",$e); $e = $ea[0];
  $ms = $_POST['text'];
  $sb = str_replace("\r", ' ', $_POST['subject']);
  $sb = str_replace("\n", ' ', $sb);
  $nm = str_replace("\r", ' ', $_POST['name']);
  $nm = str_replace("\n", ' ', $nm);
  $hd = "Content-type: text/plain; charset=$site_encoding\r\n".
        "From: $nm <$e>\r\n";
  if( ! mail($to,$sb,$ms,$hd) )
        return translate('feedback_notsent');
//  db_insert_1($d, 'feedback');
}
return translate('feedback_thanks');
}

function feedback_to(){
global $page_id;
// ID �� ����������
$uid = 0;
if( isset($_GET['uid']) && is_numeric($_GET['uid']) ) $uid = $_GET['uid'];
// ����� �� �����������
if($uid) return db_table_field('email', 'users', "`ID`=$uid");
// �������� ���� ��� ������� ����� �����, �� ����� �� �� �������� ����������� �� �������� ��������
$n = db_table_field('name', 'content', "`name`='feedback_to_$page_id'");
return translate($n, false);
}

?>

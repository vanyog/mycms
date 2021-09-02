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


// ��������� �� ��� ���������� �� ����, ����� ��� ������� ����������

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_insert_1.php');

if(!session_id()) session_start();

// ��� � ������� ���� ����� �� ������ ���������� - ���������, �� ������ �� �� �����
if (!isset($_SESSION['user_username']) || !isset($_SESSION['user_password']) )
   $page_content = '<p class="message">'.translate('userreg_nouserlogedin').'</p>';
else {
  // ������� � ����� �� �����������
  $user_table = stored_value('user_table', 'users');
  // ����� �� ������� ����������
  $id = db_table_field('ID', $user_table,
        "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
  // ��� ������ �� ������� ���������� �� � ������� - ���������, �� ������ �� �� �����
  if (!$id) $page_content = '<p class="message">'.translate('userreg_mustlogin2').'</p>';
  else { //die($id);
    // �������� ���� �������� ���������� ��� ����� �� ������� ���� �����������
    $p = db_table_field('yes_no','permissions',
           "`user_id`=$id AND ((`type`='module' AND `object`='userreg') OR `type`='all')");
    if (!$p) $page_content = '<p class="message">'.translate('userreg_nopermission').'</p>';
    else {
      // ��� �� ������� ����������
      $t = db_table_field('type', $user_table, "`ID`=$id");
      // ��������� �� ��������� �� ����������� �� ��������� � $_POST �����
      $ms = userreg_processnew($t);
      if ($ms) $ms = '<p class="message">'.$ms.'</p>';
      // ����� �� ��������� �� ��� ����������
      $f = new HTMLForm('newuserreg_form');
      $f->add_input( new FormInput('','type','hidden',$t) );
      $f->add_input( new FormInput(translate('user_email'),'email','text') );
      $f->add_input( new FormInput(translate('user_password'),'password','text', rand_string(8)) );
      $f->add_input( new FormInput('','','submit', translate('userreg_create')) );
      $page_content = '<h1>'.translate('userreg_new').'</h1>
'.$ms.$f->html();
    }
  }
}

include($idir.'lib/build_page.php');

//
// ��������� �� ��������� � $_POST �����

function userreg_processnew($t){
// ������ �������� - ��������� ������� �����������
$rz = '';
// ��� �� �� ��������� ����� - ������ ���
if (!count($_POST)) return $rz;
global $user_table;
// �������� ���� ���� ���� ���������� � ��������� �����
$e = addslashes($_POST['email']);
$id = db_table_field('ID', $user_table, "`type`='$t' AND `email`='$e'");
if ($id) return translate('userreg_sameemail');
// �������� �� ��������� �� ��������
if (strlen($_POST['password'])<8) return translate('userreg_pshort');
// ����� �� ��� ����������
$d = array(
  'type'=>addslashes($_POST['type']),
  'date_time_0'=>'NOW()',
  'date_time_1'=>'NOW()',
  'username'=>$e,
  'email'=>$e,
  'password'=>pass_encrypt($_POST['password']),
  'IP'=>$_SERVER['REMOTE_ADDR']
);
die( db_insert_1($d,$user_table, true) );
}

?>

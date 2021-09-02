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


// Създаване на нов потребител от типа, който има влезлия потребител

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_insert_1.php');

if(!session_id()) session_start();

// Ако в сесията няма данни за влязъл потребител - съобщение, че трябва да се влезе
if (!isset($_SESSION['user_username']) || !isset($_SESSION['user_password']) )
   $page_content = '<p class="message">'.translate('userreg_nouserlogedin').'</p>';
else {
  // Таблица с данни за потребители
  $user_table = stored_value('user_table', 'users');
  // Номер на влезлия потребител
  $id = db_table_field('ID', $user_table,
        "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
  // Ако номера на влезлия потребител не е валиден - съобщение, че трябва да се влезе
  if (!$id) $page_content = '<p class="message">'.translate('userreg_mustlogin2').'</p>';
  else { //die($id);
    // Проверка дали влезлият потребител има право да създава нови потребители
    $p = db_table_field('yes_no','permissions',
           "`user_id`=$id AND ((`type`='module' AND `object`='userreg') OR `type`='all')");
    if (!$p) $page_content = '<p class="message">'.translate('userreg_nopermission').'</p>';
    else {
      // Тип на влезлия потребител
      $t = db_table_field('type', $user_table, "`ID`=$id");
      // Съобщение за резултата от обработката на изпратени с $_POST данни
      $ms = userreg_processnew($t);
      if ($ms) $ms = '<p class="message">'.$ms.'</p>';
      // Форма за създаване на нов потребител
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
// Обработка на изпратени с $_POST данни

function userreg_processnew($t){
// Връщан резултат - съобщение относно обработката
$rz = '';
// Ако не са изпратени данни - празен низ
if (!count($_POST)) return $rz;
global $user_table;
// Проверка дали вече няма потребител с посочения имейл
$e = addslashes($_POST['email']);
$id = db_table_field('ID', $user_table, "`type`='$t' AND `email`='$e'");
if ($id) return translate('userreg_sameemail');
// Проверка за дължината на паролата
if (strlen($_POST['password'])<8) return translate('userreg_pshort');
// Данни за нов потребител
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

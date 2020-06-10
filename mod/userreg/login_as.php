<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2017  Vanyo Georgiev <info@vanyog.com>

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


// Влизане в системата вместо друг потребител

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_insert_1.php');
include_once($idir.'lib/f_rand_string.php');

if(!session_id()) session_start();

// Ако в сесията няма данни за влязъл потребител - съобщение, че трябва да се влезе
if (!isset($_SESSION['user_username']) || !isset($_SESSION['user_password']) )
   $page_content = '<p class="message">'.translate('userreg_mustlogin2').'</p>';
else {
  // Таблица с данни за потребители
  $user_table = stored_value('user_table','users');
  // Номер на влезлия потребител
  $ud = db_select_1('ID,type', $user_table,
        "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
  // Ако номерът на влезлия потребител не е валиден - съобщение, че трябва да се влезе
  if (!$ud) $page_content = '<p class="message">'.translate('userreg_mustlogin2', false).'</p>';
  else { //die("$id");
    // Проверка дали влезлият потребител има право да влиза от името на други потребители
    $a = db_table_field('yes_no','permissions',"`user_id`=".$ud['ID']." AND `type`='all'");
    $p = db_table_field('yes_no','permissions',"`user_id`=".$ud['ID']." AND `type`='module' AND `object`='userreg'");
    if ( !$a && ($p!=1) ) $page_content = '<p class="message">'.translate('userreg_nopermission2').'</p>';
    else {
      if(!isset($_GET['uid'])) $page_content = '<p class="message">'.translate('userreg_nouid', false).'</p>';
      else {
        // Номер на потребителя, в чиито профил ще се влезе
        $ui = 1*$_GET['uid'];
        // Данни на потребителя
        $d = db_select_1('*', $user_table, "`ID`=$ui");
        if(!$d) $page_content = '<p class="message">'.translate('userreg_nouexist', false).'</p>';
        else {
           if( !$d['username'] || !$d['password'] )
              $page_content = '<p class="message">'.translate('userreg_nonameorpass').'</p>';
           else {
              // Адрес на страницата за редактиране на данните на потребители от съответния тип
              $p = stored_value('userreg_login_'.$ud['type'], "/index.php?pid=386");
              if (!session_id()) session_start();
              $_SESSION['user_username'] = addslashes($d['username']);
              $_SESSION['user_password'] = $d['password'];
//              header("Location: $p"); die;
              $page_content = "<p> <a href=\"$p\">$p</a></p>";
           }
        }

      }
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
  'creator_id'=>$_POST['creator_id'],
  'type'=>addslashes($_POST['type']),
  'date_time_0'=>'NOW()',
  'date_time_1'=>'NOW()',
  'username'=>$e,
  'email'=>$e,
  'password'=>pass_encrypt($_POST['password']),
  'IP'=>$_SERVER['REMOTE_ADDR']
);
return translate('userreg_newid').db_insert_1($d,$user_table);
}

?>

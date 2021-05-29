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


// Създаване на нови потребители от типа, който има влезлия потребител

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_insert_m.php');

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
      $i = new FormTextArea(translate('user_emails'),'emails');
      $i->ckbutton = '';
      $f->add_input( $i );
      $f->add_input( new FormInput('','','submit', translate('userreg_create')) );
      $page_content = '<h1>'.translate('userreg_bnew')."</h1>\n".
                      '<p>User type: '.$t."</p>\n".
                      translate('userreg_bdescr')."\n".
                      $ms.$f->html();
    }
  }
}

if(!isset($page_header)) $page_header = '';

$page_header .= '<style>
th { vertical-align:top; }
</style>
';

include($idir.'lib/build_page.php');

//
// Обработка на изпратени с $_POST данни

function userreg_processnew($t){
// Връщан резултат - съобщение относно обработката
$rz = '';
// Ако не са изпратени данни - празен низ
if (!count($_POST)) return $rz;
global $user_table, $id;
// Разделяне на имейлите
$es = explode("\n", $_POST['emails']);
if(count($es)==1) $es = explode(',', $_POST['emails']);
$d = array();
$w = 0;
foreach($es as $e){
  // Проверка дали вече няма потребител с имейл $e
  $e1 = strtolower(trim($e));
  $i = db_table_field('ID', $user_table, "`type`='$t' AND `email` LIKE '$e1'");
  if (!$i && ($e1>' ') ){
     // Проверка дали не е в списъка от използвани вече грешни имейли
     $e2 = db_table_field('email', 'mail_wrong', "`email`='$e1'");
     if($e2){
        $w++;
        $rz .= "$e1 - in the wrong emails list<br>";
     }
     // Данни за нов потребител
     else $d[] = array(
            'creator_id'=>$id,
            'type'=>addslashes($_POST['type']),
            'date_time_0'=>'NOW()',
            'date_time_1'=>'NOW()',
            'username'=>$e1,
            'email'=>$e1
          );
  }
}
return "<p>$w<br>$rz</p>\n<pre>\n".count($d)."\n".db_insert_m($d, $user_table, true)."<pre>\n";
}

?>

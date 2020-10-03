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


// Изтриване на потребители от типа, който има влезлия потребител
//

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_insert_m.php');
include_once($idir.'lib/f_view_table.php');
include_once($idir.'lib/f_set_self_query_var.php');

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
      // Номер на поредния подлежащ на изтриване акаунт
      $tid = 0;
      // Съобщение за резултата от обработката на изпратени с $_POST данни
      $ms = userreg_processdel($t, $tid);
      if ($ms) $ms = '<p class="message">'.$ms.'</p>';
      // Изпратени данни
      if(isset($_POST['emails'])) $emls = trim($_POST['emails']); else $emls = '';
      // Форма за спискът имейли за изтриване
      $f = new HTMLForm('newuserreg_form');
      $f->add_input( new FormInput('', 'type','hidden', $t) );
      $f->add_input( new FormInput('', 'tid','hidden', $tid) );
      if($tid){
        $i = new FormInput(translate('userreg_bcheck'), 'todel', 'checkbox', $tid);
        // Данни за потребителя
        $dt = db_select_1('*', $user_table, "`ID`=$tid");
        // Поставяне на отметка за изтриване, ако не е влизал никога
        if( ($dt['date_time_2']=='0000-00-00 00:00:00') &&
            ($dt['date_time_1']==$dt['date_time_1']) &&
            empty($dt['password'])
          ) $i->js = ' checked="checked"';
        $f->add_input($i);
      }
      $i = new FormTextArea(translate('user_emails'), 'emails', 100, 10, $emls);
      $i->ckbutton = '';
      $f->add_input( $i );
      $f->add_input( new FormInput('','','submit', translate('userreg_delete')) );
      $page_content = '<h1>'.translate('userreg_bdel')."</h1>\n".
                      '<p>User type: '.$t."</p>\n".
                      translate('userreg_bdeldescr').
                      $ms.$f->html().
                      translate('userreg_bdelHelp')."\n";                      ;
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
// $t - тип потребител
// $tid - номер на потребител за обработка

function userreg_processdel($t, &$tid){
global $tn_prefix, $db_link;
// Връщан резултат - съобщение относно обработката
$rz = '';
// Ако не са изпратени данни - празен низ
if (!count($_POST)) return $rz;
// Имейла за изтриване
$e = db_table_field('email', 'users', "`ID`=".$_POST['tid']);
// Ако е поставена отметка за изтриване
if(isset($_POST['todel'])){ // При отметка да се изтрие
  if(($_POST['tid']>0) && isset($_POST['todel']) && ($_POST['todel']==$_POST['tid'])){
    // Премахване от списъка
    $_POST['emails'] = preg_replace('/'.$e.'\n?/s', '', $_POST['emails']);
    // Премахване от базата данни
    db_delete_from('users', $_POST['tid']);
    // Добавяне в таблица email_wrong
    $q = "INSERT INTO `$tn_prefix"."mail_wrong` (`email`) VALUES ('$e')";
    mysqli_query($db_link, $q);
  }
}
else { // Ако няма отметка за изтриване се премахва само от списъка за да се мине към следващия имейл
  $_POST['emails'] = preg_replace('/'.$e.'\n?/s', '', $_POST['emails']);
}
global $user_table, $id, $adm_pth;
// Разделяне на имейлите
$es = preg_split('/\r\n|\r|\n/', $_POST['emails']);
if(count($es)==1) $es = explode(',', $_POST['emails']);
$d = array();
$rz .= "<p>".count($es)." emails</p>\n";
foreach($es as $e){
  // Проверка дали има потребител с имейл $e
  $e1 = trim($e);
  if(!empty($e1)){
    $d = db_select_1('*', $user_table, "`type`='$t' AND `email`='$e1'");
    if(!$d){
      $rz .= "$e1 - no profile exists<br>\n";
      $_POST['emails'] = preg_replace('/'.$e.'\n?/s', '', $_POST['emails']);
    }
    else if($d['nomessage']) {
      $rz .= "$e1 - messages are desabled<br>\n";
      $_POST['emails'] = preg_replace('/'.$e.'\n?/s', '', $_POST['emails']);
    }
    else {
      $tid = $d['ID'];
      $d['ID'] = $d['ID'].' <a href="'.$adm_pth.'edit_record.php?t='.$user_table.'&r='.$d['ID'].'" target="_blank">*</a>';
      // Проверка дали има публикации
      $rcds = db_select_m('*', 'proceedings', "`user_id`=$tid");
      if(count($rcds)){
        $rz .= encode('<p>Има '.count($rcds).' публикации !</p>')."\n";
        foreach($rcds as $r) $rz .= '<p>'.$r['title']."</p>\n";
      }
      return $rz.view_table(array($d));
    }
  }
}
return $rz;
}

?>

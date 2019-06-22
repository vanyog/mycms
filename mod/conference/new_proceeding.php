<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Създаване на нов запис за доклад на участник с номер $_GET['uid']

error_reporting(E_ALL); ini_set('display_errors',1);

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'mod/usermenu/f_usermenu.php');
//include($idir.'lib/translation.php');
include($idir.'lib/f_db_insert_1.php');

// Проверяване правата на потребителя 
$tx = usermenu(true);

// Номер на участника, автор на доклад
$uid = 1*$_GET['uid'];

if (isset($can_manage['conference']) && $can_manage['conference']){
  // Тип на потребителите 
  $utype = stored_value('conference_usertype', 'vsu2014');  // Данни за доклад
  $d = array( 'user_id'=>$uid, 'utype'=>$utype, 'date_time_1'=>'NOW()' );
  // Запазване на данните
  db_insert_1($d,'proceedings');
}

// Адрес на който да се върне
$b = $_SERVER['HTTP_REFERER'].'#pof'.$uid;
header('Location: '.$b);

?>

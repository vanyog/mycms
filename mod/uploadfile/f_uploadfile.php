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

// Модул за качване на файлове
// Функцията uploadfile($n) генерира html кода за показване на
// качен на сървъра файл.
// В режим на редактиране зад хипервръзката се показват знаци:
// +  за качване на файл и
// -  за изтриване на качения файл.

include_once($idir."lib/f_db2user_date_time.php");

global $can_manage;

function uploadfile($n){
global $mod_pth, $page_id, $page_data;

$n = stripslashes($n);

// Проверка за наличие на style атрибут
$ss = ''; $m = array();
$i = preg_match_all('/,style=".*"/', $n, $m);
if (!$i) $i = preg_match_all('/,style=&quot;.*&quot;/', $n, $m);
if ($i==1){
 $ss = $m[0][0];
 $n  = str_replace($ss,'', $n);
 $ss = str_replace('&quot;','"',$ss);
 $ss[0] = ' ';
}

// Проверка за наличие на img опция
$add_image = false;
$i = preg_match_all('/,img/', $n, $m);
if ($i){
  $add_image = true;
  $n = str_replace(',img','',$n);
}

// Проверка за наличие на show-t-s опция
$add_time = false;
$add_size = false;
$i = preg_match_all('/,show(-[ts])(-[ts])?/', $n, $m);
if ($i){
  switch ($m[1][0]){
  case '-t': $add_time = true; break;
  case '-s': $add_size = true; break;
  }
  if(isset($m[2][0])) switch ($m[2][0]){
  case '-t': $add_time = true; break;
  case '-s': $add_size = true; break;
  }
  $n = str_replace($m[0],'',$n);
}

// Разпадане на параметъра $a на: име, номер на страница и опция за показване на текста
$na = explode(',',$n);

// За всеки случай, ако не е дефиниран номер на страница.
if (!isset($page_id)) $page_id = 1*$_GET['pid'];
$pid = $page_id;

// Ако е изпратен и номер на страница - коригиране на $n и $pid
if (isset($na[1])){ $pid = 1*$na[1]; $n = $na[0]; }

// Връщан резултат
$rz = '';

// Четене на данните за файла
$fr = db_select_1('*','files',"`pid`=$pid AND `name`='$n'");// print_r($fr); die;

$ne = true; // Флаг, който ако е истина файлът не се показва
$imgs = array('jpg','jpeg','gif','png','svg'); // Разширения на файлове - изображения

// $show_text - Дали да се показва текст
if (isset($na[2])) $show_text = $na[2];
else $show_text = (stored_value('uploadfile_nofilenotext','false')!='true');

if (!$fr){ // Ако няма данни за файл - надпис "Няма качен файл" или нищо
  if ($show_text||in_edit_mode()) $rz .= translate('uploadfile_nofile');
  $fid = 0;
}
else {
  // Проверка дали файлът не идва от друг сървър
  $l = strlen($_SERVER['DOCUMENT_ROOT']);
  // document_root деректорията на другия сървър, зададена с настройката uploadfile_otherroot
  $or = stored_value('uploadfile_otherroot');
  // Път до файла на този сървър
  $thfn = $fr['filename'];
  if ($or){
    $l = strlen($or);
    // Истина, ако файлът не е бил в document_root на другия сървър
    $ne = $or != substr($fr['filename'], 0, $l);
    if(!file_exists($thfn)) $thfn = $_SERVER['DOCUMENT_ROOT'].substr($fr['filename'],$l);
  }
  if ($ne){
    $l = strlen($_SERVER['DOCUMENT_ROOT']);
    // Истина ако не е в document_root и на този сървър
    $ne = $_SERVER['DOCUMENT_ROOT'] != substr($fr['filename'], 0, $l);
  }
  // href - атрибут на файла
  $f = substr($fr['filename'],  $l, strlen($fr['filename'])-$l);
  $f = str_replace(' ', '%20', $f);
  $f = str_replace('_', '%5F', $f);
  // Дали файлът е във време за показване
  $t1 = strtotime(str_replace('-','/',$fr['date_time_3']));
  $t2 = strtotime(str_replace('-','/',$fr['date_time_4']));
  $t3 = time()+3600; //die("$t1 $t2 $t3");
  $cs = ( (!$t1 || ($t1<0) || ($t3>$t1)) && (!$t2 || ($t2<0) || ($t3<$t2)) );
//  echo "$t1<br>".date("Y-m-d H:i:s", $t3)."<br>$t2<br><br>";
  // Ако няма файл или е извън DOCUMENT_ROOT, или не е във време за показване
  if ( (!$fr['filename'] || $ne || !($cs || (isset($na[2])&&($na[2]==3)) ) ) && !in_edit_mode() ){
    // Показване на текста на връзката, "няма качен файл" или нищо
    if (in_edit_mode()) $rz .= stripslashes($fr['text']);
    else switch ($show_text){
    case '0': $rz .= ''; break;
    case '1': $rz .= stripslashes($fr['text']); break;
    case '2': $rz .= translate('uploadfile_nofile'); break;
    }
  }
  else { // Показване на картинка или хипервръзка към файла
    $e = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (in_array($e, $imgs)){
      $rz .= '<img src="'.$f."\"$ss alt=\"".stripslashes($fr['text']).'" id="'.$fr['name'].'">';
      $page_data['last_img']=$f;
    }
    else {
       $rz .= '<a href="'.$f."\"$ss>".upload_file_addimage($add_image,$e).stripslashes($fr['text']).'</a>';
       if(!$cs && isset($na[2]) && ($na[2]==3)) $rz .= translate('uploadfile_old');
       if($add_time || $add_size) $rz .= ' -';
       if($add_time && file_exists($thfn)){
         $ft =  date("Y-m-d H:i:s", filemtime($thfn));
         $rz .= " ".db2user_date_time($ft);
       }
       if($add_size && file_exists($thfn))
         $rz .= ", ".filesize($thfn)." bytes";
    }
  }
  $fid = $fr['ID'];
}

// В режим на редактиране се показват знаци + - за качване-изтриване на файла
if (in_edit_mode() || can_upload()){
  $cp = current_pth(__FILE__);
  $rz .= ' <a href="'.$cp."upload.php?pid=$pid&amp;fid=$fid&amp;fn=$n"."\" title=\"Update\">+</a>\n";
  if ( isset($fr['filename']) && $fr['filename'] && !$ne )
    $rz .= ' <a href="'.$cp."delete.php?fid=$fid".'" title="Delete" onclick="return confirm(\''.
      translate('uploadfile_confdel').$f.' ?\');">-</a>'."\n";
}

return $rz;
}

// Добавяне на картинка ако $add_image==true;
function upload_file_addimage($add_image,$e){
  if (!$add_image) return '';
  $p = current_pth(__FILE__).'images/'.$e.'.png';
  $a = $_SERVER['DOCUMENT_ROOT'].$p;
  if (file_exists($a)) return '<img alt="'.$e.'" src="'.$p.'"> ';
}

// Проверяване дали потребителят има право да качва, сменя и изтрива файлове
function can_upload(){
global $can_manage, $can_edit;
if ($can_edit) return in_edit_mode();
else {
  return isset($can_manage['uploadfile']) && ($can_manage['uploadfile']==1);
}
}

?>

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
global $mod_pth, $page_id, $page_data, $page_header;

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

// Проверка за наличие на link опция
$just_link = false;
$i = preg_match_all('/,link/', $n, $m);
if ($i){
  $just_link = true;
  $n = str_replace(',link','',$n);
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
if (isset($na[1])){
  $pid = intval($na[1]);
  $n = $na[0];
}

// Връщан резултат
$rz = '';

// Четене на данните за файла
$fr = db_select_1('*','files',"`pid`=$pid AND `name`='$n'"); //print_r($fr); die;

$ne = true; // Флаг, който ако е истина файлът не се показва
$imgs = array('jpg','jpeg','jp2','gif','png','svg', 'webp'); // Разширения на файлове - изображения

// $show_text - Дали да се показва текст
if (isset($na[2])) $show_text = $na[2];
else $show_text = (stored_value('uploadfile_nofilenotext','false')!='true');

$inEditMode = in_edit_mode();

if (!$fr){ // Ако няма данни за файл - надпис "Няма качен файл" или нищо
  if ($show_text || $inEditMode) $rz .= translate('uploadfile_nofile');
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
    if(!$ne) $thfn = $_SERVER['DOCUMENT_ROOT'].substr($fr['filename'],$l);
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
  if ( (!$fr['filename'] || $ne || !($cs || (isset($na[2])&&($na[2]==3)) ) ) && !$inEditMode ){
    // Показване на текста на връзката, "няма качен файл" или нищо
    if ($inEditMode) $rz .= stripslashes($fr['text']);
    else switch ($show_text){
    case '0': $rz .= ''; break;
    case '1': $rz .= stripslashes($fr['text']); break;
    case '2': $rz .= translate('uploadfile_nofile'); break;
    }
  }
  else {
    if($just_link){
      return $f;
    }
    // Показване на изображение или хипервръзка към файла
    $e = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    // Изображение
    if (in_array($e, $imgs)){
      // За съвместимост .webp файловете, трябва да имат и .jp2 и .jpg варианти
      if($e=='webp'){//die($_SERVER['HTTP_USER_AGENT']);
         $fn = $f;
         if((strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')>0) &&
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')===false) ) $fn = substr($f,0,-4).'jp2';
         if(strpos($_SERVER['HTTP_USER_AGENT'], 'Edge'  )>0) $fn = substr($f,0,-4).'jpg';
         if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIЕ'  )>0) $fn = substr($f,0,-4).'jpg';
         $f = $fn;
      }
      // Ако е инсталиран скрипта lazysizes.min.js
      if(strpos($page_header, 'lazysizes.min.js')>0){
         $rz .= '<img data-src="'.$f."\"$ss alt=\"".stripslashes($fr['text']).'" id="'.$fr['name'].'" class="lazyload">';
	  }
      else {
         // Опит за установяване на размерите
         $szst = '';
         $inf = getimagesize($thfn); 
         if($inf){ // При успешен опит
            if(!$ss){ // Ако няма атрибут style се добавя такъв
               $ss = 'style="width:'.$inf[0].'px; height:'.$inf[1].'px;"';
            }
            else { // Ако има атрибут style:
              // но в него няма width се добавя.
              if(strpos($ss,'width:')===false)  $ss = substr($ss,0,-1).'width:'. $inf[0].'px;"';
              // Ако няма и height, също се добавя.
              if(strpos($ss,'height:')===false) $ss = substr($ss,0,-1).'height:'.$inf[1].'px;"';
            }
         }
         $rz .= '<img src="'.$f."\"$ss alt=\"".stripslashes($fr['text']).'" id="'.$fr['name'].'">';
      }
      if(!isset($GLOBALS['og_image'])) $GLOBALS['og_image']=$f;
    }
    // Друг файл
    else if($e=='mp4'){
       $rz .= "<video onloadeddata=\"this.play();\" onloadedmetadata=\"this.muted = true\"$ss playsinline muted loop>\n".
              '<source src="'.$f.'" type="video/mp4">'."\n".
              'Your browser does not support the video tag.'."\n".
              '</video>';
    }
    else {
       $rz .= '<a href="'.$f."\"$ss>".upload_file_addimage($add_image,$e).stripslashes($fr['text']).'</a>';
       if(!$cs && isset($na[2]) && ($na[2]==3)) $rz .= translate('uploadfile_old');
//       if($add_time || $add_size) $rz .= ' -';
       if($add_time && file_exists($thfn)){
         $ft =  date("Y-m-d H:i:s", filemtime($thfn));
         if($fr['text']) $rz .= ", ";
         $rz .= db2user_date_time($ft);
       }
       if($add_size && file_exists($thfn)){
         if($fr['text']) $rz .= ",";
         $rz .= " ".upload_file_bBKM(filesize($thfn));
       }
    }
  }
  $fid = $fr['ID'];
}

// В режим на редактиране се показват знаци + - за качване-изтриване на файла
if (can_upload()){
  $cp = current_pth(__FILE__);
  $rz .= ' <a href="'.$cp."upload.php?pid=$pid&amp;fid=$fid&amp;fn=$n"."\" title=\"Update\">+</a>\n";
  if ( isset($fr['filename']) && $fr['filename'] && !$ne )
    $rz .= ' <a href="'.$cp."delete.php?fid=$fid".'" title="Delete" onclick="return confirm(\''.
      translate('uploadfile_confdel').$f.' ?\');">-</a>'."\n";
}

return $rz;
}

function upload_file_bBKM($s){
if($s>1000000) return number_format($s/1000000,3)." MB";
if($s>1000) return number_format($s/1000,3)." KB";
return "$s bytes";
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
if ($can_edit || show_adm_links()) return in_edit_mode();
else {
  return isset($can_manage['uploadfile']) && ($can_manage['uploadfile']==1);
}
}

?>

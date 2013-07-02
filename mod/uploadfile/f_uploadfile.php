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
// Функцията uploadfile($n) генерира html кода на хипервръзка към
// качен на сървъра файл.
// В режим на редактиране зад хипервръзката се показват знаци:
// +  за качване на файл и 
// -  за изтриване на качения файл.

function uploadfile($n){
global $mod_pth, $page_id;

// За всеки случай, ако не е дефиниран номер на страница.
if (!isset($page_id)) $page_id = 1*$_GET['pid'];
$pid = $page_id;

// Разпадаме на параметъра на име и номер.
$na = explode(',',$n);

// Ако е изпратен и номер на страница - коригиране на $n и $pid
if (isset($na[1])){ $pid = 1*$na[1]; $n = $na[0]; }

// Връщан резултат
$rz = '';

// Четене на данните за файла
$fr = db_select_1('*','files',"`pid`=$pid AND `name`='$n'");

$ne = false; // Флаг, който е истина, ако файла се намира в DOCUMENT_ROOT
$imgs = array('jpg','gif','png'); // Разширения на файлове - изображения

// $show_text - Дали да се показва текст
if (isset($na[2])) $show_text = $na[2];
else $show_text = (stored_value('uploadfile_nofilenotext','false')!='true');

if (!$fr){ // Ако няма данни за файл - надпис "Няма качен файл" или нищо
  if ($show_text||in_edit_mode()) $rz .= translate('uploadfile_nofile');
  $fid = 0;
}
else {
  $l = strlen($_SERVER['DOCUMENT_ROOT']);
  $ne = $_SERVER['DOCUMENT_ROOT'] != substr($fr['filename'], 0, $l);
  $f = substr($fr['filename'], $l, strlen($fr['filename'])-$l);
  // Ако няма файл или е извън DOCUMENT_ROOT
  if (!$fr['filename'] || $ne){ // Показване на надпис "Няма качен файл" или нищо
    if ($show_text) $rz .= stripslashes($fr['text']);
  }
  else { // Показване на картинка или хипервръзка към файла
    $e = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (in_array($e, $imgs)) $rz .= '<img src="'.$f.'" alt="'.stripslashes($fr['text']).'">';
    else $rz .= '<a href="'.$f.'">'.stripslashes($fr['text']).'</a>';
  }
  $fid = $fr['ID'];
}

// В режим на редактиране се показват знаци + - за качване-изтриване на файла
if (in_edit_mode()){
  $cp = current_pth(__FILE__);
  $rz .= ' <a href="'.$cp."upload.php?pid=$pid&amp;fid=$fid&amp;fn=$n"."\" title=\"Update\">+</a>\n";
  if ( isset($fr['filename']) && $fr['filename'] && !$ne ) 
    $rz .= ' <a href="'.$cp."delete.php?fid=$fid".'" title="Delete" onclick="return confirm(\''.
      translate('uploadfile_confdel').$f.' ?\');">-</a>'."\n";
}

return $rz;
}

?>
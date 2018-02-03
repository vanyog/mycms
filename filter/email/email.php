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

// Този филтър загражда срещаните в текста имейли с <a href="mailto:"></a> тагове,
// но ако е зададена опция 'filter_email_toimage' със стойност 'on':
// 1. имейл адресът се превръща в .png изображение, което се съхранява в директорията за качване на файлове, 
//    зададена за модул UPLOADFILE
// 2. проверевя се дали във таблица `users` има потребител с такъв имейл, ако няма се добавя
// 3. върху изображението с имейла се поставя линк към страница с FEEDBACK модул, с който може да се
//    изпрати съобщение до потребителя.
//    Номерът на страницата с FEEDBACK модул трябва да е зададен в настройка 'filter_email_pageid'.

include_once($idir.'lib/f_db_insert_1.php');

function email($t){
$search = '/([a-zA-Z0-9\._-]+@[a-z\-]+(?:.[a-z-]+){1,2})/';
$im = stored_value('filter_email_toimage');
if($im=='on') return preg_replace_callback($search, 'email_image', $t);
$rz = preg_replace($search, '<a href="mailto:\1">\1</a>', $t);
return $rz;
}

function email_image($a){
global $main_index;
// Номер на потребител, с имейл
$uid = db_table_field('ID', 'users', "`email`='".$a[0]."'");
// Ако няма се създава
if(!$uid){
  $uid = db_insert_1(
    array(
        'date_time_0'=>'NOW()',
        'date_time_1'=>'NOW()',
        'username'=>$a[0],
        'email'=>$a[0]
    ),
    'users'
    );
}
// Номер на страница с FEEDBACK модул.
$p = stored_value('filter_email_pageid');
if(!$p) die("No 'filter_email_pageid' setting found by email filter");
// Директория с имейли-картинки
$imd = $_SERVER['DOCUMENT_ROOT'].stored_value('uploadfile_dir');
if(!is_writable($imd)) die("Folder '$imd' is not writable.");
$rz = "<a href=\"$main_index?pid=$p&uid=$uid\">".text_to_imagefile($a[0], $imd.'/email_'.$uid.'.png')."</a>";
return $rz;
}

function text_to_imagefile($t, $f){
// Ако файл $f съществува се връща URL-а му
$pt = substr($f, strlen($_SERVER['DOCUMENT_ROOT']) );
$rz = '<img src="'.$pt.'" alt="&#64;" class="emimage">';
if(file_exists($f)) return $rz;
// Ако файлът не съществува се създава
$im = imagecreatetruecolor(strlen($t)*12, 21);//die($im);
$black = imagecolorallocate($im, 255, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);
imagefill($im, 0, 0, $white);
if(!imagettftext($im, 15, 0, 1, 16, $black, __DIR__.'/courier.ttf', $t )) die;
//header('Content-type: image/png'); imagepng($im); die;
imagepng($im, $f);
imagedestroy($im);
return $rz;
}

?>
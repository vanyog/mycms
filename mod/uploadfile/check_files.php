<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Проверява файловете от таблица 'files'

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/translation.php');

$da = db_select_m('*', 'files', '1');
$in = $main_index.'?pid=';

foreach($da as $d){
  // Ако файлът няма име, най-вероятно е изтрит
  if (!$d['filename']){
    // Проверка дали се използва на страницата
    // Име на съдържанието на страницата
    $cn = db_table_field('content', 'pages', "`ID`=".$d['pid']);
    if (!$cn) break;
    foreach($languages as $language=>$c){
      // Линк към страницата
      $lk = '<a href="'.$in.$d['pid'].'">'.$d['pid'].'</a>';
      // Съдържание на страницата на език $language
      $ct = db_table_field('text', 'content', "`name`='$cn' AND `language`='$language'");
      $p = strpos($ct, '!--$$_UPLOADFILE_'.$d['name']); 
      if ($p===false) echo 'Not in use - '.$lk.' '.$d['name']." ".$language."<br>";
    }
//    echo $cn.' !--$$_UPLOADFILE_'.$d['name'].'<br>';
  }
}

?>

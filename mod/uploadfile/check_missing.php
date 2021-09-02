<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2018 Vanyo Georgiev <info@vanyog.com>

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

// Открива на кои страници няма качени файлове

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/translation.php');

// Записи от таблица content, в които има места за качване на файлове
$da = db_select_m('*', 'content', '`text` LIKE \'%!--$$_UPLOADFILE_%\'', false);

// Забележка: Това не изчерпва всички възможни места за качване на файлове. 
// Някои може да са създадени от шаблони от таблица templates, а други - от модули! 
// Настоящият скрипт трябва да се допълни с проверяване и на тези места.

// Масив с намерените страници:
$pgs = array();

// Обработка на всеки запис
foreach($da as $d){
  // Определяне номера на страницата
  $pid = db_table_field('ID','pages',"`content`='".$d['name']."'");
  // Ако не е съдържание на страница
  if(!$pid){
    // Търсене на записи, в които е вмъкнат този текст
    $na = db_select_m('*', 'content', '`text` LIKE \'%!--$$_CONTENT_'.$d['name'].'_%\'');
    foreach($na as $nd){
      // Номер на страница
      $pid = db_table_field('ID','pages',"`content`='".$nd['name']."'");
      check_file($pid, $d);
    }
  }
  check_file($pid, $d);
}

$rz = ''; //die(print_r($pgs,true));
foreach($pgs as $k=>$p) $rz .= "<a href=\"$main_index?pid=$k\">$k</a> $p, ";
if(!$rz) $rz = "No missing files in pages's content.";
echo $rz;

//================ Functions ================


function check_file($pid, $d){
global $pgs;
  $m = array();
  $i = preg_match_all('/--\$\$_UPLOADFILE_(.*?)[,_]/si', $d['text'], $m);
  
  if($i) foreach($m[1] as $n) {
    $na = explode(',', $n);
    if(isset($na[1]) && is_numeric($na[1])) $pid = $na[1];
    // Четене на запис от таблица 'files'
    $f = db_select_1('*', 'files', "`pid`=$pid AND `name`='".$na[0]."'", true);
     if($n=="A379"){ var_dump($f); die; }
    if(!$f && ($f['pid']==$pid)) $pgs[$pid] = $n;
  }
}
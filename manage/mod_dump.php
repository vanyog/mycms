<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

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

// Чете надписите с имена, започващи с $_GET['p'] и добавя
// поредица от sql заявки във файл tables.sql в директорията
// на модул с име $_GET['m'].

include_once("conf_manage.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');

if (!isset($_GET['p'])) die("Parameter p=Name_Prefix is not posted");
if (!isset($_GET['m'])) die("Parameter m=Mod_Name is not posted");

$d = db_select_m('*','content',"`name` LIKE '".$_GET['p']."%'");

$q = "INSERT INTO `content` (`name`,`nolink`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
(";

foreach($d as $i=>$a){
 $q .= "'".$a['name']."',".$a['nolink'].",NOW(),NOW(),'".$a['language']."','".
       str_replace("'","\'", str_replace("\r\n",'\r\n',$a['text']) )."'";
 if ($i==count($d)-1) $q .= ");"; else $q .= "),\n(";
}

// Проверка дали модулът е в $mod_pth или в 'mod'
$fd = $_SERVER['DOCUMENT_ROOT'].$mod_pth.$_GET['m'];
if (!file_exists($fd)) $fd = $_SERVER['DOCUMENT_ROOT'].$pth.'mod/'.$_GET['m'];
if (!file_exists($fd)) die("Directory does not exists $d");

$fn = $fd.'/tables.sql';

// Ако файлът не е достъпен за запис
if (file_exists($fn) && !is_writable($fn)){
  header("Content-Type: text/html; charset=windows-1251");
  echo "<p>File $fn is not writeble. Write to it manually.</p>
  <textarea cols=\"130\" rows=\"18\">$q</textarea>";
}
else {
  if (file_exists($fn)) {
    $f = fopen($fn, 'a');
    fwrite($f, "-- --------------------------------------------------------\n");
  }
  else $f = fopen($fn,"w");
  fwrite($f,$q);
  fclose($f);
  echo $q;
}

?>

<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2018  Vanyo Georgiev <info@vanyog.com>

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

// Проверка дали са свалени линковете към pdf файлове в зададена директория

// Директория, която се проверява за изтеглени файлове
//$fpth = "/Volumes/TOSHIBA USB 3.0/sci.vanyog.com-pdf/";
$fpth = "/users/vanyog/Sites/sci.vanyog.com-pdf/";

if(!file_exists($fpth)) die("<b>Path</b> $fpth <b>do not exists.</b>");

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_relative_to.php');

// Четене на данните за линкове към pdf файлове
$da = db_select_m('*', 'outer_links', "`link` LIKE '%.pdf'");

session_start();
unset($_SESSION['can_view_file']);

foreach($da as $d){
  $f1 = basename($d['link']);
  $f2 = basename(urldecode($d['link']));
  $p1 = $fpth.$f1;
  $p2 = $fpth.$f2;
  echo $d['Title'];
  if(file_exists($p1)){
    $r = relative_to($_SERVER['CONTEXT_DOCUMENT_ROOT'].'/', $p1)."/$f1";
    $_SESSION['can_view_file'][] = $r;
//    echo " <a href=\"file:///$r\">$f1</a>";
    echo " <a href=\"$pth"."view.php?file=$r\">$f1</a>";
  }
  else if(file_exists($p2)){
    $r = relative_to($_SERVER['CONTEXT_DOCUMENT_ROOT'].'/', $p2)."/$f2";
    $_SESSION['can_view_file'][] = $r;
    echo " <a href=\"$r\">$f2</a>";
//    echo " <a href=\"$pth"."view.php?file=$r\">$f2</a>";
  }
  else {//  die("<p>$p1<br>$p2");
    echo ' <a href="'.$d['link'].'" style="color:red;">download</a> '.$f2;
  }
  echo ' <a href="/index.php?pid=6&lid='.$d['up'].'"> '.">> </a><br>\n";
}

?>
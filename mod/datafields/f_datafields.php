<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2015  Vanyo Georgiev <info@vanyog.com>

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

// Извличане и показване на данни от таблица на базата данни
// Параметърат $a трябва да съдържа стринг от вида:
// 'Таблица[.поле][|условие|разделител записи|разделител полета]'

function datafields($a = ''){
$b = explode('|',$a);
if (!empty($b[1]))    $w  = stripslashes($b[1]); else $w = '1';
if (isset($b[2]))    $rs  = $b[2];               else $rs = ', ';
if (isset($b[3]))    $fs  = $b[3];               else $fs = ' ';
$c = explode('.',$b[0]);
if (isset($c[1])) $f = $c[1];
else $f = '*';
$n = $c[0];
$r = array();
if($n) $r = db_select_m($f, $n, $w);
$rz = '';
foreach($r as $d){
  if (!$rz) $rz = $rs; else $rz .= $rs;
  foreach($d as $v) if($v) $rz .= "$fs$v";
}
return $rz;
}

?>

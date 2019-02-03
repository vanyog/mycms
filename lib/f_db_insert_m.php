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

// Функцията db_insert_m($d,$t) вмъква масив $d данни
// като множество записи в таблица $t от базата данни.

// Всеки елемент на масива е асоциативен масив, съдържащ стойности на полета за един запис.
// Ако към функцията е изпратен трети параметър $y = true
// функцията връща SQL заявката, без да вмъква запис.
// Ако $y = false функцията връща броя на вмъкнатите записи.

include_once($idir.'lib/usedatabase.php');

function db_insert_m($d,$t,$y=false){
global $tn_prefix, $db_link;
$q = "INSERT INTO `$tn_prefix$t` (";
$ka = array_keys($d[0]);
foreach($ka as $k) $q .= "`$k`, ";
$q = substr($q,0,strlen($q)-2).") VALUES\n";
$c = 0;
foreach($d as $r){
 $q .= "(";
 foreach($r as $n=>$v) 
   if ($v=='NOW()') $q .= "$v,";
   else $q .= "'$v',";
 $q = substr($q,0,strlen($q)-1)."),\n";
}
$q = substr($q,0,strlen($q)-2).";";
if ($y) return $q;
else{
 mysqli_query($db_link,$q);
 return $c;
}
}

?>

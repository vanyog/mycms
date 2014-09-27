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

// Функцията db_insert_1($d,$t) вмъква асоциативния масив $d,
// като един запис в таблица $t от базата данни.
// Ако към функцията е изпратен трети параметър $y = true,
// функцията само връща SQL заявката, без да вмъква запис.
// Ако $y = false, функцията връща номера на вмъкнатия запис.

include_once($idir.'lib/usedatabase.php');

function db_insert_1($d,$t,$y=false){
global $tn_prefix, $db_link, $db_req_count;
$q = "INSERT INTO `$tn_prefix$t` SET ";
foreach($d as $n=>$v){
  if ($v=='NOW()') $q .= "`$n`=$v,";
  else $q .= "`$n`='".addslashes($v)."',";
}
$q = substr($q,0,strlen($q)-1).";";
if ($y) return $q;
else{
 mysqli_query($db_link,$q);
 $db_req_count++;
 return mysqli_insert_id($db_link);
}
}

?>

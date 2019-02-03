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

// ��������� db_update_where($d,$t,$w) ��������� �������� � ������� $t
// � ������� �� ����� $d, ��� �������� ��������� �� ��������� $w.

// ��� ��� ��������� � �������� �������� ��������� $y = true
// ��������� ����� SQL ������, ��� �� ������� ������.

include_once($idir.'lib/usedatabase.php');

function db_update_where($d,$t,$w,$y=false){
global $tn_prefix, $db_link, $db_req_count;
$q = "UPDATE `$tn_prefix$t` SET ";
foreach($d as $n=>$v){
  if ($n=='ID') continue;
  if ( ($v=='NOW()') || ($v=='NULL') ) $q .= "`$n`=$v,";
  else $q .= "`$n`='".addslashes($v)."',";
}
$q = substr($q,0,strlen($q)-1)." WHERE $w;";
if ($y) return $q;
else{
 mysqli_query($db_link,$q);
 $db_req_count++;
}
}

?>

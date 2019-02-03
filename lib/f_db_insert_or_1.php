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

// ��������� db_insert_or_1($d,$t,$w) ���������, ���� � ������� 
// $t �� ������ ����� ��� ����� ������������� �� ������� $w.
// ��� ��� ����� ����� �� ����������� � ������� �� ������������ ����� $d,
// � ��� ���� ������ ��� ����� � ������� �� $d.

// ��� ��� ��������� � �������� �������� ���������, ��� ��������:
// $y = 'b' - ��� ���� ����� ������, ��� ��� ����������� (�������� � �������������)
// $y = 'i' - ��� ���� ����� ������, ��� ��� �� �� ����������� (���� ��������)
// $y = 'u' - ��� ���� ����� �� ������, ��� ��� ����������� (���� �������������).

// ������ ��������� ��� � $z=true ���������, ���� ����� SQL ��������, ��� �� �� ����������� ��� ������ �����.

include_once($idir.'lib/usedatabase.php');

function db_insert_or_1($d,$t,$w,$y = 'b',$z = false){
global $tn_prefix, $db_link;
$id = db_table_field('ID',$t,$w);
if ($id) $q = "UPDATE `$tn_prefix$t` SET ";
else $q = "INSERT INTO `$tn_prefix$t` SET ";
foreach($d as $n=>$v){
  if ( ($v=='NOW()') || ($v=='NULL') ) $q .= "`$n`=$v,";
  else $q .= "`$n`='".addslashes($v)."',";
}
$q = substr($q,0,strlen($q)-1);
if ($id) $q .= " WHERE $w;"; else $q .= ";";
if ( ($y=='b') || (($y=='i') && !$id) || (($y=='u') && $id) ) if ($z==true) return $q;
else {
 mysqli_query($db_link,$q);
 return mysqli_insert_id($db_link);
}
}

?>

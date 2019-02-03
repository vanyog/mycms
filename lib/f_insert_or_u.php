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

// ��������� db_insert_or_u($f,$v,$t) ���������, ���� � ������� 
// $t �� ������ ����� ��� ����� ��� �������� $v �� ������ � ��� $f.
// ��� ��� ����� �����, ��������� ���� count � ������� � �������
// �������� ����� � ���� date_time_2, � ��� ���� - ������ ��� ����� 
// ��� ��������� �� �������� ����� � date_time_1 � date_time_2, count=1
// � IP - ������ �� ����������.

// ���������� ��������� $a � ������������ ���� ��� SQL ��������, ����� 
// ���� �� �� �������� �� �������� ��������� �� ����� ������

function db_insert_or_u($f,$v,$t,$a = ''){
global $tn_prefix, $db_link;
$v1 = addslashes($v);
// ����� �� ������, ��� ����������
$id = db_table_field('ID', $t, "`$f`='$v1'");
if ($id){ $q1 = "UPDATE `$tn_prefix"."$t` SET "; $q2 = " WHERE `ID`=$id;"; }
else { $q1 = "INSERT INTO `$tn_prefix"."$t` SET `date_time_1`=NOW(), "; $q2 = ';'; }
$q = $q1."`date_time_2`=NOW(), $a`$f`='$v1', `count`=`count`+1, `IP`='".$_SERVER['REMOTE_ADDR']."'".$q2;
mysqli_query($db_link,$q);
}

?>

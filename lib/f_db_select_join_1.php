<?php

/*
VanyoG CMS - a simple Content Management System
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

// ��������� db_select_join_1($fn,$ta,$tb,$on,$whr,$y = false), ���������� � ���� ����
// ���� �������� $fn, ����� �� ����� �� ����� �� ������� $ta, ���� � �� ����� �� ������� $tb
// �������� �� ������� ������� �� ��������� � a.���������, � �� ������� � - b.���������
// $on � ��������� �� �������������� �� ������ �� ������� �������
// ����������� ����� ������������� ��������� $whr.
// ��������� ����� � ����������� ����� � ���������� �����, ��� false ��� �������.
// ��������� �� ������ �� ������� �� ��������,
// � ����������� - ������������ �� �������� �� ���������.

include_once($idir."lib/usedatabase.php");

function db_select_join_1($fn,$ta,$tb,$on,$whr,$y = false){
global $db_link, $tn_prefix, $db_req_count;
$ta = "`$tn_prefix$ta`";
$tb = "`$tn_prefix$tb`";
$q="SELECT $fn FROM $ta a LEFT JOIN $tb b ON $on WHERE $whr LIMIT 1;";
if ($y) echo "$q<br>\n";
$r=mysqli_query($db_link,$q);
$db_req_count++;
if ($r===false) return false;
$rc=mysqli_fetch_assoc($r);
mysqli_free_result($r);
return $rc;
}

?>
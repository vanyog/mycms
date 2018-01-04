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

// ��������� db_select_m, ���������� � ���� ����
// ���� �������� $fn �� ������ ������ �� ������� $tb,
// ��������������� ��������� $whr.
// ��������� � �� ������ �����, ���������� �� ������������ $db_link
// (��� usedatabase.php).
// ��������� ����� ����� �� ���������� ������,
// ������������� �� ����� �� ����������� ������.
// ��������� �� ������ �� ����� ����� �� ������� �� ��������,
// � ����������� - ������������ �� �������� �� ���������.

include_once($idir."lib/usedatabase.php");

function db_select_m($fn,$tb,$whr,$y=false){
global $db_link, $tn_prefix, $db_req_count;
if( $tb[0]!='`' ) $tb = "`$tn_prefix$tb`";
$q="SELECT $fn FROM $tb WHERE $whr;";
if ($y) echo "$q<br>";
$dbr=mysqli_query($db_link,$q);
$db_req_count++;
$r=array();
if (!$dbr) return $r; 
while ( $rc=mysqli_fetch_assoc($dbr) ){
 $r[]=$rc; //print_r($rc);
}
mysqli_free_result($dbr);
return $r;
}

?>

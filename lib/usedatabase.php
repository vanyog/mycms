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

// ���� ���� ������������ ������������ $db_link
// ����� �� �������� � mysql_query($q,$db_link);

if (!isset($ddir) || !file_exists($ddir."conf_database.php")) die("Database is not configured");
if (!isset($colation)) $colation = 'cp1251';

include_once($ddir."conf_database.php");

$db_link = get_db_link($user, $password, $database, $colation);
$db_req_count = 0;

function get_db_link($user, $password, $database, $colation = 'cp1251'){
$l = mysqli_connect("localhost",$user,$password,$database);
if (!$l){
 echo '<p>�� �� �������� ������ � MySQL �������!'; die;
}
mysqli_query($l,"SET NAMES '$colation';");
return $l;
}

// � ���, ������ �� ������� � ���� �������:
$temp_prefix = ''; // ����������, ����� ��������� ����������� �������

// �������, ����� ������ ��� ������� � ��������� �����������
function set_prefix($np){
global $tn_prefix, $temp_prefix;
$temp_prefix = $tn_prefix;
$tn_prefix = $np;
}

// �������, ����� ������������ ����������� �������
function restore_prefix(){
global $tn_prefix, $temp_prefix;
$tn_prefix = $temp_prefix;
}

?>

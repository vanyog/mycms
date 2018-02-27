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

include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_select_m.php');

// ��������� stored_value($n,$def=false) ���� ������ `value` �� ������� $tn_prifix.'options'
// ��� �� ���������� ����� �����, ����� ���������� $def.

function stored_value($n, $def = false){
global $option_value, $db_req_count; // �������� ����������, ����� ����� �� ���.
if (!isset($option_value[$n])) $option_value[$n] = db_table_field('value', 'options',"`name`='$n'", $def);
if(empty($option_value[$n]) && $def) $option_value[$n] = $def;
return $option_value[$n];
}

// ��������� store_value($n,$v) ������� ���������� $v � ����� � `name`=$n �� ������� $tn_prifix.'options'

function store_value($n,$v){
global $tn_prefix, $db_link;
// ������ �� ������ � ��� $n �� �������� ���� ��� �����
$r = db_select_1('*','options',"`name`='$n'");
if ($r) { $q = 'UPDATE'; $w = " WHERE `name`='$n';";} else { $q = 'INSERT INTO'; $w = ", `name`='$n';"; }
$q .= " `$tn_prefix"."options` SET `value`='$v'$w";
mysqli_query($db_link,$q);
}

// ���� � ���� SQL ������, ���� ����������� �� ������ ������ � �����, �������� � ������ $option_name 
// � �� ��������� �� ���������� �� ��������� ����� $option_value

function load_options($option_name){
global $option_value, $db_req_count;
$q = '';
foreach($option_name as $n){
if ($q) $q .= ' OR ';
if (!isset($option_value[$n])) $q .= "`name`='$n'";
}
if($q) $d = db_select_m('`name`,`value`','options',"$q");
else $d = array();
foreach($d as $r) $option_value[$r['name']]=$r['value'];
foreach($option_name as $n) if(!isset($option_value[$n])) $option_value[$n] = '';
$option_name = array();
}

?>

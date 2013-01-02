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

include_once('f_db_table_field.php');

// Функцията stored_value($n) чете полето `value` от таблица $tn_prifix.'options'

function stored_value($n){
  return db_table_field('value','options',"`name`='$n'");
}

// Функцията store_value($n,$v) записва стойността $v в запис с `name`=$n на таблица $tn_prifix.'options'
function store_value($n,$v){
global $tn_prefix, $db_link;
// четене на зеписа с име $n за проверка дали има такъв
$r = db_select_1('*','options',"`name`='$n'");
if ($r) { $q = 'UPDATE'; $w = " WHERE `name`='$n';";} else { $q = 'INSERT INTO'; $w = ", `name`='$n';"; }
$q .= " `$tn_prefix"."options` SET `value`='$v'$w";
mysql_query($q,$db_link);
}

?>

<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2014  Vanyo Georgiev <info@vanyog.com>

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

// Целта на този файл е да намали броя на заявките към базата данни за четене на настройки

include_once($idir."lib/f_db_select_m.php");

load_options();

function load_options(){
global $option_name, $option_value;
$q = '';
foreach($option_name as $n){
  if ($q) $q .= ' OR ';
  if (!isset($option_value[$n])) $q .= "`name`='$n'";
}
$d = db_select_m('`name`,`value`','options',"$q");
foreach($d as $r) $option_value[$r['name']]=$r['value'];
$option_name = array();
}

?>

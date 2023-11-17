<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// ƒобав€не на времето за работа от таблица `worktime2` в `worktime`.

$idir = dirname(__DIR__).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_query.php');
include_once($idir.'lib/f_view_table.php');

$da = db_select_m('*', 'worktime2', '1');//print_r($da); die;

$q1 = "INSERT INTO `$tn_prefix"."worktime` (`name`,`time`) VALUES ";
foreach($da as $d){
  $q = $q1.'(\''.$d['name'].'\', '.$d['time'].") ON DUPLICATE KEY UPDATE `time`=`time`+".$d['time'].";";
  mysqli_query($db_link, $q);
}

echo "All done.";


?>

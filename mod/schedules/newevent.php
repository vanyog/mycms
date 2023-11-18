<?php
/*
VanyoG CMS - a simple Content Management System
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

// Създаване на нов график

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_insert_1.php');

$mid = db_table_field('MAX(`ID`)', 'schedules', '1')+1;

if (!$_GET['schn']) die("Missing schn parameter.");

$s = $_GET['schn'];

$t1 = db_table_field('MAX(`date_time_1`)', 'schedules', "`sch_name`='$s'");
$t2 = db_table_field('MAX(`date_time_2`)', 'schedules', "`sch_name`='$s'");
if ($t2>$t1) $t1=$t2;
if (!$t1) $t1 = 'NOW()';


$d = array(
'sch_name'=>$s,
'ev_name'=>"schedule_event_$mid",
'date_time_1'=>$t1,
'date_time_2'=>$t1
);

db_insert_1($d, 'schedules');

$h = 'Location: '.$_SERVER['HTTP_REFERER'];
header($h);

?>

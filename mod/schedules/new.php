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

// Създаване на нов график

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_insert_1.php');

$mid = intval(db_table_field('MAX(`ID`)', 'schedules', '1'))+1;


$d = array(
'sch_name'=>"schedule_$mid",
'ev_name'=>"schedule_event_$mid",
'date_time_1'=>"NOW()",
'date_time_2'=>"NOW()"
);

$id = db_insert_1($d, 'schedules');
if(!$id) die("Database error");

$h = 'Location: '.$_SERVER['HTTP_REFERER'];
header($h);

?>

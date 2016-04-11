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

//
// Показва натрупаната в таблица content_hostory статистика за нарастване на общия обем на съдържанието на сайта.
//

error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'conf_paths.php');
include($idir.'lib/f_encode.php');
include($idir.'lib/f_chart.php');
include($idir.'lib/f_db_table_status.php');

$page_content = encode('<h1>Статистика на обема на съдържанието на сайта</h1>');

// Четене на данните от таблица content_history
$da = db_select_m('*', 'content_history', 1);

// Съставяне на масив дата - брой
$dt = array();
foreach($da as $d){
  $dt[$d['date']] = $d['size'];
}

// Добавяне и на днешния ден
$td = date("Y-m-d");
if (!isset($dt[$td])) $dt[$td] = db_table_status('content', 'Data_length');

// Четене на данните от таблица outer_links
$ld = db_select_m('LEFT(`date_time_1`, 10), COUNT(*)', 'outer_links', "`link`>'' GROUP BY LEFT(`date_time_1`, 10)");

// Съставяне на масив дата - брой
$lt = array();
$v = 0;
foreach($ld as $d){
  $k = $d['LEFT(`date_time_1`, 10)'];
  $v += $d['COUNT(*)'];
  $lt[$k] = $v;
}

$page_content .= chart($dt);

include($idir.'lib/build_page.php');

?>

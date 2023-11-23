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

// Този файл инициализира променливата $db_link
// която се използва в mysqli_ функциите

global $db_link, $db_req_count;

$db_link = false;
$db_req_count = 0;

if (!isset($colation)) $colation = 'cp1251';

if (!isset($ddir) || !file_exists($ddir."conf_database.php")) return;
//   die($ddir."conf_database.php - file not found.<p><pre>".print_r(debug_backtrace(),true)."</pre>");

include_once($ddir."conf_database.php");

if(!isset($host)) $host = 'localhost';

$db_link = get_db_link($host, $user, $password, $database, $colation);

function get_db_link($host, $user, $password, $database, $colation = 'cp1251'){
try { $l = mysqli_connect($host,$user,$password,$database); }
catch (Exception $e){
 if (!headers_sent()) header("Content-Type: text/html; charset=Windows-1251");
 echo "<p>Не се получава връзка с MySQL сървъра!</p>\n<pre>";
// debug_print_backtrace();
 die;
}
if($l===false) die('No link to database.');
mysqli_query($l,"SET NAMES '$colation';");
return $l;
}

// С цел, четене от таблици с друг префикс:
$temp_prefix = ''; // Променлива, която съхранява оригиналния префикс

// Функция, която задава нов префикс и съхранява оригиналния
function set_prefix($np){
global $tn_prefix, $temp_prefix;
$temp_prefix = $tn_prefix;
$tn_prefix = $np;
}

// Функция, която възстановява оригиналния префикс
function restore_prefix(){
global $tn_prefix, $temp_prefix;
$tn_prefix = $temp_prefix;
}

// Показване броя на извършените SQL заявки
function db_req_count(){
global $db_req_count, $exe_time;
static $lcount = 0;
$rc = $db_req_count - $lcount;
$rz = "$rc/$db_req_count";
if( $rc  > 1 ) $rz = '<span style="color:#FF0000;">'.$rz.'</span>';
if( $rc == 1 ) $rz = '<span style="color:#00FF00;">'.$rz.'</span>';
$lcount = $db_req_count;
static $ex_time = 0;
if($ex_time==0) $ex_time = $exe_time;
$ext = microtime(true);
$rz .= " ".number_format(($ext - $ex_time)*1000, 3)."/".number_format(($ext - $exe_time)*1000, 3);
$ex_time = $ext;
return $rz;
}

?>

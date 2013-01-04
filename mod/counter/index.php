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

// Скрипт - брояч на посещенията

// Масив с началната част на адреси, които се отброяват
$sites = array('http://ph');

include("../../conf_database.php");

if (!isset($_SERVER['HTTP_REFERER'])) die;

$rfr = $_SERVER['HTTP_REFERER'];

$url = '';

foreach($sites as $s){
  $n = strlen($s);
  if ($s==substr($rfr,0,$n)){ $url=$s; break; }
}

header("Content-Type: text/javascript; charset=windows-1251");

if (!$url) die;

$page = substr($rfr,$n,strlen($rfr)-$n);
$agent = $_SERVER['HTTP_USER_AGENT'];

$q = "INSERT INTO `$tn_prefix"."mod_counter` (`url`, `page`, `referrer`, `agent`, `date_time`) VALUES ('$url', '$page', '$agent', '$agent',NOW());";

echo 'document.write("'.$q.'");';



?>

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

include('../conf_database.php');

$p = 'tables.sql';

if (isset($_GET['m'])) $p = "../mod/".$_GET['m']."/$p";

if (!file_exists($p)) die("tables.sql file not found");

$fc = file_get_contents($p);

$fc = str_replace('CREATE TABLE IF NOT EXISTS `',   "CREATE TABLE IF NOT EXISTS `$tn_prefix",$fc);

$fc = str_replace('INSERT INTO `',   "INSERT INTO `$tn_prefix",$fc);

header("Content-Type: text/html; charset=utf-8");

echo $fc;

?>

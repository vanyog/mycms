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

$fc = file_get_contents('tables.sql');

$fc = str_replace('IF NOT EXISTS `content`',   "IF NOT EXISTS `$tn_prefix"."content`",$fc);
$fc = str_replace('IF NOT EXISTS `menu_items`',"IF NOT EXISTS `$tn_prefix"."menu_items`",$fc);
$fc = str_replace('IF NOT EXISTS `pages`',     "IF NOT EXISTS `$tn_prefix"."pages`",$fc);
$fc = str_replace('IF NOT EXISTS `scripts`',   "IF NOT EXISTS `$tn_prefix"."scripts`",$fc);
$fc = str_replace('IF NOT EXISTS `templates`', "IF NOT EXISTS `$tn_prefix"."templates`",$fc);
$fc = str_replace('IF NOT EXISTS `visit_history`', "IF NOT EXISTS `$tn_prefix"."visit_history`",$fc);

$fc = str_replace('INSERT INTO `content`',   "INSERT INTO `$tn_prefix"."content`",$fc);
$fc = str_replace('INSERT INTO `menu_items`',"INSERT INTO `$tn_prefix"."menu_items`",$fc);
$fc = str_replace('INSERT INTO `pages`',     "INSERT INTO `$tn_prefix"."pages`",$fc);
$fc = str_replace('INSERT INTO `scripts`',   "INSERT INTO `$tn_prefix"."scripts`",$fc);
$fc = str_replace('INSERT INTO `templates`', "INSERT INTO `$tn_prefix"."templates`",$fc);

header("Content-Type: text/html; charset=utf-8");
echo $fc;

?>

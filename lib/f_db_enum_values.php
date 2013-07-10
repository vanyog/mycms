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

// Функцията db_enum_values($fn, $tn) връща масив от валидните стойности
// на полето $fn от таблица $tn ако това поле и от тип enum

include_once($ddir.'conf_database.php');

function db_enum_values($fn, $tn){
global $tn_prefix, $db_link;
$q = "SHOW COLUMNS FROM `$tn_prefix$tn` LIKE '$fn'";
$r = mysql_query($q,$db_link);
if (!$r) return false;
$a = mysql_fetch_assoc($r);
if (!isset($a['Type'])) return false;
$rz = substr($a['Type'], 5, strlen($a['Type'])-6);
if (substr($a['Type'], 0, 5)!='enum(') return false;
return str_getcsv($rz, ',', "'");
}

?>

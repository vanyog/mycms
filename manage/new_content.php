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

// Вмъкване на съдържание в таблица content

$idir = dirname(dirname(__FILE__)).'/';

include($idir."lib/usedatabase.php");

$n = $_GET['n']; // Име на записа
$l = $_GET['l']; // Език на записа

$q = "INSERT INTO $tn_prefix"."content SET name='$n', language='$l', date_time_1=NOW(), date_time_2=NOW();";

mysql_query($q,$db_link);

$i = mysql_insert_id($db_link);

header('Location: '.$adm_pth.'edit_record.php?t=content&r='.$i);

?>

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

// Дублиране на един запис от таблица на базата данни

include('conf_manage.php');
include($idir.'lib/f_db_duplicate_record.php');

$t = $_GET['t'];  // Таблица
$id = 1*($_GET['r']); // `ID` на записа

$i = db_duplicate_record($t,$id);

if($i) $l = "Location: edit_record.php?t=$t&r=$i";
else $l = 'Location: show_table.php?t='.$t;

header($l);

?>
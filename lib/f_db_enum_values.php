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

global $ddir;

include_once($ddir.'conf_database.php');
include_once($ddir.'conf_database.php');

function db_enum_values($fn, $tn){
$ft = db_show_columns($tn, $fn, 'Type');
preg_match('/.*\((.*)\)/', $ft[0], $tp);
return str_getcsv($tp[1], ',', "'");
}

?>

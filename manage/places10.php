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

// В някои от таблиците има поле place, което служи за задаване реда на извличане на записите.
// Това поле съдържа целочислени стойности през 10.
// При необходимост от промяна на реда на извличане се променя полето place на записа, 
// който трябва да се премести. Например, за да се 
// премести между редове с place=20 и place=30, се задава place=25.

// Настоящия скрипт променя отново стойностите на поле place през 10
// и трябва да се използва след като са извършвани премествания.

include("conf_manage.php"); 
include_once($idir."conf_paths.php");
include_once($idir."lib/f_db_select_m.php");
include_once($idir."lib/f_db_places10.php");

$t = $_GET['t']; // Име на таблицата

db_places10($t);

// Връщане на страницата, извикала скрипта
header('Location: '.$_SERVER['HTTP_REFERER']);

?>

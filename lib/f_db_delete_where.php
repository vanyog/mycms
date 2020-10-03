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

// Изтрива всички записи от таблица $t, които отговарят на условието $w.
// Ако $y = true, само се показва SQL заявката без да се трие нищо.
// Функцията връща броя на изтритите редове

function db_delete_where($t,$w,$y=false){
global $tn_prefix, $db_link, $db_req_count;
$q = "DELETE FROM `$tn_prefix"."$t` WHERE $w;";
if ($y) { echo "$q<br>\n"; return; }
mysqli_query($db_link, $q);
$db_req_count++;
$i = mysqli_affected_rows($db_link);
return $i;
}

?>
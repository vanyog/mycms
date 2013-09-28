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

include_once($idir."lib/usedatabase.php");

function db_table_exists($t){
global $database, $db_link, $tn_prefix;
$r = mysqli_query($db_link,"SHOW TABLES FROM $database;");
$ls = array();
while ($l = mysqli_fetch_row($r)) $ls[] = $l[0];
//echo $tn_prefix.$t.'<p>'; print_r($ls); die;
return in_array($tn_prefix.$t,$ls);
}

?>

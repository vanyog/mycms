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

function db_tables(){
global $db_link, $db_req_count;
$q = "SHOW TABLES;";
$rs = mysqli_query($db_link,$q);
$db_req_count++;
$rz = array();
while ($a = mysqli_fetch_array($rs)){
 $rz[] = $a[0];
}
return $rz;
}

?>

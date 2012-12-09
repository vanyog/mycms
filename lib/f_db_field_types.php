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

include_once("usedatabase.php");

function db_field_types($t){
global $db_link,$tn_prefix;
$q = "SELECT * FROM $tn_prefix$t LIMIT 1,1;";
$r = mysql_query($q,$db_link);
$rz = array();
if ($r){
  $n = mysql_num_fields($r);
  for($i=0; $i<$n; $i++){
    $rz[] = mysql_field_type($r,$i);
  }
}
return $rz;
}

?>

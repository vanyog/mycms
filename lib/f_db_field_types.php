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

include_once($idir."lib/usedatabase.php");

function db_field_types($t){
global $db_link,$tn_prefix, $db_req_count;
$q = "SELECT * FROM $tn_prefix$t LIMIT 1,1;";
$r = mysqli_query($db_link,$q);
$db_req_count++;
$rz = array();
if ($r){
  $ft = mysqli_fetch_fields($r);
  foreach($ft as $t){
    $rz[] = $t->type;
  }
}
return $rz;
}

?>

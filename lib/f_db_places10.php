<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2020  Vanyo Georgiev <info@vanyog.com>

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

function db_places10($t){
global $tn_prefix, $db_link;
// Задаване на стойности през 1
$i = 1;
$r = db_select_m('ID', $t, '1 ORDER BY `place` ASC');
foreach($r as $r1){
  $q = "UPDATE `$tn_prefix$t` SET `place`=$i WHERE ID=".$r1['ID'].";";
  mysqli_query($db_link,$q);
  $i++;
}

// Умножаване на стойностите по 10
$q = "UPDATE `$tn_prefix$t` SET `place` = `place` * 10;";
$q = mysqli_query($db_link,$q);
}

?>
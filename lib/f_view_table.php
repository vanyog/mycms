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

// Показва данните от масива във вид на таблица
// Елементите на масива трябва да са асоциирани масиви

function view_table($da,$id=''){
if ($id) $id = " id=\"$id\"";
$rz = "<table$id>";
foreach($da as $i=>$d){
  if ($i==0){ // Антетка на таблицата
    $rz .= '<tr>';
    foreach($d as $k=>$l) $rz .= "<th>$k</th>";
    $rz .= "</tr>\n";
  }
  $rz .= '<tr>';
  foreach($d as $k=>$l) $rz .= "<td>".stripslashes($l)."</td>";
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}
 
?>

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

// Връща html код представящ данните от масива $da във вид на таблица
// Елементите на масива трябва да са асоциирани масиви с еднакви ключове
// Вторият параметър е стойността на id атрибут на <table> тага.
// Третият параметър е асоциативен масив с ключове - имена на полета
// и стойности - надписи, които да се сложат в антетката на таблицата.

function view_table($da,$id='',$n=''){
if ((strlen($id)>0)&&($id[0]!=' '))  $id = " id=\"$id\"";

if ( !is_array($n)){
  if (count($da)) {
    $n = array_keys($da[0]);
    $n = array_combine($n,$n);
  }
  else $n = array();
}

$rz = "<table$id>
<tr>";
// Първи ред - антетка на таблицата
foreach($n as $k=>$v){
  $rz .= "<th>$v</th>";
}
$rz .= "</tr>\n";
foreach($da as $i=>$d){
  $rz .= '<tr>';
  foreach($n as $k=>$v){
    if (!isset($d[$k])) $vl = '';
    else if ($k=='email') $vl = '<a href="mailto:'.$d[$k].'">'.$d[$k].'</a>';
         else $vl = stripslashes($d[$k]);
    $rz .= "<td>$vl</td>";
  }
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}
 
?>

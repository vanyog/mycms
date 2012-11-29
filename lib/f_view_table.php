<?php
// Copyright: Vanyo Georgiev info@vanyog.com

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

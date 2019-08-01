<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2014  Vanyo Georgiev <info@vanyog.com>

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

// Показване съдържанието на запис от база данни във вид на таблица
// $d - асоциативен масив с прочетените данни
// $n - асоциативен масив с надписи за полетата. Ако липсва се показват имената на полетата.

// $st - CSS дефиниции за форматиране на таблицата

function view_record($d, $n = '', $st = ''){
if ($n==''){
  $n = array_keys($d);
  $n = array_combine($n,$n);
}
if($st) $st = " style=\"$st\"";
$rz = '<table class="record_table"'."$st>\n";
foreach($n as $k=>$v){
  $vl = '';
  if (isset($d[$k])) $vl = stripslashes($d[$k]);
  $rz .= '<tr><th>'.$v.'</th><td>'.$vl.'</td></tr>'."\n";
}
$rz .= "</table>\n";
return $rz;
}

?>

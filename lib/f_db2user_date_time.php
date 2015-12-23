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

// Функцията db2user_date_time($dts) преформатира сринг, съдържащ дата-час,
// извлечени от MySQL база данни във формат: dd mmmm yyyy hh:mm

// За да се изпълнява без грешки, тази функция изисква в таблица $tn_prefix.'content'
// да има запис с име 'month_names', съдържащ дефиниция на масив $month с имената на 
// месеците на всеки от езиците, използване на сайта.

// Ако вторият параметър $tm = false не се показва час.
// иначе час не се показва, само ако е 00:00:00.

function db2user_date_time($dts, $tm = true){
$c = translate('month_names',false);// print_r($c); die;
eval($c);
if ((substr($dts,11,8)=="00:00:00")||!$tm) $t = '';
else {
  $h = (1*substr($dts,11,2));
  if ($h<10) $t = ' &nbsp;'; else $t = ' ';
  $t .= $h.substr($dts,13,3);
  if (1*substr($dts,17,2)) $t .= substr($dts,16,3);
}
$d = 1*substr($dts,8,2);
if ($d<10) $d = '&nbsp;'.$d;
return $t.' '.
  $d.' '.
  $month[1*substr($dts,5,2)].' '.
  substr($dts,0,4);
}

?>

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

function db2user_date_time($dts){
$c = translate('month_names'); //print_r($c); die;
eval($c);
return substr($dts,8,2).' '.
  $month[1*substr($dts,5,2)].' '.
  substr($dts,0,4);
}

?>

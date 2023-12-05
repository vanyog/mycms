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

// Функцията db_select_join_m($fn,$ta,$tb,$on,$whr,$y = false), дефинирана в този файл
// чете полетата $fn, които са както от запис от таблица $ta, така и от запис на таблица $tb
// Полетата от първата таблица се означават с a.имеНаПоле, а от втората с - b.имеНаПоле
// $on е условието за присъединяване на полета от втората таблица
// Прочетеният запис удовлетворява условието $whr.
// Функцията връща в масив от асоциативни масиви с прочетените записи, или false при неуспех.
// Ключовете на масивите са имената на полетата,
// а стойностите - съдържанието на полетата от таблиците.

include_once($idir."lib/usedatabase.php");

function db_select_join_m($fn,$ta,$tb,$on,$whr,$y = false){
global $db_link, $tn_prefix, $db_req_count;
$ta = "`$tn_prefix$ta`";
$tb = "`$tn_prefix$tb`";
$q="SELECT $fn FROM $ta a LEFT JOIN $tb b ON $on WHERE $whr;"; 
if ($y) echo "$q<br>\n";
$dbr=mysqli_query($db_link,$q);
$db_req_count++;
$r=array();
if (!$dbr) return $r; 
while ( $rc=mysqli_fetch_assoc($dbr) ){
 $r[]=$rc;
}
mysqli_free_result($dbr);
return $r;
}

?>
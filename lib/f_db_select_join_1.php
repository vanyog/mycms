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

// Функцията db_select_join_1($fn,$ta,$tb,$on,$whr,$y = false), дефинирана в този файл
// чете полетата $fn, които са както от запис от таблица $ta, така и от запис на таблица $tb
// Полетата от първата таблица се означават с a.имеНаПоле, а от втората с - b.имеНаПоле
// $n е условието за присъединяване на полета от втората таблица
// Прочетеният запис удовлетворява условието $whr.
// Функцията връща в асоциативен масив с прочетения запис, или false при неуспех.
// Ключовете на масива са имената на полетата,
// а стойностите - съдържанието на полетата от таблиците.

include_once($idir."lib/usedatabase.php");

function db_select_join_1($fn,$ta,$tb,$on,$whr,$y = false){
global $db_link, $tn_prefix, $db_req_count;
$ta = "`$tn_prefix$ta`";
$tb = "`$tn_prefix$tb`";
$q="SELECT $fn FROM $ta a LEFT JOIN $tb b ON $on WHERE $whr LIMIT 1;";
if ($y) echo "$q<br>\n";
$r=mysqli_query($db_link,$q);
$db_req_count++;
if ($r===false) return false;
$rc=mysqli_fetch_assoc($r);
mysqli_free_result($r);
return $rc;
}

?>
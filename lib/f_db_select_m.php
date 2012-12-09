<script language="php">

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

// Функцията db_select_m, дефинирана в този файл
// чете полетата $fn на всички записи от таблица $tb,
// удовлетворяващи условието $whr.
// Таблицата е от базата данни, определена от променливата $db_link
// (виж usedatabase.php).
// Функцията връща масив от асоциирани масиви,
// съответстващи на всеки от прочетените записи.
// Ключовете на масива за даден запис са имената на полетата,
// а стойностите - съдържанието на полетата от таблицата.

include_once("usedatabase.php");

function db_select_m($fn,$tb,$whr){
global $db_link, $tn_prefix;
$q="SELECT $fn FROM `$tn_prefix$tb` WHERE $whr;"; //echo "$q<br>";
$dbr=mysql_query($q,$db_link);
$r=array();
if (!$dbr) return $r; 
while ( $rc=mysql_fetch_assoc($dbr) ){
 $r[]=$rc; //print_r($rc);
}
mysql_free_result($dbr);
return $r;
}

</script>

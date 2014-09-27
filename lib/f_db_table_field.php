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

// Функцията db_table_field($fn,$tb,$whr), връща съдържанието на полетато $fn
// на първия, отговарящ на условието $whr запис от таблица $tb.

// Таблицата е от базата данни, определена от променливата $db_link,
// дефинирана в usedatabase.php.

include_once($idir."lib/usedatabase.php");

function db_table_field($fn, $tb, $whr, $def = '', $y = false){
global $db_link,$tn_prefix, $db_req_count;
$q="SELECT $fn FROM $tn_prefix$tb WHERE $whr;";
if ($y===true) echo $q.'<br>'; 
$r=mysqli_query($db_link,$q);
$db_req_count++;
if (!$r){
  return $def;
}
if ($fn[0]=='`') $fn = substr($fn,1,strlen($fn)-2);
$rc=mysqli_fetch_assoc($r);
if (isset($rc[$fn])) return stripslashes($rc[$fn]);
else return $def;
}

</script>

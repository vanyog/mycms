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

// Функцията db_select_1, дефинирана в този файл
// чете полетата $fn на един запис от таблица $tb,
// на базата данни, определена от променливата $db_link
// (виж usedatabase.php).
// Прочетеният запис удовлетворява условието $whr.
// Функцията връща, асоцииран масив, или false при неуспех.
// Ключовете на масива са имената на полетата,
// а стойностите - съдържанието на полетата от таблицата.

include_once($idir."lib/usedatabase.php");

function db_select_1($fn,$tb,$whr,$y = false){
global $db_link, $tn_prefix, $db_req_count;
if($db_link===false){ echo "<pre>"; debug_print_backtrace(); die('No link to database.'); }
$t = "$tn_prefix$tb";
if($t[0]!='`') $t = "`$t`";
$q="SELECT $fn FROM $t WHERE $whr LIMIT 1;";
if ($y) echo "$q<br>\n";
$r = false;
try { $r = mysqli_query($db_link,$q); }
catch(Exception $e){ die($e->getMessage()."<br>".$q); }
$db_req_count++;
if (($r===false) || ($r->num_rows==0)) return false;
$rc=mysqli_fetch_assoc($r);
mysqli_free_result($r);
return $rc;
}

?>

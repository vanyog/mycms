<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

include_once($idir.'lib/usedatabase.php');

// Универсална функция за изпълняване на SQL заявки.
// $q е SQL заявката.
// $y - незадължителен параметър, който ако е true предизвиква показване на заявката.
// Връщаният резултат е масив от асоциативни масиви.
// Ключовете на масивите са имената на полетата, а стойностите са стойностите на полетата.

function db_query($q, $y = false){
if ($y) echo "$q<br>";
global $db_link, $db_req_count;
$r = mysqli_query($db_link, $q);
$db_req_count++;
$a = array();
if (!is_bool($r) && $r)
   while($a[] = mysqli_fetch_assoc($r));
return $a;
}

?>

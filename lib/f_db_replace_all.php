<?php
/*
VanyoG CMS - a simple Content Management System
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

// Замества всички срещания на подстринг $s1 със стринг $s2 в поле $f на всички записи от таблица $t.
// Ако е изпратен параметър $y = true функцията само разпечатава sql заявки, без да ги изпълнява.
// Връща масив от две стойности - броя на заместванията, броя на записите, в които са направени замествания.

if (!isset($idir)) $idir = dirname(dirname(__FILE__)).'/';
if (!isset($ddir)) $ddir = $idir;

include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_update_record.php');

function db_replace_all($s1, $s2, $f, $t, $y = false){
$da = db_select_m("ID,$f", $t, "`$f` LIKE '%$s1%'");
$rz = 0;
foreach($da as $d){
  $c = 0;
  $d[$f] = str_replace($s1, $s2, stripslashes($d[$f]), $c );
  $rz = 1*$rz + 1*$c;
  $r = db_update_record($d,$t,$y);
  if ($y) echo "$r<br>\n";
}
return array($rz, count($da));
}

?>

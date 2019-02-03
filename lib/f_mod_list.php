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

include_once($idir.'lib/f_file_list.php');

//
// Връща масив с имената на модулите

function mod_list($a = true){
global $mod_apth, $apth;
$a1 = dir_list($mod_apth);
if ($a) foreach($a1 as $i=>$n) $a1[$i] = $mod_apth.$n.'/';
$a2 = dir_list($apth.'mod');
if ($a) foreach($a2 as $i=>$n) $a2[$i] = $apth.'mod/'.$n.'/';
$rz = array_merge($a1,$a2);
return $rz;
}

?>

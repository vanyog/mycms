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

// Съставя адрес към текущо изпалнявания php скрипт $ind_fl
// с параметри, към които се добая и параметър с име $n и стойност $v.
// Ако съставеният адрес ще се използва за пренасочване, а не като htef атрибут,
// трябва да се подаде и трети параметър $a = false, за да не се замества & с &amp;.

function set_self_query_var($n, $v, $a = true){
global $ind_fl, $adm_name, $edit_name;
$r = $_GET;
$r[$n] = $v;
unset($r[$adm_name]);
unset($r[$edit_name]);
ksort($r);
$rz = http_build_query($r);
if ($a) $rz = str_replace('&','&amp;',$rz);
return $ind_fl.'?'.$rz;
}

?>

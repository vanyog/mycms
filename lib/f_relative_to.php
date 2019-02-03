<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2018  Vanyo Georgiev <info@vanyog.com>

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

// Връща относителния път до файл/директория, с абсолютен път $d
// спрямо абсолютна директория $apth

function relative_to($apth,$d){
$r = explode('/', $apth);
$p = explode('/', $d);
//echo("$apth<br>$d<br>\$r = ".print_r($r,true)."<br>\$p = ".print_r($p,true)."<br>");//die();
$start = 0;
$rz =  array();
foreach($r as $i => $n){
   if(!$start && (!isset($p[$i]) || ($n!=$p[$i])) ) $start = $i;
   if($start && !empty($n)){ 
      $rz[] = '..';
//      $rz[] = $n;
   }
}
if(!$start) $start = count($r) - 1;
//echo "$start<br>".print_r($rz,true)."<br>";
$rt = implode('/', $rz);
if($rt) $rt .= '/';
//echo "$rt<br>";
$s = array_slice($p, $start, -1);
if(count($s)) $rt .= implode('/',$s);
if(count($p)>=count($r)) $rt .= '/';
//echo (print_r($s,true)."<br>$rt<p>\n");
return $rt;
}

function identic_letter($a, $b){
$la = strlen($a);
$lb = strlen($b);
$ml = $la;
if($ml>$lb) $ml=$lb;
for($i=0; $i<$ml; $i++) if($a[$i]!=$b[$i]) return $i;
return $i;
}

?>
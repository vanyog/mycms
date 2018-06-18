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

//
// Проверява дали модул с име $f, изписано с малки букви, се намира в директория $mod_pth или в директория mod
// Връща абсолютния път до директорията на модула или '', ако не е намерен модул.
//
function mod_path($f){
global $mod_pth,$pth;
$fn = "$mod_pth$f/f_$f.php";
$afn = $_SERVER['DOCUMENT_ROOT']."$fn";
if ( ($mod_pth!='/mod/') && !file_exists($afn) ){
  $fn = $pth."mod/$f/f_$f.php"; 
  $afn = $_SERVER['DOCUMENT_ROOT']."$fn";
}
if (file_exists($afn)) return $afn;
else return '';
}

?>

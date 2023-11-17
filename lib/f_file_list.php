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

//
// Връща масив с имената на файловете от абсолютна директория $d.
// Ако има втори параметър true масивът е подреден по азбучен ред.
//

function file_list($d,$s = false){
$dl = array();
if (!file_exists($d)) return $dl;
$dr = opendir($d);
while ($a = readdir($dr))
  if ( ($a!='.') && ($a!='..') && !is_dir("$d/$a") ) $dl[] = $a;
if ($s) sort($dl);
return $dl;
}

//
// Връща масив с имената на директориите от абсолютна директория $d.
// Ако има втори параметър true масивът е подреден по азбучен ред.
//

function dir_list($d,$s = false){//print_r($d); die;
$dl = array();
if (!file_exists($d)) return $dl;
$dr = opendir($d);
while ($a = readdir($dr))
  if ( ($a!='.') && ($a!='..') && is_dir("$d/$a") ) $dl[] = $a;
if ($s) sort($dl);
return $dl;
}

?>

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

// Показва стойността на глобална PHP променлива с име $a
// Може да се покаже и стойността на елемент от масив: $_GET, $_SERVER
// Например, при $a = 'SERVER['REQUEST_URI']' се показва стойността на: $_SERVER['REQUEST_URI']
// Ако променливата не е дефинирана се връща празна стойност или
// стойността, изпратена в параметъра $a, отделена от името на променливата със знак '|'.

function variable($a){
$aa = explode('|',$a);
$r = array();
// Случай на елемент от масив
$j = preg_match_all('/(.*)\[\'(.*)\'\]/', $aa[0], $r);
if($j) switch($r[1][0]){
case 'GET':    return $_GET[$r[2][0]];    break;
case 'SERVER': return $_SERVER[$r[2][0]]; break;
}
//if($a=='page_header') die($GLOBALS[$a]);
if(!isset($aa[1])) $aa[1] = '';
return isset($GLOBALS[$aa[0]]) ? stripslashes($GLOBALS[$aa[0]]) : $aa[1];
}

?>

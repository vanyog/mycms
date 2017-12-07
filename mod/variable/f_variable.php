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

function variable($a){
$r = array();
$j = preg_match_all('/(.*)\[\'(.*)\'\]/', $a, $r);
if($j) switch($r[1][0]){
case 'GET': return $_GET[$r[2][0]]; break;
}
global $$a;
return stripslashes($$a);
}

?>

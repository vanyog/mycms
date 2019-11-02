<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2017  Vanyo Georgiev <info@vanyog.com>

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

function setvariable($a){
if(!$a) die('function setvariable: missing parameter');
$b = explode('|', $a);
if(count($b)<2) die('Error in module SETVARIABLE: Incorrect parameter.');
$GLOBALS[$b[0]] = $b[1];
return '';
}
?>

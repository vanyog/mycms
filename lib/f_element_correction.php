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

// Функция, която коригира --$$_ _$$-- елементите

function element_correction($v1){
 $v1 = str_replace( chr(60).' !--$$_',    chr(60).'!--$$_', $v1);
 $v1 = str_replace( '<!--$$_',            chr(60).'!--$$_', $v1);
 $v1 = str_replace( '_$$-->',             '_$$--'.chr(62),  $v1);
 $v1 = str_replace( chr(38).'lt;!--$$_',  chr(60).'!--$$_', $v1);
 $v1 = str_replace( chr(38).'lt; !--$$_', chr(60).'!--$$_', $v1);
 $v1 = str_replace( '_$$--'.chr(38).'gt;','_$$--'.chr(62),  $v1);
 return $v1;
}

?>

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

global $idir;

include_once($idir.'lib/f_set_self_query_var.php');

//
// Връща меню с препратки към страници за извършване на действия с потребители
//
function user_menu_items(){
$q1 = set_self_query_var('user','newreg');
$q2 = set_self_query_var('user','delete');
return '<a href="'.$q1.'">User new</a><br>
<a href="'.$q2.'">User delete</a>'."\n";
}

?>

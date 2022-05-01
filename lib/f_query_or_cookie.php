<?php

/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

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

// Тази функция проверява дали с GET e изпратена променлива $k със стойност $v
// или има бисквитка с име $k и стойност $v.
// Ако това е така връща истина.

// Ако има стойност $_GET[$k] се установява бисквитка с тази стойност.

function query_or_cookie($k,$v){
if (isset($_GET[$k])){
   setcookie($k, $_GET[$k], 
             ['expires'=>time()+60*60*24*30, 'path'=>'/', 'SameSite'=>'Strict'] );
   return ($_GET[$k]==$v);
}
else if (isset($_COOKIE[$k])) return ($_COOKIE[$k]==$v);
        else return '';
}

?>

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

// Показване на хипервръзка от модул OUTERLINKS
// Параметърът $a е номерът на хипервръзката от таблица outer_links

// Параметърът може да съдържа и текст, отделен от номера с |, който да замени надписа на линка

function outerlink($a){
global $main_index;
$aa = explode('|',$a);
// Четене данните за хипервръзката
$d = db_select_1('*', 'outer_links', "`ID`=".$aa[0] );
if (!isset($aa[1])) $aa[1] = $d['Title'];
return '<a href="'.$main_index.'?lid='.$aa[0].'&pid=6" target="_blank">'.$aa[1].'</a>';
}

?>
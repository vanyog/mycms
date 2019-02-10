<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Функция, която от съдържанието на страницата прави извадка - описание за споделяне в социални мрежи

function ogdescription(){
global $page_content, $og_description;
if($og_description) return $og_description;
$a = strip_tags($page_content);
$a = str_replace('&nbsp;',' ',$a);
$a = str_replace("\n",' ',$a);
$a = str_replace("\r",'',$a);
$a = trim($a);
$l = 300;
$fl = strlen($a)-1;
while ( ($l<$fl) && !in_array($a[$l], array(' ', ',', '.', ':', '-', '&') ) ) $l++;
$rz = substr($a,0,$l);
if (strlen($a)>strlen($rz)) $rz .= '...';
return $rz;
}

?>
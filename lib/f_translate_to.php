<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2016 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Превеждане на стринг с име $n към език $l,
// независимо дали на сайта е предвиден този език и какъв е текущия език.
// Третият параметър $y определя дали в режим на редактиране да се показва линк за редактиране.

//include_once("f_translate.php");

function translate_to($n, $l, $y = true){
global $language;
$l1 = $language;
$language = $l;
$rz = translate($n, $y);
$language = $l1;
return $rz;
}


?>

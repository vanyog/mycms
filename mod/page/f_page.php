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

function page($a){
global $main_index;
$rz = '';
$pd = db_select_1('*', 'pages', 'ID='.$a);
$rz .= '<a href="'.$main_index."?pid=$a\">".translate($pd['title']).'</a>';
return $rz;
}

?>
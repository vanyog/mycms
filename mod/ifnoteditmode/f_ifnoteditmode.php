<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// Показва съдържание, ако сайтът не е в режим на редактиране.
// Ако параметърът $a започва с 'content_' е име на запис от таблица `content`.
// ако не започва с 'content_' е съдържанието, което се показва.

function ifnoteditmode($a){
$rz = '';
if(!in_edit_mode() && !is_local()){
   if(substr($a,0,8)=='content_') $rz = translate($a);
   else $rz = stripslashes($a);
}
return $rz;
}

?>
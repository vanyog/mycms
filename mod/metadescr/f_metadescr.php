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

// Показва meta таг description.
// Описанието трябва да е присвоено на глобална променлива $page_description 
// Ако не е дефинирана променлива $page_description, заглавието се използва вместо описание.

function metadescr(){
global $page_description, $page_title;
if(!$page_description) $page_description = strip_tags($page_title);
return '<meta name="description" content="'.stripslashes($page_description)."\">";
}

?>
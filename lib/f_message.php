<?php
/* 
VanyoG CMS - a simple Content Management System
Copyright (C) 2021 Vanyo Georgiev <info@vanyog.com>

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

// Функцията връща '<p class="message">'.$tx.'</p>', но преди това 
// зарежда запазеният в таблица 'options' под име 'css_p.message' стил за съобщение

function message($tx){
add_style('p.message');
return '<p class="message">'.$tx."</p>\n";
}

?>

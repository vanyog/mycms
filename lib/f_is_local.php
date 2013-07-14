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

include_once($idir."lib/f_query_or_cookie.php");
include_once($idir."lib/f_stored_value.php");

// Променливи и фуркции, които дават информация за хостинга и администрирането

$web_host = stored_value('host_web','mysite.com'); // Домейн на сайта.

$local_host = stored_value('host_local','localhost'); // Локален домейн на сайта, който не е достъпен през Интернет.
                      // Използва се, когато се създава пробно локално копие на сайта.

$phpmyadmin_site = stored_value('phpmyadmin_web','http://mysite.com/phpmyadmin');   // Адрес на phpMyAdmin за отдалечения сървър
$phpmyadmin_local = stored_value('phpmyadmin_local','http://localhost/phpmyadmin'); // Адрес на phpMyAdmin за локалния сървър

// Връща истина, ако сайтът се намира на локален сървър.
function is_local(){
global $local_host;
return $local_host==$_SERVER['HTTP_HOST'];
}

// Функцията in_edit_mode() връща истина ако сайтът е в режим на редактиране
// В такъв режим се показват линкове за редактиране на надписи, текстове, менюта и др.
// Сайтът е в режим на редактиране ако:
// - получи бисквитка im=admin
// - получи $_GET['im']=='admin'

function in_edit_mode(){
global $edit_name, $edit_value; // echo "$edit_name, $edit_value"; die;
return query_or_cookie($edit_name,$edit_value);
}



?>

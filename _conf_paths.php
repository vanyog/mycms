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

// Скрипт за указване на пътя до някои директории

// Директория на входната страница.
// Задава се ако сайтът се изгражда като раздел в друг сайт и се намира в директория на главния сайт.
// Трябва да завършва с /.
$pth = '/';

// Абсолютна директория на входната страница във файловата система на сървъра
$apth = $_SERVER['DOCUMENT_ROOT'].$pth;

// Директория за администриране
$adm_pth = '/manage/';

// Абсолютна директория на директорията за администриране във файловата система на сървъра
$adm_apth = $_SERVER['DOCUMENT_ROOT'].$adm_pth;

// Адрес на phpMyAdmin за отдалечения сървър 
$phpmyadmin = $adm_pth.'db/index.php';

// Път до ckeditor
$ckpth = '/ckeditor/';

// Тайни стойности, на които се базира сигурността на административния достъп до сайта:
$adm_name = 'admin'; // Име на променлива, която се изпраща с GET за да се покаже менюто за администриране 
$adm_value = 'on'; // Стойност на променлива, която се изпраща с GET за да се покаже менюто за администриране 

$edit_name = 'edit';  // Име на променлива, която се изпраща с GET за да се мине в режим на редактиране
$edit_value = 'on';  // Стойност на променлива, която се изпраща с GET за да се мине в режим на редактиране

// Връща истина, ако се изпълнява скрипт от директорията за администриране
function in_admin_path(){
global $adm_pth;
return ( substr($_SERVER['PHP_SELF'],0,strlen($adm_pth))==$adm_pth );
}

?>

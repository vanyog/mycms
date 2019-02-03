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

include_once($idir."lib/f_query_or_cookie.php");

function in_edit_mode(){
global $edit_name, $edit_value; // echo "$edit_name, $edit_value"; die;
if (isset($_COOKIE['PHPSESSID']) || show_adm_links()) return query_or_cookie($edit_name,$edit_value);
return false;
}

function show_adm_links(){
global $adm_pth,$adm_name,$adm_value;
// Не се показват ако има бисквитка noadm = yes
//print_r($_COOKIE); die;
if (isset($_COOKIE['noadm']) && ($_COOKIE['noadm']=='yes')) return false;
// Истина ако се зарежда страница от директорията за администриране
$a = substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth;
// Линкове за администриране се генерират в случай, че:
// - сайтът е на локален сървър
// - сайтът е в режим на редактиране
// - показва се страница от директорията за администриране
// - получена е стойност $_GET[$adm_name] = $adm_value
// - има бисквитка с име $adm_name и стойност $adm_value
return is_local() /*|| in_edit_mode()*/ || $a || query_or_cookie($adm_name,$adm_value);
}

?>
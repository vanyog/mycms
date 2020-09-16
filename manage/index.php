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

error_reporting(E_ALL); ini_set('display_errors',1);

$page_head = '';
$page_content = '';

include_once("conf_manage.php");
include($idir.'conf_paths.php');
include($idir.'lib/f_page_cache.php');

$_COOKIE['noadm']='no';
setcookie($adm_name, $adm_value, time()+60*60*24*30,'/');
setcookie('noadm', 'no', time()+60*60*24*30,'/');
purge_page_cache($pth);

$page_content = '<p>&nbsp;</p>
<p><a href="'.$adm_pth.'/places10.php?t=menu_items">Menu items renum</a></p>';

include("build_page.php");
?>
<?php

/*
VanyoG CMS - a simple Content Management System
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
$page_content = "<h1>Administration</h1>\n";
$exe_time = microtime(true);

include_once("conf_manage.php");
include($idir.'conf_paths.php');
include($idir.'lib/f_page_cache.php');
include_once($idir.'lib/f_mysetcookie.php');

$_COOKIE['noadm']='no';
mysetcookie($adm_name, $adm_value);
mysetcookie('noadm', 'no');
purge_page_cache($pth);

if(basename(dirname($_SERVER['PHP_SELF']))=='manage') 
  $page_content .= '<p>This "manage" directory is not renamed and secured. '.
  'Run <a href="_secure.php">_secure.php</a> to secure the site.</p>';

$page_content .= '<p>&nbsp;</p>
<p><a href="'.$adm_pth.'/places10.php?t=menu_items">Menu items renum</a></p>';

include("build_page.php");
?>
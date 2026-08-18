<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2015  Vanyo Georgiev <info@vanyog.com>

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

// Показване на информацията от README.txt файловете.
// Без параметри се показва README.txt файла на системата.
// С параметър $_GET['m']=xx се README.txt файла на модул xx.

error_reporting(E_ALL); ini_set('display_errors',1);

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

$fln1 = 'README-bg.txt';
$fln = $apth.'README-bg.txt';

if (isset($_GET['m'])) { 
   $fln1 = $mod_pth.strtolower($_GET['m']).'/README.txt';
   $fln = $_SERVER['DOCUMENT_ROOT'].$fln1;
   $fln1 = substr($fln1,1);
}
if (!file_exists($fln)) {
    $fln1 = 'mod/'.strtolower($_GET['m']).'/README.txt';
    $fln = $apth.$fln1;
}

if (!file_exists($fln)) $cnt = "File not found<br>$fln";
else $cnt = nl2br( htmlspecialchars( file_get_contents($fln), ENT_COMPAT, 'cp1251' ) );

$page_content = '<div style="width:800px; margin:0 auto; font-family:monospace;">'.
                iconv('windows-1251', $site_encoding, $cnt).'</div>';

if(in_edit_mode()) $page_content .= '<p><a href="'.$adm_pth.'edit_file.php?f='.$fln1.'">Edit</a> </p>';

include($idir.'lib/build_page.php');

?>
<?php
/*
MyCMS - a simple Content Management System
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

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

$fln = $apth.'README-bg.txt';

if (isset($_GET['m'])) $fln = $_SERVER['DOCUMENT_ROOT'].$mod_pth.strtolower($_GET['m']).'/README.txt';
if (!file_exists($fln)) $fln = $apth.'mod/'.$_GET['m'].'/README.txt';

if (!file_exists($fln)) $cnt = "File not found<br>$fln";
else $cnt = nl2br( htmlspecialchars( file_get_contents($fln), ENT_COMPAT, 'cp1251' ) );

$page_content = '<div style="width:800px; margin:0 auto; font-family:monospace;">'.iconv('windows-1251', $site_encoding, $cnt).'</div>';

include($idir.'lib/build_page.php');

?>

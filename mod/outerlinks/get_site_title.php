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

// „етене на заглавието на интернет страница с адрес $_GET['url']

if(!isset($_GET['url'])) die('');

if(file_exists($_GET['url'])) die('');

$er = error_reporting(1);
$tx = file_get_contents($_GET['url'], FILE_BINARY, NULL, 0, 4096);
error_reporting($er);
if($tx===false) die("Error reading ".$_GET['url']);

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;
include_once($idir.'conf_paths.php');

header("Content-Type: text/html; charset=$site_encoding");

$e = get_between('charset="', '"', $tx);

$t = get_between('<title>', '</title>', $tx);

$t = iconv($e, "$site_encoding//IGNORE", $t);

echo $t;

function get_between($b1, $b2, $tx){
$p1 = stripos($tx, $b1, 1);
if($p1===false) return '';
$p1 += strlen($b1);
$p2 = stripos(substr($tx, $p1), $b2, 1);
if($p2===false) return '';
return substr( $tx, $p1, $p2);
}

?>
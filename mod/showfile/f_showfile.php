<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

function showfile($a){
$fe = pathinfo($a,PATHINFO_EXTENSION);
switch ($fe){
case  'txt': return txt_file($a); break;
case 'html': return html_file($a); break;
default: return "Unknown file type \"$a\" in SHOWFILE module.";
}
}

function txt_file($fn){
global $file_encoding, $site_encoding;
$afn = $_SERVER['DOCUMENT_ROOT'].$fn;
$cnt = file_get_contents( $afn );
$cnt = htmlspecialchars( $cnt, ENT_COMPAT, 'cp1251' );
$cnt = iconv( $file_encoding, $site_encoding, $cnt);
$cnt = nl2br( $cnt );
$cnt = str_replace('<br />','<br>',$cnt);
return $cnt;
}

function html_file($fn){
$afn = $_SERVER['DOCUMENT_ROOT'].$fn;
$cnt = file_get_contents( $afn );
return $cnt;
}


?>
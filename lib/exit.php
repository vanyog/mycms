<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

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

// На локален сървър изпраща бисквитка noadm=yes, а на отдалечен - изтрива всички бисквитки.
// Това предизмиква скриване на административното меню.

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;
include($idir."lib/f_is_local.php");
include($idir."lib/f_page_cache.php");

if (is_local()){ 
  setcookie('noadm','yes',time()+30*24*3600, '/');
}
else 
{
  $past = time() - 3600;
  foreach ( $_COOKIE as $key => $value ) setcookie( $key, $value, $past, '/' );
}

purge_page_cache($_SERVER['HTTP_REFERER']);
header( 'Location: '.acceptable($_SERVER['HTTP_REFERER'],false) );
//echo '<a href="'.$_SERVER['HTTP_REFERER'].'">Back</a>';

?>

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

// ћасив с началната част на адреси, които се бро€т
$sites = array('http://ph');

//header("Content-Type: text/html; charset=windows-1251");

if (!isset($_SERVER['HTTP_REFERER'])) die("NO");

$idir = dirname(dirname(dirname(__FILE__))).'/';

include($idir."lib/f_db_select_1.php");
include($idir."lib/f_db_table_field.php");

$rfr = $_SERVER['HTTP_REFERER'];

$url = '';

foreach($sites as $s){
  $n = strlen($s);
  if ($s==substr($rfr,0,$n)){ $url=$s; break; }
}

if (!$url) die("NO");

$url = addslashes($url);
$page = addslashes( substr($rfr,$n,strlen($rfr)-$n) );
$agent = addslashes( $_SERVER['HTTP_USER_AGENT'] );

$q = "INSERT INTO `$tn_prefix"."mod_counter` (`url`, `page`, `referrer`, `agent`, `date_time`, `IP`) VALUES ('$url', '$page', '".addslashes($_SERVER['QUERY_STRING'])."', '$agent', NOW(), '".$_SERVER['REMOTE_ADDR']."');";

mysqli_query($db_link, $q);

$fc = file_get_contents("Untitled.png");

header("Content-Type: image/png");
echo $fc;

//echo db_table_field('COUNT(*)','mod_counter',"`url`='$url' AND `page`='$page'");

?>

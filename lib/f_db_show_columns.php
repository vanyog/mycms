<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

if (!isset($idir)) $idir = dirname(dirname(__FILE__)).'/';

include_once($idir."lib/usedatabase.php");

function db_show_columns($tn, $fn = '', $in = ''){
global $tn_prefix, $db_link, $db_req_count;
$lk = '';
if ($fn) $lk = " LIKE '$fn'";
$q = "SHOW COLUMNS FROM `$tn_prefix$tn`$lk;";
$r = mysqli_query($db_link,$q);
$db_req_count++;
$rz = array();
if($r) while ( $a = mysqli_fetch_assoc($r) ) if ($in) $rz[] = $a[$in]; else $rz[] = $a;
return $rz;
}

?>

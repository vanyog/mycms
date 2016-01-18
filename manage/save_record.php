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

include("conf_manage.php");
include($idir."conf_paths.php");
include_once($idir."lib/usedatabase.php");
include_once($idir."lib/f_page_cache.php");

print_r($_POST); die;
$t = ''; $r = 0; $gtc = false;
$q1 = "UPDATE "; $q2 = ' SET'; $q3 = ' WHERE ID='; 
foreach($_POST as $k => $v){
switch ($k) {
case 'table_name': $q1 .= "`$tn_prefix$v`"; $t = $v; break;
case 'record_id': $q3 .= $v; $r = $v; break;
//case 'ID': break;
case 'date_time_2': $q2 .= " `$k`=NOW(),"; break;
case 'go_to_close': if (1*$v) $gtc = true; break;
default:
  $v1 =  str_replace(chr(60).' !--$$_',chr(60).'!--$$_',$v);
  $v1 =  str_replace('<!--$$_',chr(60).'!--$$_',$v1);
  $v1 =  str_replace('_$$-->','_$$--'.chr(62),$v1);
  $q2 .= " `$k`='".addslashes($v1)."',";
}
}

$q = $q1.substr($q2,0,strlen($q2)-1).$q3.';';

mysqli_query($db_link,$q);

session_start();

if ($gtc){ print_r($_SESSION['http_referer']); die;
  purge_page_cache($_SESSION['http_referer']);
  header('Location: '.$_SESSION['http_referer']);
}
else header('Location: '.$adm_pth.'edit_record.php?t='.$t.'&r='.$r);

?>

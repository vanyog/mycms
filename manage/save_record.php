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

// «аписване в базата данни след редактиране с edit_data.php

include("conf_manage.php");
include($idir."conf_paths.php");
include_once($idir."lib/usedatabase.php");
include_once($idir."lib/f_page_cache.php");
include_once($idir."lib/f_element_correction.php");
include_once($idir."lib/f_db_field_types.php");
include_once($idir."lib/f_db_field_names.php");

$t = ''; $r = 0; $gtc = false;
$q1 = "UPDATE "; $q2 = ' SET'; $q3 = ' WHERE ID='; 
if(!count($_POST)) die("No data posted.");
foreach($_POST as $k => $v){
switch ($k) {
case 'table_name':
     $q1 .= "`$tn_prefix$v`";
     $t = $v;
     $ft = db_field_types($v);
     $fn = db_field_names($v);
     $ft = array_combine($fn, $ft);
     break;
case 'record_id': $q3 .= $v; $r = $v; break;
case 'date_time_2': if($t!='schedules') $q2 .= " `$k`=NOW(),"; break;
case 'go_to_close': if (1*$v) $gtc = true; break;
case 'start_edit_time':
   $tm = time() - $v;
   $fn = $_POST['table_name'].'.'.$_POST['record_id'];
   $q = "INSERT INTO `$tn_prefix".
        "worktime` (`name`,`time`) VALUES ('$fn', $tm) ON DUPLICATE KEY UPDATE `time`=`time`+$tm;";
//   die($q);
   mysqli_query($db_link, $q);
   break;
case 'username': if(($t=='users')&&($v=='')) break;
default:
  $v1 = element_correction($v);
  if( ($t=='content') && ($k=='text') ) $v1 = str_replace( ' />', '>', $v1);
  if($v1=='') switch ($ft[$k]){
              case 1: $q2 .= " `$k`=0,"; break;
              default: $q2 .= " `$k`='',";
              }
  else if(is_numeric($v1)) $q2 .= " `$k`=".addslashes($v1).",";
       else $q2 .= " `$k`='".addslashes($v1)."',";
}
}

$q = $q1.substr($q2,0,strlen($q2)-1).$q3.';';

mysqli_query($db_link,$q);
$e = mysqli_error($db_link);
if($e) die($e);

session_start();

if ($gtc && isset($_SESSION['http_referer'])){ //print_r($_SESSION['http_referer']); die;
  purge_page_cache($_SESSION['http_referer']);
  header('Location: '.$_SESSION['http_referer']);
}
else header('Location: '.$adm_pth.'edit_record.php?t='.$t.'&r='.$r);

?>

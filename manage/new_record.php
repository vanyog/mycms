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

// Вмъкване на нов запис в таблица $_GET['t']
// Стойностите на други параметри $_G['x'], се използват като стойности на полета 'x'
// След вмъкване на записа той се отваря за редактиране с edit_record.php

include("conf_manage.php"); 
include($idir."lib/f_db_field_types.php");
include($idir."lib/f_db_field_names.php");
include($idir."lib/f_db_table_field.php");
include($idir."conf_paths.php");

$tb = $_GET['t'];
$ft = db_field_types($tb);
$fn = db_field_names($tb);// die(print_r($ft,true));

$q = "INSERT INTO `$tn_prefix$tb` SET ";
foreach($fn as $i => $n){
  switch ($n){
  case 'ID': if (isset($_GET[$n])) $q .= '`ID`='.(1*$_GET[$n]).', ';
     break;
  case 'date_time_1':
     if (!isset($_GET['date_time_1'])) $q .= "`$n`=NOW(), ";
     else $q .= "`$n`='".addslashes($_GET['date_time_1'])."', ";
     break;
//  case 'hidden': die("aaa");break;
  case 'language':
     if($tb=='users'){
     if (!isset($_GET['language'])) $q .= "`$n`='English', ";
     else $q .= "`$n`='".addslashes($_GET['language'])."', ";
     }
     break;
  case 'place': $pl = intval( db_table_field('MAX(`place`)',$tb,'1') ) + 10;
     $q .= "`$n`='$pl', "; break;
  case 'template_id':
     if (isset($_GET['template_id'])) $q .= "`template_id`=".(1*$_GET['template_id']).', ';
     else $q .= "`template_id`=1, "; break;
  case 'username': if($tb=='users') break;
  default:
    $v = '';
    if (isset($_GET[$n])) $v = $_GET[$n];
    if ($v) $q .= "`$n`='$v', ";
    else { switch ($ft[$i]){
           case 1:
           case 3: $q .= "`$n`=0, "; break;
           case 12: $q .= "`$n`='0000-01-01 00:00:00', "; break;
           case 254: $q .= "`$n`='".firstEnumValue($n)."', "; break;
           default: $q .= "`$n`='', ";
           }
    }
  }
}

$q = substr($q,0,strlen($q)-2).";";
//echo $q; die;

mysqli_query($db_link,$q);

$i = mysqli_insert_id($db_link);

if (!$i) die("Can't create new record. Error in SQL statement:<br>$q");

header('Location: '.$adm_pth.'edit_record.php?t='.$tb.'&r='.$i);

function firstEnumValue($n){
global $db_link, $tb;
$q = "SHOW COLUMNS FROM `$tb` WHERE Field = '$n'; ";
$r = mysqli_query($db_link, $q);
$rc=mysqli_fetch_assoc($r);
$a = array();
$i = preg_match('/enum\(\'(.*?)\'/', $rc['Type'], $a, PREG_UNMATCHED_AS_NULL);
return $a[1];
}

?>

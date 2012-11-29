<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Вмъкване на нов запис в таблица $_GET['t']
// След вмъкване на записа той се отваря за редактиране с edit_record.php

$idir = dirname(dirname(__FILE__)).'/';

include($idir."lib/f_db_field_types.php");
include($idir."lib/f_db_field_names.php");
include($idir."lib/f_db_table_field.php");
include($idir."conf_paths.php");

$tb = $_GET['t'];
$ft = db_field_types($tb);
$fn = db_field_names($tb);

$q = "INSERT INTO `$tn_prefix$tb` SET ";
foreach($fn as $i => $n){
  switch ($n){
  case 'ID': break;
  case 'date_time_1': $q .= "`$n`=NOW(), "; break;
  case 'hidden': break;
  case 'place': $pl = db_table_field('MAX(`place`)',$tb,'1')+10;
     $q .= "`$n`='$pl', "; break;
  case 'template_id': $q .= "`template_id`=1, "; break;
  default:
    $v = '';
    if (isset($_GET[$n])) $v = $_GET[$n];
    if ($v) $q .= "`$n`='$v', ";
    else { if ($ft[$i]=='int') $q .= "`$n`=0, "; else $q .= "`$n`='', "; }
  }
}

$q = substr($q,0,strlen($q)-2).";";
//echo $q; die;

mysql_query($q,$db_link);

$i = mysql_insert_id($db_link);

header('Location: '.$adm_pth.'edit_record.php?t='.$tb.'&r='.$i);

?>

<?php
// Copyright: Vanyo Georgiev info@vanyog.com

$idir = dirname(dirname(__FILE__)).'/';

include($idir."conf_paths.php");
include($idir."lib/usedatabase.php");

$t = ''; $r = 0; $gtc = false;
$q1 = "UPDATE "; $q2 = ' SET'; $q3 = ' WHERE ID='; 
foreach($_POST as $k => $v){
switch ($k) {
case 'table_name': $q1 .= "`$tn_prefix$v`"; $t = $v; break;
case 'record_id': $q3 .= $v; $r = $v; break;
//case 'ID': break;
case 'date_time_2': $q2 .= " `$k`=NOW(),"; break;
case 'go_to_close': if (1*$v) $gtc = true; break;
default: $q2 .= " `$k`='".addslashes($v)."',";
}
}

$q = $q1.substr($q2,0,strlen($q2)-1).$q3.';';

mysql_query($q,$db_link);

if ($gtc) header('Location: '.$adm_pth.'show_table.php?t='.$t);
else header('Location: '.$adm_pth.'edit_record.php?t='.$t.'&r='.$r);
?>

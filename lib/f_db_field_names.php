<?php

include_once("usedatabase.php");

function db_field_names($t){
global $db_link,$tn_prefix;
$q = "SELECT * FROM $tn_prefix$t LIMIT 1,1;";
$r = mysql_query($q,$db_link);
$rz = array();
if (!$r) return $rz;
$n = mysql_num_fields($r);
for($i=0; $i<$n; $i++){
  $rz[] = mysql_field_name($r,$i);
}
return $rz;
}

?>

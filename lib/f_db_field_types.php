<?php

include_once("usedatabase.php");

function db_field_types($t){
global $db_link,$tn_prefix;
$q = "SELECT * FROM $tn_prefix$t LIMIT 1,1;";
$r = mysql_query($q,$db_link);
$rz = array();
if ($r){
  $n = mysql_num_fields($r);
  for($i=0; $i<$n; $i++){
    $rz[] = mysql_field_type($r,$i);
  }
}
return $rz;
}

?>

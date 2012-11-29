<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Връща неповтарящите се стойности от поле $fn на таблица $tn,
// отговарящи на условие $wh

include_once("f_db_select_m.php");

function db_field_values($fn,$tn,$wh, $lm = ''){
$d = db_select_m( "`$fn`", $tn, "$wh GROUP BY `$fn` ORDER BY `$fn` $lm" );// print_r($d);
$rz = array();
foreach($d as $r) $rz[] = $r[$fn];
return $rz;
}

?>

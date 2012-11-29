<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include_once('usedatabase.php');

function db_table_exists($t){
global $database, $db_link, $tn_prefix;
$r = mysql_query("SHOW TABLES FROM $database;", $db_link);
$ls = array();
while ($l = mysql_fetch_row($r)) $ls[] = $l[0];
//echo $tn_prefix.$t.'<p>'; print_r($ls); die;
return in_array($tn_prefix.$t,$ls);
}

?>

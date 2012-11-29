<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include_once("usedatabase.php");

function db_tables(){
global $db_link;
$q = "SHOW TABLES;";
$rs = mysql_query($q,$db_link);
$rz = array();
while ($a = mysql_fetch_array($rs)){
 $rz[] = $a[0];
}
return $rz;
}

?>

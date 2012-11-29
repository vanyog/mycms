<?php
// Copyright: Vanyo Georgiev info@vanyog.com

$idir = dirname(dirname(__FILE__)).'/';

include($idir.'lib/usedatabase.php');

$t = $_GET['t'];
$id = $_GET['r'];

$q = "DELETE FROM `$tn_prefix$t` WHERE `ID`=$id;";
//echo $q;
mysql_query($q,$db_link);

$l = 'Location: show_table.php?t='.$t;
//echo $l;
header($l);
?>

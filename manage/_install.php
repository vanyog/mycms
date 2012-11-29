<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include('../conf_database.php');

$fc = file_get_contents('tables.sql');

$fc = str_replace('IF NOT EXISTS `content`',   "IF NOT EXISTS `$tn_prefix"."content`",$fc);
$fc = str_replace('IF NOT EXISTS `menu_items`',"IF NOT EXISTS `$tn_prefix"."menu_items`",$fc);
$fc = str_replace('IF NOT EXISTS `pages`',     "IF NOT EXISTS `$tn_prefix"."pages`",$fc);
$fc = str_replace('IF NOT EXISTS `scripts`',   "IF NOT EXISTS `$tn_prefix"."scripts`",$fc);
$fc = str_replace('IF NOT EXISTS `templates`', "IF NOT EXISTS `$tn_prefix"."templates`",$fc);
$fc = str_replace('IF NOT EXISTS `visit_history`', "IF NOT EXISTS `$tn_prefix"."visit_history`",$fc);

$fc = str_replace('INSERT INTO `content`',   "INSERT INTO `$tn_prefix"."content`",$fc);
$fc = str_replace('INSERT INTO `menu_items`',"INSERT INTO `$tn_prefix"."menu_items`",$fc);
$fc = str_replace('INSERT INTO `pages`',     "INSERT INTO `$tn_prefix"."pages`",$fc);
$fc = str_replace('INSERT INTO `scripts`',   "INSERT INTO `$tn_prefix"."scripts`",$fc);
$fc = str_replace('INSERT INTO `templates`', "INSERT INTO `$tn_prefix"."templates`",$fc);

header("Content-Type: text/html; charset=utf-8");
echo $fc;

?>

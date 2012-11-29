<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Страница, която показва информация за таблиците от базата данни.

$idir = dirname(dirname(__FILE__)).'/';

include($idir."conf_paths.php");
include($idir."lib/f_db_tables.php");
include($idir."lib/f_db_field_names.php");

$tl = db_tables();

$page_content = '<p> Database: <strong>'.$database.'</strong> </p>
<p>Tables:</p>
<table>';

$pl = strlen($tn_prefix);

foreach($tl as $t) if (substr($t,0,$pl)==$tn_prefix){
$t0 = substr($t,strlen($tn_prefix));
$page_content .= '<tr><th align="left"><a href="'.$adm_pth.'show_table.php?t='.$t0.'">'.$t0."</a></strong></th>\n<td>     ";
$fn = db_field_names($t0);
foreach($fn as $n) $page_content .= "   $n";
$page_content .= "</td></tr>\n";
}
 
$page_content .= '</table>';

include_once("build_page.php");
?>

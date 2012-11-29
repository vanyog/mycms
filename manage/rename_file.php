<?php
// Copyright: Vanyo Georgiev info@vanyog.com

$idir = dirname(dirname(__FILE__)).'/';

include($idir.'conf_paths.php');

$fn = $_GET['o'];
$nn = $_GET['n'];

$pn = dirname($fn);
if ($pn) $pn .= '/';

rename("$apth$fn","$apth$pn$nn");
//echo "$apth$fn => $apth$pn$nn";

header('Location: '.$adm_pth.'edit_file.php?f='.dirname($fn));

?>

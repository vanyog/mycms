<?php
// Copyright: Vanyo Georgiev info@vanyog.com

$idir = dirname(dirname(__FILE__)).'/';

include($idir."conf_paths.php");

$fn = $_GET['f'];
$afn = $apth.$fn;

$f = fopen($afn,"w") or die("Can't create file $fn"); fclose($f);

header('Location: '.$adm_pth.'edit_file.php?f='.dirname($fn));

?>

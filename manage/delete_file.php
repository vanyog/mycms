<?php
// Copyright: Vanyo Georgiev info@vanyog.com

$idir = dirname(dirname(__FILE__)).'/';

include($idir."conf_paths.php");

$fn = $_POST['file'];
$afn = $apth.$fn;
$dn = dirname($fn);

if ($dn=='.') $dn = '/..';

if (is_file($afn)) unlink($afn);
if (is_dir($afn)) rmdir($afn);

header('Location: '.$adm_pth.'edit_file.php?f='.$dn);

?>

<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include("../conf_paths.php");
include("ta_ctag.php");

$fn = $_POST['file'];
$afn = $apth.$fn;
$i = strrpos($fn,'/');
$pn = substr($fn,0,$i);
$fc = $_POST['editor1'];
if (get_magic_quotes_gpc()) $fc = stripslashes($fc);
if ( !(('/'.$fn == $adm_pth.'edit_file.php') ||
       ('/'.$fn == $adm_pth.'save_file.php')
      )
   )
{
   $fc = str_replace($ta_fctag,$ta_ctag,$fc);
}
//echo $afn; die;

$f = fopen($afn,"w");
fwrite( $f, $fc );

header('Location: '.$adm_pth.'edit_file.php?f='.$fn);
?>

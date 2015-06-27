<?php

/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include("conf_manage.php");
include($idir."conf_paths.php");
include(dirname(__FILE__)."/ta_ctag.php");

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
   $fc = str_replace(chr(60).' !--$$_',chr(60).'!--$$_',$fc); 
   $fc = str_replace('&lt; !--$$_',chr(60).'!--$$_',$fc);
   $fc = str_replace('_$$--&gt;','_$$--'.chr(62),$fc);

}
//echo $afn; die;

if (!is_writable($afn)){
  session_start();
  $_SESSION['edit_result_message'] = "The file $afn is not writable";
}
$f = fopen($afn,"w");
if ($f){
  fwrite( $f, $fc );
  fclose($f);
}

header('Location: '.$adm_pth.'edit_file.php?f='.$fn);
?>

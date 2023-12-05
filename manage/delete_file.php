<?php

/*
VanyoG CMS - a simple Content Management System
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

$fn = '';
if(isset($_POST['file'])) $fn = $_POST['file']; 
if(isset($_GET['f'])    ){
  $fn = $_SERVER['DOCUMENT_ROOT'].urldecode($_GET['f']);
  if(file_exists("$fn")){ var_dump(unlink($fn)); die($fn); }
  header("Location: ".$_SERVER['HTTP_REFERER']);
  die;
}

$afn = $apth.$fn;
$dn = dirname($fn);

if ($dn=='.') $dn = '/..';

$rz = true;
if (is_file($afn)) $rz = unlink($afn);
if (is_dir($afn)) $rz = rmdir($afn);

if (!$rz) {
   session_start();
   $_SESSION['edit_result_message'] = "The file $afn was not deleted";
}

header('Location: '.$adm_pth.'edit_file.php?f='.$dn);

?>

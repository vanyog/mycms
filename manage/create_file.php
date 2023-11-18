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

$fn = $_GET['f'];
$afn = $apth.$fn;

$drn = dirname($afn);
if (!is_writable($drn)){
  session_start();
  $_SESSION['edit_result_message'] = "The folder $drn is not writable";
}
else {
  $f = fopen($afn,"w") or die("Can't create file $fn");
  fclose($f);
}

header('Location: '.$adm_pth.'edit_file.php?f='.dirname($fn));

?>

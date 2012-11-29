<?php
// Copyright: Vanyo Georgiev info@vanyog.com

error_reporting(E_ALL); ini_set('display_errors',1);

include('../conf_paths.php');

setcookie($adm_name, $adm_value, time()+60*60*24*30,'/');

$page_content = '';

include("build_page.php");
?>

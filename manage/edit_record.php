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

$idir = dirname(dirname(__FILE__)).'/';

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_field_types.php');
include_once("editor.php");

session_start();
if (strpos( $_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'] ) === false ) 
   $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];

$t = $_GET['t'];
$id = 1*$_GET['r'];

$ft = db_field_types($t);
$r = db_select_1('*',$t,"ID=$id");

$page_content = '<script type="text/javascript"><!--
function saveAndClose(){
var f = document.edit_form;
f.go_to_close.value = 1;
f.submit();
}
--></script><p>Database: <strong>'.$database.'</strong> Table: <strong>'.$tn_prefix.$t.'</strong></p>
<form method="POST" action="save_record.php" name="edit_form">
<input type="hidden" name="table_name" value="'.$t.'">
<input type="hidden" name="record_id" value="'.$id.'">
<input type="hidden" name="go_to_close" value="0">
';

$i = 0;
if ($r) foreach($r as $k => $v){
 switch ($ft[$i]){
 case 'blob'    : $page_content .= '<p>'.$k.':<br>'.editor($k,stripslashes($v)).'</p>'."\n"; break;
 case 'datetime': $page_content .= '<p>'.$k.':<br><input type="text" name="'.$k.'" value="'.$v.'"></p>'."\n"; break;
 case 'int'     : $page_content .= '<p>'.$k.':<br><input type="text" name="'.$k.'" value="'.$v.'"></p>'."\n"; break;
 case 'string'  : $v = stripslashes($v); $v = str_replace('"','&quot;',$v);
    $page_content .= '<p>'.$k.':<br><input type="text" name="'.$k.'" value="'.$v.'"></p>'."\n"; 
    break;
 default: $page_content .= '<p>Unknown type '.$ft[$i].'</p>';
 }
 $i++;
}

$page_content .= '
<input type="submit" value="Save"> 
<input type="button" value="Save & Close" onclick="saveAndClose();"> 
<input type="button" value="Cancel" onclick="document.location=\''.$adm_pth.'show_table.php?t='.$t.'\'">
</form>';

include("build_page.php");
?>

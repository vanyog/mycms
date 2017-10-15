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
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_field_types.php');
include_once("editor.php");

session_start();
// ��� ������������ �������� � �������� �� ���� ������, �� ������� � ���������� �� �������
if (isset($_SERVER['HTTP_REFERER']) && (strpos( $_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'] ) === false )) 
   $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];

$t = $_GET['t'];
$id = 1*$_GET['r'];

$ft = db_field_types($t);//print_r($ft); die;
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
<input type="hidden" name="start_edit_time" value="'.time().'">
<table>';

$i = 0;
if ($r) foreach($r as $k => $v){
 $page_content .= '<tr>';
 switch ($ft[$i]){
 case 252: $page_content .= '<td class="r">'.$k.':</td><td>'.editor($k,stripslashes($v)).'</td>'."\n"; break;
 case 1  :
 case 3  : 
 case 4  : 
 case 5  :
 case 10 :
 case 12 : 
 case 254: $page_content .= '<td class="r">'.$k.':</td><td><input type="text" name="'.$k.'" value="'.$v.'"></td>'."\n";
           break;
 case 253: $v = stripslashes($v); $v = str_replace('"','&quot;',$v);
    $page_content .= '<td class="r">'.$k.':</td><td><input type="text" name="'.$k.'" value="'.$v.'"></td>'."\n"; 
    break;
 default: $page_content .= '<td>'.$k.'</td><td>Unknown type '.$ft[$i].'</td>';
 }
 $page_content .= '</tr>';
 $i++;
}
else die("<p>Record ID=$id do not exist.</p>
<p>Click <a href=\"new_record.php?t=$t&ID=$id\">here</a> to create.</p>");

$rfr = $adm_pth.'show_table.php?t='.$t;
if (isset($_SESSION['http_referer'])) $rfr = $_SESSION['http_referer'];

$page_content .= '</table>
<input type="submit" value="Save"> 
<input type="button" value="Save & Go back" onclick="saveAndClose();"> 
<input type="button" value="Table" onclick="document.location=\''.$adm_pth.'show_table.php?t='.$t.'\'">
<input type="button" value="Cancel" onclick="document.location=\''.$rfr.'\'">
</form>';

include("build_page.php");
?>

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

$exe_time = microtime(true);

include("conf_manage.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_field_types.php');
include_once("editor.php");

session_start();
// Ако рефериращата страница е различна от този скрипт, се запомня в променлива на сесията
if (isset($_SERVER['HTTP_REFERER']) && (strpos( $_SERVER['HTTP_REFERER'], $_SERVER['SCRIPT_NAME'] ) === false ))
   $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];

$t = $_GET['t'];
$id = is_numeric($_GET['r']) ? 1*$_GET['r'] : 0;

$ft = db_field_types($t);//print_r($ft); die;
$r = db_select_1('*',$t,"ID=$id");

$page_content = '<script>
function saveAndClose(){
var f = document.edit_form;
f.go_to_close.value = 1;
f.submit();
}
function doDublicate(id){
if (confirm("Do you want to duplicate record ID="+id+"?")){
document.location="duplicate_record.php?t=english_bulgarian&r="+id;
}
}
function openOtherRecord(f,e){
var r = document.forms.edit_form[f].value;
var l = "edit_record.php?t='.$t.'&r="+r;
e.preventDefault();
document.location.href = l;
}
function openOtherLanguage(f,e){ 
var l = document.forms.edit_form[f].value;
var n = document.forms.edit_form["name"].value;
var a = "'.current_pth(__FILE__).'ajax_getRecId.php?a=" + Math.floor(Math.random() * 1000) +
        "&t='.$t.'&language=" + l + "&name=" + n;alert(a);
ajaxO.onreadystatechange = otherLanguageIdSent;
ajaxO.open("GET", a, true);
ajaxO.send();
e.preventDefault();
}
function otherLanguageIdSent(){
if (ajaxO.readyState == 4 && ajaxO.status == 200){
  var l = "edit_record.php?t='.$t.'&r="+ajaxO.responseText;
  alert(l);
  document.location.href = l;
}
}
function onDelete(id)
{
if(confirm("Do you really want to delete this record (id="+id+")?")){ 
  document.location.href = "delete_record.php?t='.$t.'&r='.$id.'";
}
}
</script>
<p>Database: <strong>'.$database.'</strong> Table: <strong>'.$tn_prefix.$t.'</strong></p>
<form method="POST" action="save_record.php" name="edit_form">
<input type="hidden" name="table_name" value="'.$t.'">
<input type="hidden" name="record_id" value="'.$id.'">
<input type="hidden" name="go_to_close" value="0">
<input type="hidden" name="start_edit_time" value="'.time().'">
<table>';
//die(print_r($ft,true));
$i = 0;
if ($r) foreach($r as $k => $v){
 if(($ft[$i]==1)||($ft[$i]==3)) $js = ' onfocus="select()"';
 else $js = '';
 $page_content .= '<tr>';
 if(isset($_GET[$k])) $v = $_GET[$k];
 switch ($ft[$i]){
 case 1  :
 case 2  :
 case 3  :
 case 4  : 
 case 5  :
 case 10 :
 case 12 : 
 case 254: $page_content .= '<td class="r">'.$k.':</td><td><input type="text" name="'.$k.
                            '" value="'.$v.'"'.$js.'>';
           if($k=='ID') $page_content .= ' <button onclick="openOtherRecord(\'ID\',event);">open</button>';
           $page_content .= '</td>'."\n";
           break;
 case 252: if(!isset($v)) $v = ''; 
           $page_content .= '<td class="r">'.$k.':</td><td>'.editor($k,stripslashes($v)).'</td>'."\n"; 
           break;
 case 253: $v = isset($v) ? stripslashes($v) : ''; 
    $v = str_replace('"','&quot;',$v);
    $page_content .= '<td class="r">'.$k.':</td><td><input type="text" name="'.$k.'" value="'.$v.'">';
    if($k=='language'){ $page_content .= ' <button onclick="openOtherLanguage(\'language\',event);">open</button>'; }
    $page_content .= '</td>'."\n"; 
    break;
 default: $page_content .= '<td>'.$k.'</td><td>Unknown type '.$ft[$i].'</td>';
 }
 $page_content .= '</tr>';
 $i++;
}
else die("<p>Record ID = $id do not exist.</p>
<p>Click <a href=\"new_record.php?t=$t&ID=$id\">here</a> to create.</p>
<p><a href=\"/\">Home</a> &nbsp; <a href=\"show_table.php?t=$t\">Table</a></p>");

$rfr = $adm_pth.'show_table.php?t='.$t;
if (isset($_SESSION['http_referer'])) $rfr = $_SESSION['http_referer'];

$page_content .= '</table>
<input type="submit" value="Save"> 
<input type="button" value="Save & Go back" onclick="saveAndClose();"> 
<input type="button" value="Duplicate" onclick="doDublicate('.$id.');">
<input type="button" value="Table" onclick="document.location=\''.$adm_pth.'show_table.php?t='.$t.'\'">
<input type="button" value="Delete" onclick="onDelete('.$id.');"> 
<input type="button" value="Cancel" onclick="document.location=\''.$rfr.'\'">
</form>';

include("build_page.php");
?>

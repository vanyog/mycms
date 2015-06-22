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

// Този скрипт генерира страница за редактиране на файловете на сайта

include("conf_manage.php");
include_once($idir."conf_paths.php");
include_once($idir."lib/f_strip_last_name.php");

$f = ''; // Име на файла или директорията, които ще се редактират
if (isset($_GET['f'])) $f = remove_dots($_GET['f']);

$d = $apth.$f; // Абсолютен път до файла или директорията във файловата система на сървъра
//echo $d; die;

$page_content = '';

if (is_dir($d)){ // Ако е директория се показва таблица с файловете в нея
                 // ------------------------------------------------------

if ($f) $f .= '/';

$page_header = '<script type="text/javascript"><!--
function fileName(){
var frm = document.forms.file_form;
var f = frm.file;
for(var i=0;i<f.length;i++) if (f[i].checked) return f[i].value;
return false;
}
function doRename(){
var fn = fileName();
if (fn){
  var nn = prompt("Enter a new name of "+fn);
  if (nn) document.location = "'.$adm_pth.'rename_file.php?o="+fn+"&n="+nn;
}
else alert("Choose a file to be renamed");
}
function doDelete(){
if (!'.stored_value('can_delete_files',0).'){
  alert("Deleting files on this system is not permited.");
  return;
}
var fn = fileName();
if (!fn) { alert("Choose a file to be deleted"); return; }
if (fn && confirm("Do you really want to delete file "+fn)){
  document.forms.file_form.action = "'.$adm_pth.'delete_file.php"
  document.forms.file_form.submit();
}
else return;
}
function doView(){
var fn = fileName();
var lc = "'.$pth.'"+fn;
var q = document.forms.file_form.query.value;
if (q) lc = lc+"?"+q;  
if (fn) document.location = lc;
else alert("Choose a file to be viewed as a web page");
}
function doCreate(){
  var fn = prompt("Create a file with name:");
  if (fn){
    fn = "'.$f.'"+fn;
    document.location = "'.$adm_pth.'create_file.php?f="+fn;
  }
}
function doMakeDir(){
  var fn = prompt("Create a file with name:");
  if (fn){
    fn = "'.$f.'"+fn;
    document.location = "'.$adm_pth.'create_dir.php?f="+fn;
  }
}
--></script>';

$page_content .= '<p>Folder: <strong>/'.$f.'</strong> </p>
<form method="POST" action="'.$adm_pth.'edit_file.php" name="file_form">
<table border="1" cellspacing="0">
<tr>
';

$dr = opendir($d);
$dl = array();
while ($a = readdir($dr)) if ( ($a!='.') && !(($a=='..')&&($d==$apth)) ) $dl[] = $a;
sort($dl);

$cls = 3; $rw = 0; $j = 0; $stp = floor(count($dl)/$cls); $cln=0;
$rem = count($dl) % $cls; $rc = 0; 
for($i=0; $i<count($dl); $i++){
  $a = $dl[$j];
  $j += $stp;
  if ($rc<$rem) $j++;
  $rc++;
  if ($j>count($dl)-1) { $rw++; $j = $rw; }
  $s1 = ''; $s2 = '';
  if (is_dir($d.'/'.$a)){ $s1 = '<strong>'; $s2 = '</strong>'; }
  $page_content .= '<td><input type="radio" name="file" value="'.$f.$a.
  "\"></td><td>$s1<a href=\"edit_file.php?f=$f$a\">$a</a>$s2</td>\n"; 
  $cln++;
  if (($cln % $cls)==0){ $page_content .= "</tr><tr>\n"; $rc = 0; }
}
if (($cln % $cls)!=0) $page_content .= "</tr>";


$page_content .= '</table>
<p>?<input type="text" name="query"></p>
<p><input type="button" value="Create file" onclick="doCreate();"> 
<input type="button" value="Make dir" onclick="doMakeDir();"> 
<input type="button" value="View" onclick="doView();"> 
<input type="button" value="Rename" onclick="doRename();"> 
<input type="button" value="Delete" onclick="doDelete();"></p> 
</form>';

}
else { // Ако е файл се показва форма за редактиране на съдържанието му
       // -------------------------------------------------------------

include("editor.php");

$fc = array();
$can_edit = array('php','txt','css','js','html','sql','htaccess','');
$e = pathinfo($d,PATHINFO_EXTENSION);
if (is_file($d) && in_array($e,$can_edit)) $fc=file($d);
else {
  $page_content .= '
<script type="text/javascript"><!--
function desableEditForm(){
var f = document.forms.edit_form;
f.submit_button.disabled = "disabled";
f.save_as.disabled="disabled";
}
--></script>
<p class="red">You can\'t edit file with extension '."'<strong>.$e</strong>'.<p>";
  $body_adds = ' onload="desableEditForm();"';
}
if (!file_exists($d)) $page_content .= '<p slass="red">File not exists!</p>';

$tx = '';
foreach($fc as $l) $tx .= $l;

$page_content .= '<script type="text/javascript"><!--
function doSaveAs(){
  var fn = prompt("Save file as");
  if (!fn) return;
  var f = document.forms.edit_form;
  var d = "'.strip_last_name($f).'";
  if (d) d = d + "/";
  f.file.value = d+fn;
  f.submit();
}
function doOpen(){
  var te = document.forms.edit_form.editor1;
  var st = te.value.substring(te.selectionStart,te.selectionEnd);
  if (!st.length) return; 
  var d = "'.strip_last_name($f).'";
  if (d) d += "/";  
  document.location = "'.$adm_pth.'edit_file.php?f=" + d + st;
}
--></script>
<p><strong>/'.$f.'</strong></p>
<form action="save_file.php" method="POST" name="edit_form">
<input type="hidden" name="file" value="'.$f.'">
'.editor('editor1',$tx).'
<br><input type="submit" value="Save" name="submit_button"> 
<input type="button" value="Save as" onclick="doSaveAs();" name="save_as">
<input type="button" value="Close" onclick="document.location=\''.$adm_pth.'edit_file.php?f='.strip_last_name($f).'\'">
<input type="button" value="Open" onclick="doOpen();">
</form>
';

}

function remove_dots($p){
$a = explode('/',$p); //print_r($a);
$rz = ''; $rm = false;
for($i=count($a)-1; $i>=0; $i--)
   if ($a[$i]=='..') { unset($a[$i]); $rm = true; }
   else if ($rm) { unset($a[$i]); $rm = false; } //print_r($a);
foreach($a as $b) $rz .= '/'.$b; 
$rz = substr($rz,1); //echo $rz;
return $rz; 
}

include("build_page.php");
?>

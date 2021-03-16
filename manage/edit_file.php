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

$exe_time = microtime(true);

include("conf_manage.php");
include_once($idir."conf_paths.php");
include_once($idir."lib/f_is_local.php");
include_once($idir."lib/f_relative_to.php");
include_once($idir."lib/f_strip_last_name.php");

$f = ''; // Име на файла или директорията, които ще се редактират
if (isset($_GET['f'])) $f = $_GET['f'];

// Абсолютен път до файла или директорията във файловата система на сървъра
$d = str_replace('\\', '/', realpath($apth.$f));

$f = relative_to($apth,$d.'/');
//echo("$apth$f<br>$d<br>$f<br>");

$page_content = '';

// Показване на съобщение за неуспех на последната операция, ако има такова
session_start();
if (isset($_SESSION['edit_result_message'])){
  $page_content .= '<p style="color:#FF0000;">'.$_SESSION['edit_result_message']."</p>\n";
  unset($_SESSION['edit_result_message']);
}



if (is_dir($d)){ // Ако е директория се показва таблица с файловете в нея
                 // ------------------------------------------------------

//if($f) $f .= '/';

// Дали е разрешено изтриване на файлове.
// Разрешено е на локален сървър, а на отдалечен сървър,
// по подразбиране не е разрешено, освен ако е зададена настройка
// can_delete_files със стойност 1.
$cdel = stored_value('can_delete_files','false');
if(is_local()) $cdel = 'true';

$page_header = '<script>
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
if (!'.$cdel.'){
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
function doOptimize(){
var fn = fileName();
if (fn){
    var fp = "'.$adm_pth.'optimize.php?f=" + fn;
    document.location = fp;
}
else alert("Choose a file to optimize");
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
</script>';

$ne = identic_letter($apth, $d) - strlen($apth);
$alls = ( $ne > - 1 ) || stored_value('accsess_all_file_system');
if( ($ne<-1) && !$alls) { http_response_code(403); die('Forbidden'); }

$page_content .= '<p>Folder: <strong>'.$d.'</strong> </p>
<form method="POST" action="'.$adm_pth.'edit_file.php" name="file_form">
<table border="1" cellspacing="0">
<tr>
';

$dr = opendir($d);
$dl = array();
while ($a = readdir($dr)) if ($a!='.') $dl[] = $a;
sort($dl);

$cls = 3; $rw = 0; $j = 0; $stp = floor(count($dl)/$cls); $cln=0;
$rem = count($dl) % $cls; $rc = 0;
for($i=0; $i<count($dl); $i++){
  $a = $dl[$j];// die("<br>$d $f $a");
  $j += $stp;
  if ($rc<$rem) $j++;
  $rc++;
  if ($j>count($dl)-1) { $rw++; $j = $rw; }
  $s1 = ''; $s2 = '';
  if (is_dir($d.'/'.$a)){ $s1 = '<strong>'; $s2 = '</strong>'; }
  $page_content .= '<td style="width:1%;"><input type="radio" name="file" value="'.$f.$a."\"></td>";
  if(is_dir($d.'/'.$a) && ($a=='..') && !$alls)
     $page_content .= "<td>$s1$a$s2"; //  Без линк
  else
     if(in_array(strtolower(pathinfo($a, PATHINFO_EXTENSION)), array('png', 'jpg', 'jpeg', 'gif')))
        $page_content .= "<td>$s1<a href=\"$pth$f".urlencode($a)."\">$a</a>$s2";
     else
        $page_content .= "<td>$s1<a href=\"edit_file.php?f=".urlencode("$f/$a")."\">$a</a>$s2";
  $page_content .= '</td><td style="width:1%;text-align:right;">';
  if (is_file($d.'/'.$a)) $page_content .= filesize($d.'/'.$a);
  $page_content .= "</td>\n";
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
<input type="button" value="Delete" onclick="doDelete();">
<input type="button" value="Optimize" onclick="doOptimize();"></p>
</form>
<p>&nbsp;</p>';

}


else { // Ако е файл се показва форма за редактиране на съдържанието му
       // -------------------------------------------------------------

if(substr($f,-1)=='/') $f = substr($f,0,-1);
//die("--$f--");

include_once("editor.php");
include_once($idir.'lib/f_set_self_query_var.php');

$fc = array();
$can_edit = array('css', 'inc', 'js', 'htaccess', 'html', 'htm', 'php', 'po', 'pot', 'sql', 'svg', 'txt', 'xml', '');
$e = pathinfo($d,PATHINFO_EXTENSION);
if (is_file($d) && in_array($e,$can_edit)) $fc=file($d);
else {
  $page_content .= '
<script>
function desableEditForm(){ 
var f = document.forms.edit_form;
f.submit_button.disabled = "disabled";
f.save_as.disabled="disabled";
}
</script>
<p class="red">';
  if (!file_exists($d)) $page_content .= "File $d do not exists.";
  else $page_content .= "You can't edit file with <strong>.$e</strong> extension.";
  $page_content .= "<p>";
  $body_adds = ' onload="desableEditForm();"';
}
if (!file_exists($d)) $page_content .= '<p slass="red">File not exists!</p>';

$tx = '';
foreach($fc as $l) $tx .= $l;

$enc = "windows-1251";
if(isset($_GET['enc']) && in_array($_GET['enc'], array('utf8')) ) $enc = $_GET['enc'];
$tx = iconv($enc, "$site_encoding//IGNORE", $tx);
$encp = "?enc=$enc";

$page_content .= '<script>
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
</script>
<p>File: <strong>'.$f.'</strong> &nbsp; <a href="'.set_self_query_var('enc','utf8').'">utf8</a></p>
<form action="save_file.php'.$encp.'" method="POST" name="edit_form">
<input type="hidden" name="file" value="'.$f.'">
<input type="hidden" name="start_edit_time" value="'.time().'">
'.editor('editor1',$tx).'
<br><input type="submit" value="Save" name="submit_button">
<input type="button" value="Save as" onclick="doSaveAs();" name="save_as">
<input type="button" value="Close" onclick="document.location=\''.$adm_pth.'edit_file.php?f='.strip_last_name($f).'\'">
<input type="button" value="Open" onclick="doOpen();">
</form>
<p>&nbsp;</p>
';

}

// Стара функция. Вече не се използва.

function remove_dots($p){
$a = explode('/',$p);
$rz = ''; $rm = false;
for($i=count($a)-1; $i>=0; $i--)
   if ($a[$i]=='..') { unset($a[$i]); $rm = true; }
   else     if ($rm) { unset($a[$i]); $rm = false; }
foreach($a as $b) $rz .= '/'.$b;
$rz = substr($rz,1);
return $rz;
}

include("build_page.php");
?>

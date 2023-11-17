<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2014  Vanyo Georgiev <info@vanyog.com>

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

// Показване списък на наличните модули за по-лесно добавяне

include_once($idir.'lib/f_parse_content.php');
include_once($idir.'lib/f_encode.php');

function mod_picker(){
global $page_header, $pth, $apth, $adm_pth;

$page_header .= '<script>
var tefc;
function onTeFocus(){
tefc = document.activeElement;
}
var tgToIn = "p";
function doInsertTag(){
tgToIn = prompt("Enter a html tag to be inserted", tgToIn);
insert_tag(tgToIn,tgToIn);
}
function insert_tag(t1,t2){
var te = tefc;
te.focus();
var s = te.selectionStart;
var e = te.selectionEnd;
var v = te.value;
if (t2.length) v = v.substring(0,e)+"</"+t2+">"+v.substring(e,v.length);
te.value = v.substring(0,s)+"<"+t1+">"+v.substring(s,v.length);
s += t1.length + 2;
e += t1.length + 2;
te.selectionStart = s;
te.selectionEnd = e;
}
function insert_text(t1){
var te = tefc;
te.focus();
var s = te.selectionStart;
var e = te.selectionEnd;
var v = te.value;
te.value = v.substring(0,s)+t1+v.substring(e,v.length);
e = s + t1.length;
te.selectionStart = s;
te.selectionEnd = e;
}
function insert_2_texts(t1,t2){
var te = tefc;
te.focus();
var s = te.selectionStart;
var e = te.selectionEnd;
var v = te.value;
if (t2.length) v = v.substring(0,e)+t2+v.substring(e,v.length);
te.value = v.substring(0,s)+t1+v.substring(s,v.length);
s += t1.length;
e += t1.length;
te.selectionStart = s;
te.selectionEnd = e;
}
var tag_a1 = "a href=\"/index.php?pid=\"";
var tag_a2 ="a";
var tag_s1 = "<script>\n";
var tag_s2 = "\n<"+"/script>";
var metaPressed = false;
function showCharCount(a){
var s = document.getElementById(a.id + "_count");
s.innerHTML = a.value.length;
metaPressed = false;
}
//function editor_onKey(e,v){
//if(metaPressed && (v.key=="Enter")) insert_tag(tgToIn,tgToIn);
//metaPressed = true;
//}
function toClip(a){
if(tefc){
  var n = a.innerText;
  var f = n.toLowerCase() + "_params";
  var p = "";
  if( eval("typeof "+f+"===\"function\"") )
      eval("p = "+f+"();");
  else {
     p = prompt("'.encode('Въведете стойност на параметър, ако е необходима за този модул'). '");
     if(p) p = "_" + p;
  }
  insert_2_texts("<!--$$_"+a.innerHTML+p, "_$$-->");
}
}
</script>
<style type="text/css">
#modbtn span { display:inline-block; width:160px; padding:0 5px; font-size:80%; }
#modbtn span span { display:inline; font-size:100%; padding:0; cursor:default; }
#modbtn span span:hover { background-color:#EEEEEE; } 
#modbtn span a { }
</style>
';

$rz = "<p id=\"modbtn\"><strong>Modules:</strong><br>\n";
$ml = mod_list();
$mn = array();
foreach($ml as $i=>$m){ $mn[$i] = strtoupper(pathinfo($m,  PATHINFO_BASENAME)); }
asort($mn);
foreach($mn as $i=>$m) {
  $rm = $ml[$i].'README.txt';
  $pj = $ml[$i].'params.js';
  $rz .= '<span><span onclick="toClip(this);">'.$m.'</span>';
  if(show_adm_links()){
    $elk = relative_to($apth, $ml[$i]);
    $elk = $adm_pth.'edit_file.php?f='.urlencode($elk);
    $rz .= ' <a href="'.$elk.'">&gt;</a>';
  }
  if (file_exists($rm)) $rz .= ' <a href="'.$pth.'mod/help.php?m='.$m.'" target="_blank">help</a>';
  if (file_exists($pj)) $page_header .= "<script>\n".parse_content(file_get_contents($pj))."\n</script>\n";
  $rz .= '</span> '."\n";
}
$rz .= '</p>';
return $rz;
}


?>
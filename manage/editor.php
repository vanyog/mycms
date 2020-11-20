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

include("ta_ctag.php");
include_once($idir.'lib/f_mod_list.php');
include_once($idir.'lib/f_mod_picker.php');

function editor($n,$tx){//die($n);
global $ta_ctag, $ta_fctag, $page_header;
$tx = str_replace('&','&amp;',$tx);
$tx = str_replace(chr(60).'!--$$_',chr(60).' !--$$_',$tx);
// Брой на textarea елементите
static $tec = 0;
// Ако още няма textarea елементи се извежда javascript-ът
// и mod_picker
if (!$tec){
$js = '
<script>
var tefc;
function onTeFocus(){
tefc = document.activeElement;
}
var tgToIn = "p";
function doInsertTag(){
var nt = window.prompt("Enter a html tag to be inserted", tgToIn);
if(nt == null) return;
tgToIn = nt;
insert_tag(tgToIn,tgToIn);
}
var lastEv;
function insert_tag(t1,t2){
var te = tefc;
te.focus();
var s = te.selectionStart;
var e = te.selectionEnd;
var v = te.value;
var s1 = "";
var s2 = "/";
var p = 0;
if(lastEv && lastEv.shiftKey) {
  s1 = "/";
  s2 = "";
  p = 1;
}
if (t2.length) v = v.substring(0,e)+"<"+s2+t2+">"+v.substring(e,v.length);
te.value = v.substring(0,s)+"<"+s1+t1+">"+v.substring(s,v.length);
s += t1.length + 2 + p;
e += t1.length + 2 + p;
te.selectionStart = s;
te.selectionEnd = e;  
}
function insert_text(t1, sl=true){
var te = tefc;
te.focus();
var s = te.selectionStart;
var e = te.selectionEnd;
var v = te.value;
var n = v.substring(0,s)+t1+v.substring(e,v.length);
te.value = n;
e = s + t1.length;
if(sl){
  te.selectionStart = s;
  te.selectionEnd = e;
}
else{
  te.selectionStart = e;
  te.selectionEnd = e;
}
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
var shiftPressed = false;
function showCharCount(a){
var s = document.getElementById(a.id + "_count");
s.innerHTML = a.value.length;
metaPressed = false;
shiftPressed = false;
lastEv = "";
}
function editor_onKey(e,v){
lastEv = v;
if(shiftPressed && !metaPressed && (v.key=="Enter")) insert_text("<br>", false);
var ya = "'.encode('я').'";
if(metaPressed && (v.key=="Enter")) insert_tag(tgToIn,tgToIn);
if(metaPressed && (v.key=="1")    ) insert_tag("h1","h1");
if(metaPressed && (v.key=="2")    ) insert_tag("h2","h2");
if(metaPressed && (v.key=="3")    ) insert_tag("h3","h3");
if(metaPressed && (v.key=="4")    ) insert_tag("h4","h4");
if(metaPressed && shiftPressed &&
   ( (v.key.toLowerCase()=="s") || (v.key.toLowerCase()==ya) )
  )
{
  saveAndClose();
}
if(metaPressed &&
   ( (v.key.toLowerCase()=="s") || (v.key.toLowerCase()==ya) )
  )
{
  d = document.forms.edit_form;
  if(d.submit){
    v.preventDefault();
    d.submit();
  }
}
if(v.ctrlKey || v.metaKey) metaPressed = true;
if(v.shiftKey) shiftPressed = true;
return false;
}
function doRemoveTag(){
var te = tefc;
if(!te) return;
var tx = te.value;
var s = te.selectionStart;
var e = te.selectionEnd;
if(s!=e) return;
if((tx[e]=="<")&&(tx[e+1]!="/")) while( (e<tx.length) && (tx[e]!=" ") && (tx[e]!=">")) e++;
if(e==s) return;
var tn = tx.substring(s+1,e);
while( (e<tx.length) && (tx[e]!=">") ) e++;
var ct = "</"+tn+">";
var i = tx.indexOf(ct, e+1);
var nt = tx.substring(0,s) + tx.substring(e+1, i) + tx.substring(i+ct.length);
te.value = nt;
te.selectionStart = s;
te.selectionEnd = s;
}
</script>
'.mod_picker();
} else $js = '';
$tec += 1;
// Връщане на резултата
return $js.

'<input type="button" value="tag" onclick="doInsertTag();">'.'
'.make_tag_button('a','tag_a1','tag_a2').'
'.make_insert_button('php','<?php\n// Copyright: Vanyo Georgiev info@vanyog.com\n\n?>\n').'
'.make_insert_2_button('case','\'case \\\'\'','\'\\\': break;\'', '').'
'.make_insert_2_button('include','\'include(\\\'\'','\'\\\');\'', '').'
'.make_insert_2_button('include_once','\'include_once($idir.\\\'\'','\'\\\');\'', '').'
'.make_insert_2_button('print_r','\'print_r($\'','\'); die;\'', '').'
'.make_insert_2_button('<!--$$_','\'<!--$$_\'','\'_$$-->\'', 'Insert a module').'
'.make_insert_2_button('javascript','tag_s1','tag_s2','Insert SCRIPT tag').ckeb($tec).'
<input type="button" value="x" onclick="doRemoveTag();" title="Remove next tag">
<span id="editor'.$tec.'_count"></span>
<textarea id="editor'.$tec.'" cols="120" name="'.$n.'" rows="22" style="font-size:120%;" onfocus="onTeFocus();" onkeyup="showCharCount(this);" onkeydown="editor_onKey(this,event);">'.
str_replace($ta_ctag,$ta_fctag,$tx).$ta_ctag;

}

function make_tag_button($n,$t1,$t2){
return '<input type="button" value="'.$n.'" onclick="insert_tag('.$t1.','.$t2.');">';
}

function make_insert_button($n,$t1){
return '<input type="button" value="'.$n.'" onclick="insert_text(\''.$t1.'\');">';
}

function make_insert_2_button($n,$t1,$t2,$tt){
return '<input type="button" value="'.$n.'" onclick="insert_2_texts('.$t1.','.$t2.');"  title="'.$tt.'">';
}

// HTML код за показване на бутон за включване на CKEditor
function ckeb($n){
global $page_header, $ckpth;
// Път до основния файл на CKEditor
$ckep = $_SERVER['DOCUMENT_ROOT'].$ckpth.'ckeditor.js';
// Проверка дали CKEditor съществува
if (file_exists($ckep)) $page_header .= '<script src="'.$ckpth."ckeditor.js\"></script>\n";
else $page_header .= "<script src=\"//cdn.ckeditor.com/4.5.7/full/ckeditor.js\"></script>\n";
return '
<input type="button" onclick="CKEDITOR.replace( \'editor'.$n.'\' );" value="CKEditor" title="Load CKEditor">';
}

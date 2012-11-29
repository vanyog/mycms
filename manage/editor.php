<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include("ta_ctag.php");

function editor($n,$tx){
global $ta_ctag, $ta_fctag;
return '
<script type="text/javascript"><!--
function doInsertTag(){
var t = prompt("Enter a html tag to be inserted");
insert_tag(t,t);
}
function insert_tag(t1,t2){
var te = document.forms.edit_form.'.$n.';
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
var te = document.forms.edit_form.'.$n.';
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
var te = document.forms.edit_form.'.$n.';
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
var tag_a1 = "a href=\"index.php?pid=\"";
var tag_a2 ="a";
var tag_s1 = "<script type=\"text/javascript\"><!--\n";
var tag_s2 = "\n--><"+"/script>";
--></script>
'.

'<input type="button" value="tag" onclick="doInsertTag();">'.
make_tag_button('a','tag_a1','tag_a2').'
'.make_insert_button('php','<?php\n// Copyright: Vanyo Georgiev info@vanyog.com\n\n?>\n').'
'.make_insert_2_button('case','\'case \\\'\'','\'\\\': break;\'').'
'.make_insert_2_button('include','\'include(\\\'\'','\'\\\');\'').'
'.make_insert_2_button('include_once','\'include_once(\\\'\'','\'\\\');\'').'
'.make_insert_2_button('print_r','\'print_r($\'','\'); die;\'').'
'.make_insert_2_button('<!--$$_','\'<!--$$_\'','\'_$$-->\'').'
'.make_insert_2_button('javascript','tag_s1','tag_s2').'
<textarea class="ckeditor" cols="120" name="'.$n.'" rows="22" style="font-size:120%;">'.
str_replace($ta_ctag,$ta_fctag,$tx).$ta_ctag;

}

function make_tag_button($n,$t1,$t2){
return '<input type="button" value="'.$n.'" onclick="insert_tag('.$t1.','.$t2.');">';
}

function make_insert_button($n,$t1){
return '<input type="button" value="'.$n.'" onclick="insert_text(\''.$t1.'\');">';
}

function make_insert_2_button($n,$t1,$t2){
return '<input type="button" value="'.$n.'" onclick="insert_2_texts('.$t1.','.$t2.');">';
}



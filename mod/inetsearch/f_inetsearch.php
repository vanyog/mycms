<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// Показване на форма за търсене в различни търсачки

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_encode.php');

function inetsearch(){
global $page_header;
$page_header = '<script type="text/javascript"><!--
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}
document.onkeypress = stopRKey;
function searchBy(a, b){
var l = document.getElementById("selink");
var w = document.forms.inetsearch.words.value;
var q = encodeURIComponent(b+w+b)
l.href = a+encodeURIComponent(b+w+b);
if (w){ 
  l.text = w;
  window.open(l.href);
}
}
function words_enter_pressed(e){
if (e.keyCode == 13) searchBy("https://google.bg/search?q=", "");
return false;
}
--></script>';
$rz = '<p><a href="" id="selink" target="_blank">'.encode('Вижте резултата').'</a></p>'."\n";

if (in_edit_mode()){ // Бутон за добавяне в Интернет връзки
$sc = 'http';
if (isset($_SERVER['REQUEST_SCHEME'])) $sc = $_SERVER['REQUEST_SCHEME'];
$rz .= '<script type="text/javascript"><!--
function sendLink(){
var f = document.forms.send_link_form;
var i = f.up.value;
if(!i){
  alert("'.encode('Не сте въвели номер на раздел').'");
  return;
}
var l = document.getElementById("selink");
if(l.href=="'.$sc.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'"){
  alert("'.encode('Не сте извършили търсене').'");
  return;
}
f.link.value = l.href;
f.title.value = l.text;
f.action = "/index.php?lid="+i+"&pid=6";
f.submit();
}
--></script>
<p><form method="POST" name="send_link_form" action="aaa">
<input type="hidden" name="action" value="update">
<input type="hidden" name="link">
<input type="hidden" name="place">
'.encode('Раздел:').' <input type="text" name="up" size="5" value="'.(isset($_COOKIE['lid']) ? $_COOKIE['lid'] : '').'">
'.encode('Служебен:').' <input type="text" name="private" size="1">
<input type="hidden" name="title">
<input type="hidden" name="comment">
<input type="button" value="'.encode('Добавяне в Интернет връзки').'" onclick="sendLink();"></p>
</form>'."\n";
} // if (in_edit_mode())

$f = new HTMLForm('inetsearch');
$i = new FormInput(encode('Ключови думи:'), 'words', 'text');
$i -> size = 100;
$i -> js = 'onkeypress="words_enter_pressed(event);"';
$f -> add_input( $i );
$i = new FormInput(encode('Търсене в:'), '', 'button', 'google.bg');
$i -> js = 'onclick="searchBy(\'https://google.bg/search?q=\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'scholar.google.bg');
$i -> js = 'onclick="searchBy(\'https://scholar.google.bg/scholar?q=\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'bg.wikipedia.org');
$i -> js = 'onclick="searchBy(\'https://bg.wikipedia.org/wiki/\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'en.wikipedia.org');
$i -> js = 'onclick="searchBy(\'https://en.wikipedia.org/wiki/\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'academic.microsoft.com');
$i -> js = 'onclick="searchBy(\'https://academic.microsoft.com/#/search?iq=\', \'@\');"';
$f -> add_input( $i );
return $rz.$f->html();
}

?>

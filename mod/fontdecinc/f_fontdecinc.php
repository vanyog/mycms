<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Функцията връща HTML код за показване на A+ - A- бутони за увелизаване - намаляване на шрифта на страниците на сайта.
// Текущата големина на буквите се запомня с бисквитки, валидни до затваряне на браузъра.

function fontdecinc(){
global $page_header, $body_adds;
$page_header .= '<script>
function font_inc(){
s = document.body.style.fontSize;
if (!s.length){
  document.body.style.fontSize = "16px";
  s = document.body.style.fontSize;
}
l = s.length;
s = s.substr(0,l-2);
s = Math.round(1.1*s)+"px";
document.body.style.fontSize = s;
document.cookie = "fontSize="+s+";";
}
function font_dec(){
s = document.body.style.fontSize;
if (!s.length){
  document.body.style.fontSize = "16px";
  s = document.body.style.fontSize;
}
l = s.length;
s = s.substr(0,l-2);
s = Math.round(s/1.1)+"px";
document.body.style.fontSize = s;
document.cookie = "fontSize="+s+";";
}
function set_font_size(){
c = document.cookie;
i = c.indexOf("fontSize=");
if (i>-1){
  j = c.indexOf(";",i);
  s = c.substring(i+9,j);
}
else{
 s = "16px";
 document.cookie = "fontSize=16px;";
}
document.body.style.fontSize = s;
}
</script>';
//$body_adds = preg_replace('/onload="(.*?)"/', 'aaaa', $body_adds); die($body_adds);
//$body_adds .= ' onload="set_font_size();"';
return '<a href="" onclick="font_inc();return false;">A+</a> <span class="smaller"><a href="" onclick="font_dec();return false;">A-</a></span>';
}

?>

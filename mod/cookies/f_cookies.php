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

// Показва съобщение относно "бисквитките" или таблица с "бисквитките" от сайта.
// При $a=='message' се показва съобщение иначе се показва таблица.
// При показване на таблица, ако $_GET['clear']=='all', се изтриват всички "бисквитки".

function cookies($a=''){
global $page_header;
if (isset($_GET['clear'])&&($_GET['clear']=='all')) return cookies_clear_all();
if ($a=='message') return cookies_message();
$rz = '';
if (!count($_COOKIE)) return '<p>'.translate('cookies_nocookie').'</p>';
$rz .= '<p>'.translate('cookies_table').'</p>
<table class="staff_table">'."\n".
'<th>'.translate('cookies_name').'</th>'."\n".
'<th>'.translate('cookies_value').'</th>'."\n".
'<th>'.translate('cookies_description').'</th>'."\n";
foreach($_COOKIE as $k=>$v){
  $rz .= "<tr><td>$k</td><td>$v</td><td>".translate('cookies_'.$k.'_description')."</td></tr>\n";
}
$rz .= '</table>
<p><a href="'.set_self_query_var('clear','all').'">'.translate('cookies_clear').'</a></p>
';
return $rz;
}

// Показване на съобщение

function cookies_message(){
global $cookies_msg, $page_header;
$page_header .= '<script type="text/javascript"><!--
function cookies_accept(){
var d = new Date();
d = new Date(d.valueOf()+30*24*3600*1000);
document.cookie = "cookies_accept=Yes;expires="+d.toGMTString();
}
--></script>';
return '<script type="text/javascript"><!--
if (document.cookie && (document.cookie.indexOf("cookies_accept=Yes")<0)){
document.write("<div id=\"cookies_message\">");
'.to_javascipt_write(translate('cookies_message')).'
document.write("</div>");
}
--></script>';
}

function to_javascipt_write($a){
$aa = explode("\n",$a);
$rz = '';
foreach($aa as $l){
  $rz .= 'document.write("'.addslashes(trim($l)).'");';
}
return $rz;
}

// Изтриване на всички бисквитки

function cookies_clear_all(){
if (isset($_SERVER['HTTP_REFERER'])){
   foreach($_COOKIE as $n=>$v){
     setcookie($n,'',time()-60*60*24,'/');
   }
   header('Location: '.$_SERVER['HTTP_REFERER']);
   die;
}
}

?>

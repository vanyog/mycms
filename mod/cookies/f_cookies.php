<?php
/*
VanyoG CMS - a simple Content Management System
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

// ������� ��������� ������� "�����������" ��� ������� � "�����������" �� �����.
// ��� $a=='message' �� ������� ��������� ����� �� ������� �������.
// ��� ��������� �� �������, ��� $_GET['clear']=='all', �� �������� ������ "���������".

function cookies($a=''){
global $page_header;
if ($a=='message') return cookies_message();
$rz = '';
if (!count($_COOKIE)) return '<p>'.translate('cookies_nocookie').'</p>';
$rz .= '<p>'.translate('cookies_table').'</p>
<table class="staff_table">'."\n".
'<th>'.translate('cookies_name').'</th>'."\n".
'<th>'.translate('cookies_value').'</th>'."\n".
'<th>'.translate('cookies_description').'</th>'."\n";
foreach($_COOKIE as $k=>$v){
  $rz .= "<tr><td>$k</td><td>";
  if($k=='PHPSESSID') $rz .= '********';
  else $rz .= "$v";
  $rz .= "</td><td>".translate('cookies_'.$k.'_description')."</td></tr>\n";
}
$rz .= '</table>
';//.print_r(session_get_cookie_params(),true);
return $rz;
}

// ��������� �� ���������

function cookies_message(){
global $cookies_msg, $page_header;
$page_header .= '<script>
function cookies_accept(){
var d = new Date();
d = new Date(d.valueOf()+30*24*3600*1000);
document.cookie = "cookies_accept=yes;expires="+d.toGMTString()+"path=/;SameSite=Strict";
document.location.reload(true);
}
</script>';
if(!isset($GLOBALS['need_cookie'])) $GLOBALS['need_cookie'] = 'document.cookie';
return '<script>
if ('.$GLOBALS['need_cookie'].' && (document.cookie.indexOf("cookies_accept=yes")<0)){
var cm = document.createElement("div");
cm.id = "cookies_message";
cm.innerHTML = '.for_javascript(translate('cookies_message')).';
document.body.appendChild(cm);
}
</script>';
}

function for_javascript($a){
$aa = explode("\n",$a);
$rz = '';
foreach($aa as $i=>$l){
  $rz .= '"'.addslashes(trim($l)).'"';
  if( $i < (count($aa)-1) ) $rz .= " + \n";
}
return $rz;
}

function to_javascipt_write($a){
$aa = explode("\n",$a);
$rz = '';
foreach($aa as $l){
  $rz .= 'document.write("'.addslashes(trim($l)).'");';
}
return $rz;
}

?>
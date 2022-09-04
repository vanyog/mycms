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

// ��������� �� ������������ �� ������� �� ������ �����

$exe_time = microtime(true);

include('conf_manage.php');
include_once($idir.'conf_paths.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_tables.php');
include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_field_names.php');
include_once($idir.'lib/f_set_query_var.php');

$t = $_GET['t']; // ��� �� ���������

// ����������� �� ���� �� ��������� �� ��� ��� ����� ����� ������ � ���������� �� �������� �� ���������
$cls = 0;
if (isset($_COOKIE['cols'])) $cls = $_COOKIE['cols'];
if (isset($_POST['cols'])){ $cls = $_POST['cols']; setcookie('cols',$cls,time()+60*60*24*30,'/'); }

// ����������� �� ���� �� �������� ��� ����� ����� �������� � �������� �� ���������
$rws = 0;
if (isset($_COOKIE['rows'])) $rws = $_COOKIE['rows'];
if (isset($_POST['rows'])){ $rws = $_POST['rows']; setcookie('rows',$rws,time()+60*60*24*30,'/'); }

// ����� �� ���� �� ����������� ������ �� ���������
$l = 5;
if (isset($_GET['limit']))       { $l = $_GET['limit']; setcookie('limit',$l,time()+60*60*24*30,'/'); }
else if (isset($_COOKIE['limit'])) $l = $_COOKIE['limit'];

$f = db_field_names($t); // ����� �� ��������

$or = ''; // ������ �� SQL ��������, ����� �������� ���� �� ����������
$so = '-'; // �� ������������ - ���������� � ��������� ���
$sbf = 'ID'; // �� ������������ - ���������� �� ������ `ID`
$sos = array('-' => 'DESC', '+' => 'ASC');
$soo = array('-' => '+', '+' => '-');
if (isset($_GET['sortby'])) {
  $sb = $_GET['sortby'];
  $so = $sb[0];
  $sbf = substr($sb,1);
}
$or = 'ORDER BY `'.$sbf.'` '.$sos[$so];
if (!in_array($sbf,$f)) $or = '';

$wh = set_filter(); // ������������ ����������� �� ����������
$not_found = false;

// ������ �� ����� �� ���������
$r = db_select_m('*',$t,"$wh $or LIMIT 0, $l");
// ��� ���� ������ ���������� �� �������
if (!count($r)) { // ���� ������ ��� ����������� 
  $wh = '1';
  $not_found = true;
  $r = db_select_m('*',$t,"$wh $or LIMIT 0, $l");
}

// ���� �� ������ ������
$c = db_table_field('COUNT(*)',$t,1);

// JavaScript �������
$page_header = '<script>
function doDelete(id){
if (confirm("Delete record ID=\'"+id+"\'?")){
document.location="delete_record.php?t='.$t.'&r="+id;
}
}
function doDublicate(id){
if (confirm("Do you want to duplicate record ID=\'"+id+"\'?")){
document.location="duplicate_record.php?t='.$t.'&r="+id;
}
}
function doOpen(id){
document.location="'.$pth.'index.php?pid="+id;
}
function ChangeLimit(){
var l = document.getElementById("limit");
document.location = document.location + "&limit=" + l.value;
}
function keyPressed(e){
if(e.keyCode==13) ChangeLimit();
}
</script>';

// ���������� �� ����������

$page_content = "<p>Database: <strong>$database</strong> Table: <strong>$tn_prefix$t</strong> $c records \n";

$page_content .= '<input type="text" size="4" value="'.$l.'" id="limit" onkeyup="keyPressed(event);"> viewed
<input type="button" value="Change" onclick="ChangeLimit(event);"></p>'."\n";

$page_content .= '<p>Other tables: ';
$tbs = db_tables();
foreach($tbs as $tb) if (substr($tb,0,strlen($tn_prefix))==$tn_prefix){
 $tb0 = substr($tb,strlen($tn_prefix));
 $page_content .= '<a href="show_table.php?t='.$tb0.'">'.$tb0.'</a>, ';
}
$b1020 = ''; // html ���� �� ������ "10,20..."
if ($c){ // ��� �� ��������� ������ �� ���������

$k = array_keys($r[0]);

// ����� �� ���������� � ����� �� ��������� �� ������ ��������
$page_content .= '</p>

<div style="float:left;">
<form method="POST">
<p><select name="field">';
foreach($k as $ky){
$page_content .= '<option value="'.$ky.'">'.$ky."\n";
}
$page_content .= '</select> LIKE 
<input type="text" name="value"> 
<input type="submit" value="Filter"></p>
</form>
</div>

<div style="float:left; margin-left:50px;">
<form method="POST">
<p>Columns: <input type="text" name="cols" value="'.$cls.'" size="3">  
rows: <input type="text" name="rows" value="'.$rws.'" size="3"> 
<input type="submit" value="limit"></p>
</form>
</div>

<div style="clear:both;"></div>';

if($not_found) $page_content .= "<p>No records found.</p>\n";
else $page_content .= "<p>".count($r)." records found.</p>\n";

// ������ �� ���������
$page_content .= '<table border="1" cellspacing="0"><tr>
';

// ����� ��� � ������� �� ��������
foreach($k as $ky) $page_content .= '<th><a href="'.
  $_SERVER['SCRIPT_NAME'].'?'.set_query_var('sortby',$soo[$so].$ky)."\">$ky</a></th>\n";


foreach($r as $rc){
 $page_content .= '<tr valign="top">';
 foreach($rc as $ky => $v){
   if ($ky=='place') 
     $b1020 = "\n".'<input type="button" value="10 20 ..." onclick="document.location=\''.$adm_pth.'places10.php?t='.$t.'\';">';
   if ($ky=='ID'){
     $v1 = '<a href="edit_record.php?t='.$t.'&r='.$v.'">'.$v.'</a> '.
           '<input type="button" value="x" onclick="doDelete('.$v.');"> '.
           '<input type="button" value="2" onclick="doDublicate('.$v.');"> ';
     if ($t=='pages') $v1 .= '<input type="button" value="->" onclick="doOpen('.$v.');"> ';
     $v = $v1;
   }
   else $v = cut_lines_to( htmlspecialchars(stripslashes(isset($v)?$v:''), ENT_COMPAT, 'cp1251'), $cls, $rws);
   $page_content .= "<td><code><pre>$v</pre></code></td>\n";
 }
 $page_content .= '</tr>';
}
$page_content .= '</tr></table>';

} // ���� �� if ($c)

$page_content .= '<p><input type="button" value="New record" onclick="document.location=\''.$adm_pth.'new_record.php?t='.$t.'\';">'.$b1020.'</p>';

include('build_page.php');

function set_filter(){
if (isset($_POST['value']) && strlen($_POST['value'])){
return '`'.$_POST['field']."` LIKE '".$_POST['value']."'";
}
return '1';
}

function cut_lines_to($v, $c, $r){
if ($c==0) return $v;
$va = explode("\n",$v);
$vc = count($va);
foreach($va as $k=>$l){
  if (strlen($l)>$c+3) $va[$k] = mb_substr($l,0,$c).'...';
  if ($r && ($k>($r-1))) {
	  $va = array_slice( $va, 0, $r, true);
	  if($r<$vc) $va[] = '...'; 
	  //echo "$k $r $c "; print_r($va); echo "<br>";// die;
	  break;
  }
} 
return implode("\n",$va);
}
?>

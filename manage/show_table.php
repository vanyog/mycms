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

// Показване на съдържанието на таблица от базата данни

include('conf_manage.php');
include_once($idir.'conf_paths.php'); 
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_tables.php');
include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_db_field_names.php');
include_once($idir.'lib/f_set_query_var.php');

$t = $_GET['t']; // Име на таблицата

// Лимит за броя на показваните редове
$l = 5;
if (isset($_GET['limit']))       { $l = $_GET['limit']; setcookie('limit',$l,time()+60*60*24*30,'/'); }
else if (isset($_COOKIE['limit'])) $l = $_COOKIE['limit'];

$f = db_field_names($t); // Имена на полетата

$or = ''; // Частта от SQL заявката, която определя реда на подреждане
$so = '-'; // По подразбиране - подреждане в намаляващ ред
$sbf = 'ID'; // По подразбиране - подреждане по полето `ID`
$sos = array('-' => 'DESC', '+' => 'ASC');
$soo = array('-' => '+', '+' => '-');
if (isset($_GET['sortby'])) {
  $sb = $_GET['sortby'];
  $so = $sb[0];
  $sbf = substr($sb,1);
}
$or = 'ORDER BY `'.$sbf.'` '.$sos[$so];
if (!in_array($sbf,$f)) $or = '';

$wh = set_filter();

// Четене на данни от теблицата
$r = db_select_m('*',$t,"$wh $or LIMIT 0, $l");
if (!count($r)) {
  $wh = '1';
  $r = db_select_m('*',$t,"$wh $or LIMIT 0, $l");
}

// Брой на всички записи
$c = db_table_field('COUNT(*)',$t,1);

// JavaScript функции
$page_header = '<script type="text/javascript"><!--
function doDelete(id){
if (confirm("Delete record ID=\'"+id+"\'?")){
document.location="delete_record.php?t='.$t.'&r="+id;
}
}
function doOpen(id){
document.location="'.$pth.'index.php?pid="+id;
}
function ChangeLimit(){
var l = document.getElementById("limit");
document.location = document.location + "&limit=" + l.value;
}
--></script>';

// Сглобяване на страницата

$page_content = "<p>Database: <strong>$database</strong> Table: <strong>$tn_prefix$t</strong> $c records \n";

$page_content .= '<input type="text" size="4" value="'.$l.'" id="limit"> viewed 
<input type="button" value="Change" onclick="ChangeLimit();"></p>'."\n";

$page_content .= '<p>Other tables: ';
$tbs = db_tables();
foreach($tbs as $tb) if (substr($tb,0,strlen($tn_prefix))==$tn_prefix){
 $tb0 = substr($tb,strlen($tn_prefix));
 $page_content .= '<a href="show_table.php?t='.$tb0.'">'.$tb0.'</a>, ';
}
$b1020 = ''; // html кода за бутона "10,20..."
if ($c){ // Ако са прочетени записи от таблицата

$k = array_keys($r[0]);

$page_content .= '</p>
<form method="POST">
<p><select name="field">';
foreach($k as $ky){
$page_content .= '<option value="'.$ky.'">'.$ky."\n";
}
$page_content .= '</select> LIKE 
<input type="text" name="value"> 
<input type="submit" value="Filter"></p>
</form>';

$page_content .= '<table border="1" cellspacing="0"><tr>
';

foreach($k as $ky) $page_content .= '<th><a href="'.
  $_SERVER['PHP_SELF'].'?'.set_query_var('sortby',$soo[$so].$ky)."\">$ky</a></th>\n"; 

foreach($r as $rc){
 $page_content .= '<tr valign="top">';
 foreach($rc as $ky => $v){
   if ($ky=='place') 
     $b1020 = "\n".'<input type="button" value="10 20 ..." onclick="document.location=\''.$adm_pth.'places10.php?t='.$t.'\';">';
   if ($ky=='ID'){
     $v1 = '<a href="edit_record.php?t='.$t.'&r='.$v.'">'.$v.'</a> '.
               '<input type="button" value="x" onclick="doDelete('.$v.');"> ';
     if ($t=='pages') $v1 .= '<input type="button" value="->" onclick="doOpen('.$v.');"> ';
     $v = $v1;
   }
   else $v = htmlspecialchars(stripslashes($v), ENT_COMPAT, 'cp1251');
   $page_content .= "<td><code><pre>$v</pre></code></td>\n";
 }
 $page_content .= '</tr>';
}
$page_content .= '</tr></table>';

} // край на if ($c)

$page_content .= '<p><input type="button" value="New record" onclick="document.location=\''.$adm_pth.'new_record.php?t='.$t.'\';">'.$b1020.'</p>';

include('build_page.php');

function set_filter(){
if (isset($_POST['value']) && $_POST['value']){
return '`'.$_POST['field']."` LIKE '".$_POST['value']."'";
}
return '1';
}

?>

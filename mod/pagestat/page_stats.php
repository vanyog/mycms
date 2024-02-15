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

// Показва натрупаната в таблица visit_hostory статистика за посещаването на страниците.
// Ако има параметър $_GET['days'] показва статистиката от последните, зададени с този параметър брой дни.
// $_GET['pid'] - статистиката по дати на страницата с този номер
// $_GET['date'] - статистиката за определена дата
// $_GET['group'] - статистика на страниците от раздел


error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_table_field.php');
include($idir.'lib/f_db_delete_where.php');
include($idir.'lib/f_encode.php');
include($idir.'lib/f_unset_self_query_var.php');
include($idir.'lib/translation.php');

if(isset($_GET['del'])) delete_record();

$d = 0; // Брой на последните дни, за които се показва статистика
// Стойност 0 означава цялата статистика за всички дни
if (isset($_GET['days'])) $d = 1*$_GET['days'];

$d2 = db_table_field('MAX(`date`)', 'visit_history', '1');

$w = '1';
if ($d) $w = "`date`>'".date('Y-m-d', strtotime($d2)-$d*60*60*24)."'";
if (isset($_GET['pid']) && is_numeric($_GET['pid'])){
  $pid = 1*$_GET['pid'];
  $w .= " AND `page_id`=$pid";
}

$d1 = db_table_field('MIN(`date`)', 'visit_history', $w);

// Сглобяване на страницата
$page_title = encode('Статистика за посещението на страниците');

$page_content = encode("<p>От: $d1 до: $d2</p>\n");

if (isset($_GET['pid'])){
//  $pid = 1*$_GET['pid'];
  if($_GET['pid']=='all'){
     $page_content .= total_pages($w); }
  else 
     $page_content .= one_page($pid, $w);
}
else if (isset($_GET['date'])){
  $page_content = one_day($_GET['date']);
}
else if (isset($_GET['group'])){
  $page_content = page_group();
} else $page_content .= all_pages($w);

include($idir.'lib/build_page.php');

//
// Показва таблица с всички страници

function all_pages($w){
global $pth;
// Четене на сумите на посещенията по страници
$da = db_select_m('`page_id`, sum(`count`)', 'visit_history', "$w GROUP BY `page_id`");

$dt = array();

// Съставяне на нов масив с индекси номерата на страниците и стойности - сумите на посещенията им
foreach($da as $d){ $dt[$d['page_id']] = $d['sum(`count`)']; }

// Подреждане на масива по намаляване на броя посещения
arsort($dt);

$page_content = '<h1>Page statistics</h1>
<p>See: 
<a href="page_stats.php?pid=all">Site totals</a></p>
<table style="border-bottom:solid 1px black;">
<tr><th>'.encode('Посещения').'</th><th></th><th>ID</th><th>'.encode('Страница').'</th></tr>';

$t = 0;
foreach($dt as $i=>$c){
  // Дани за страница с номер $i
  $ptn = db_select_1('menu_group,title','pages','`ID`='.$i);
  if($ptn){
  // Текст на заглавието на страницата
  $pt = translate($ptn['title']);
  $page_content .= "<tr>
<td align=\"right\"> <a href=".set_self_query_var('pid',$i).">$c</a> </td>
<td> <a href=".set_self_query_var('group', $ptn['menu_group']).">g</a> </td>
<td align=\"center\"> <a href=\"$pth"."index.php?pid=$i\" target=\"_blank\">$i</a>
</td><td>$pt</td>
</tr>\n";}
  $t += $c;
}

$page_content .= "</table>
$t ".encode("Общо\n");

return $page_content;
}

//
// Показва таблица за една страница

function one_page($i, $w){
global $language, $main_index;
// Четене на записите за страница с номер $i
$da = db_select_m('*', 'visit_history', "`page_id`=$i AND $w ORDER BY `date` DESC");
$pd = db_select_1('*', 'pages', "`ID`=$i");
// Добавяне на днешна дата
$d = array( 'date'=>date("Y-m-d", time() + 24*3600), 'count'=>$pd['dcount'] );
array_unshift( $da, $d );
//die(print_r($da, true));
$min = db_table_field('MIN(`count`)', 'visit_history', "`page_id`=$i AND $w");
$max = db_table_field('MAX(`count`)', 'visit_history', "`page_id`=$i AND $w");
$ave = db_table_field('AVG(`count`)', 'visit_history', "`page_id`=$i AND $w");
if ($max<$da[0]['count']) $max = $da[0]['count'];
if (!$max) $max = 1;
//die("$min $max");
$m = 800;
$tn = $pd['title'];
$tn = db_table_field('text', 'content', "`name`='$tn' AND `language`='$language'");
$rz = "<h1>Page Statistics</h1>
<p>Page: <a href=\"$main_index?pid=$i\">$tn</a>, Group <a href=\"?group=".$pd['menu_group']."\">".$pd['menu_group']."</a></p>".'
<p>See: 
<a href="page_stats.php">All pages statistics</a> &nbsp; 
<a href="page_stats.php?pid=all">Site totals</a></p>
'."Minimum visit count: $min, average: ".number_format(floatval($ave), 1).", Maximum: $max".encode('
<table>
<tr><th>Дата</th><th>Посещения</th></tr>');
foreach($da as $d){
  $a = $d['count']/$max * $m;
  $t = date("N",strtotime($d['date']));
  $rz .= '<tr><td><a href="page_stats.php?date='.$d['date'].'">'.$d['date']."</a> $t".
         '</td><td><div style="display:inline-block; background-color:red;width:'.$a.'px;">'.
         $d['count'].'</div>';
  $dv =  $ave ? intval($d['count'])/intval($ave) : 0;
  if($dv>10 && isset($d['ID'])) $rz .= ' <a href="'.set_self_query_var('del',$d['ID']).'">x</a>';    
  $rz .= '</td>';
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}

// 
// Показване на таблица за една дата

function one_day($d){
$rz = '<h1>Day Statistics</h1>
<p>See: 
<a href="page_stats.php">All pages statistics</a> &nbsp; 
<a href="page_stats.php?pid=all">Site totals</a></p>
'."<p>Visit statistics for: $d</p>";
// Четене на данните за дата $d
$dt = db_select_m('*', 'visit_history', "`date`='$d' ORDER BY `count` DESC");
$rz .= '<table>
<tr><th>Visits</th><th>Page</th></tr>';
foreach($dt as $t){
  $rz .= "\n<tr><td>".$t['count'].'</td>'.
         '<td><a href="'.$_SERVER['PHP_SELF'].'?pid='.$t['page_id'].'">'.$t['page_id']."</a></td></tr>";
}
$rz .= '</table>';
return $rz;
}

//
// Статистика на страниците от група $_GET['group']

function page_group(){
global $main_index;
$gr = 1*$_GET['group'];
$pd = db_select_m('ID', 'pages', "`menu_group`=$gr");
$q = '';
foreach($pd as $p){
  if($q) $q .= ' OR ';
  $q .= "`page_id`=".$p['ID'];
}
$vh = db_select_m('SUM(`count`),page_id', 'visit_history', "$q GROUP BY `page_id` ORDER BY SUM(`count`) DESC");
$rz = '<h1>'.encode('Статистика на страниците от група ').$gr."</h1>\n".
      "<table>\n";
foreach($vh as $d)
   $rz .= '<tr><td style="text-align:right;">'.$d['SUM(`count`)'].
          "</td><td><a href=\"$main_index?pid=".$d['page_id'].'">'.$d['page_id'].'</a> '.
          '</td><td>'.translate( db_table_field('title', 'pages', "`ID`=".$d['page_id']) ).
          "</td></tr>\n";
$rz .= "</table>\n";
return $rz;
}

// Показва статистика за сумата на посещенията на всички страници по дати

function total_pages($w){
// Четене на сумите за всички страници
$da = db_select_m('date,SUM(`count`)', 'visit_history', "$w GROUP BY `date` ORDER BY `date` DESC");
// Добавяне на днешна дата
$d = array( 'date'=>date("Y-m-d", time() + 24*3600), 'SUM(`count`)'=>db_table_field('SUM(`dcount`)', 'pages', 1) );
array_unshift( $da, $d );
$min = 100000000;
$max = 0;
$ave = 0;
foreach($da as $d){
  if($min>$d['SUM(`count`)']) $min = $d['SUM(`count`)'];
  if($max<$d['SUM(`count`)']) $max = $d['SUM(`count`)'];
  $ave += $d['SUM(`count`)'];
}
if ($max<$da[0]['SUM(`count`)']) $max = $da[0]['SUM(`count`)'];
if (!$max) $max = 1;
//die("$min $max");
$m = 800;
$rz = "<h1>Page totals</h1>".'
<p>See: <a href="page_stats.php">All pages statistics</a></p>
'."Minimum visits: $min, average: ".number_format(floatval($ave), 1).", Maximum vizits: $max".encode('
<table>
<tr><th>Дата</th><th>Посещения</th></tr>');
foreach($da as $d){
  $a = $d['SUM(`count`)']/$max * $m;
  $t = date("N",strtotime($d['date']));
  $rz .= '<tr><td><a href="page_stats.php?date='.$d['date'].'">'.$d['date']."</a> $t".
         '</td><td><div style="background-color:red;width:'.$a.'px;">'.$d['SUM(`count`)'].'</div></td>';
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}

// Изтриване на запис с номер $_GET['del']

function delete_record(){
if(!is_numeric($_GET['del'])) return '';
db_delete_where('visit_history', '`ID`='.$_GET['del'], false);
$q = unset_self_query_var('del');
header("Location: $q");
}

?>
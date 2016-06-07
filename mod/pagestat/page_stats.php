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

// Показва натрупаната в таблица visit_hostory статистика за посещаването на страниците.
// Ако има параметър $_GET['days'] показва статистиката от последните, зададени с този параметър брой дни.
// $_GET['pid'] - статистиката по дати на страницата с този номер
// $_GET['date'] - статистиката за определена дата


error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_table_field.php');
include($idir.'lib/f_encode.php');
include($idir.'lib/translation.php');

$d = 0; // Брой на последните дни, за които се показва статистика
// Стойност 0 означава цялата статистика за всички дни
if (isset($_GET['days'])) $d = 1*$_GET['days'];

$d2 = db_table_field('MAX(`date`)', 'visit_history', '1');

$w = '1';
if ($d) $w = "`date`>'".date('Y-m-d', strtotime($d2)-$d*60*60*24)."'";

$d1 = db_table_field('MIN(`date`)', 'visit_history', $w);

// Сглобяване на страницата
$page_title = encode('Статистика за посещението на страниците');

$page_content = encode("<p>От: $d1 до: $d2</p>\n");

if (isset($_GET['pid'])){
  $pid = 1*$_GET['pid'];
  $page_content .= one_page($pid, $w);
}
else if (isset($_GET['date'])){
  $page_content = one_day($_GET['date']);
}
else $page_content .= all_pages($w);

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

$page_content = '<table style="border-bottom:solid 1px black;">
<tr><th>'.encode('Посещения').'</th><th>ID</th><th>'.encode('Страница').'</th></tr>';

$t = 0;
foreach($dt as $i=>$c){
  // Име на заглавието на страница с номер $i
  $ptn = db_table_field('title','pages','`ID`='.$i);
  // Текст на заглавието на страницата
  $pt = translate($ptn);
  $page_content .= "<tr>
<td align=\"right\"><a href=".set_self_query_var('pid',$i).">$c</a></td>
<td align=\"center\"><a href=\"$pth"."index.php?pid=$i\" target=\"_blank\">$i</a>
</td><td>$pt</td>
</tr>\n";
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
// Добавяне на дрешна дата
$d = array( 'date'=>date("Y-m-d", time() + 24*3600), 'count'=>db_table_field('dcount', 'pages', "`ID`=$i") );
array_unshift( $da, $d );
//die(print_r($da, true));
$min = db_table_field('MIN(`count`)', 'visit_history', "`page_id`=$i AND $w");
$max = db_table_field('MAX(`count`)', 'visit_history', "`page_id`=$i AND $w");
if ($max<$da[0]['count']) $max = $da[0]['count'];
if (!$max) $max = 1;
//die("$min $max");
$m = 800;
$tn = db_table_field('title', 'pages', "`ID`=$i");
$tn = db_table_field('text', 'content', "`name`='$tn' AND `language`='$language'");
$rz = "<p>Page: <a href=\"$main_index?pid=$i\">$tn</a></p>".'
<p>See: <a href="page_stats.php">All pages statistics</a></p>
'."Minimum visit count: $min, Maximum: $max".encode('
<table>
<tr><th>Дата</th><th>Посещения</th></tr>');
foreach($da as $d){
  $a = $d['count']/$max * $m;
  $t = date("N",strtotime($d['date']));
  $rz .= '<tr><td><a href="page_stats.php?date='.$d['date'].'">'.$d['date']."</a> $t".
         '</td><td><div style="background-color:red;width:'.$a.'px;">'.$d['count'].'</div></td>';
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}

// 
// Показване на таблица за една дата

function one_day($d){
$rz = "<p>Visit statistics for: $d</p>";
// Четене на данните за дата $d
$dt = db_select_m('*', 'visit_history', "`date`='$d' ORDER BY `count` DESC");
$rz .= '<table>
<tr><th>Visits</th><th>Page</th></tr>';
foreach($dt as $t){
  $rz .= "\n<tr><td>".$t['count']."</td><td>".$t['page_id']."</td></tr>";
}
$rz .= '</table>';
return $rz;
}
?>

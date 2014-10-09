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

// Показва натрупаната в таблица hostory статистика за посещаването на страниците.
// Ако има параметър $_GET['days'] показва статистиката от последните, зададени с този параметър брой дни.


error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_table_field.php');
include($idir.'lib/translation.php');

$d = 0; // Брой на последните дни, за които се показва статистика
// Стойност 0 означава цялата статистика за всички дни
if (isset($_GET['days'])) $d = 1*$_GET['days'];

$d2 = db_table_field('MAX(`date`)', 'visit_history', '1');

$w = '1';
if ($d) $w = "`date`>'".date('Y-m-d', strtotime($d2)-$d*60*60*24)."'";

$d1 = db_table_field('MIN(`date`)', 'visit_history', $w);

// Четене на сумите на посещенията по страници
$da = db_select_m('`page_id`, sum(`count`)', 'visit_history', "$w GROUP BY `page_id`");

$dt = array();

// Съставяне на нов масив с индекси номерата на страниците и стойности - сумите на посещенията им
foreach($da as $d){ $dt[$d['page_id']] = $d['sum(`count`)']; }

// Подреждане на масива по намаляване на броя посещения
arsort($dt);

// Сглобяване на страницата
$page_content = "<p>От: $d1 до: $d2</p>\n".
'<table style="border-bottom:solid 1px black;">
<tr><th>Посещения</th><th>ID</th><th>Страница</th></tr>';

$t = 0;
foreach($dt as $i=>$c){
  // Име на заглавието на страница с номер $i
  $ptn = db_table_field('title','pages','`ID`='.$i);
  // Текст на заглавието на страницата
  $pt = translate($ptn);
  $page_content .= "<tr>
<td align=\"right\">$c</td>
<td align=\"center\"><a href=\"$pth"."index.php?pid=$i\" target=\"_blank\">$i</a>
</td><td>$pt</td>
</tr>\n";
  $t += $c;
}

$page_content .= "</table>
$t Общо\n";

$page_title = 'Статистика за посещението на страниците';

include($idir.'lib/build_page.php');
?>
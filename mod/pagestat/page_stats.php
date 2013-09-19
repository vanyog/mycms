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

error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_table_field.php');
include($idir.'lib/translation.php');

$d = 0;
if (isset($_GET['days'])) $d = 1*$_GET['days'];

$w = '1';
if ($d) $w = "`date`>'".date('Y-m-d', time()-$d*60*60*24)."'";

$da = db_select_m('`page_id`, sum(`count`)', 'visit_history', "$w GROUP BY `page_id`");

$dt = array();

foreach($da as $d){ $dt[$d['page_id']] = $d['sum(`count`)']; }

asort($dt);

$page_content = '<table style="border-bottom:solid 1px black;">';

$t = 0;
foreach($dt as $i=>$c){
  $ptn = db_table_field('title','pages','`ID`='.$i);
  $pt = translate($ptn);
  $page_content .= "<tr><td align=\"right\">$c</td><td>$pt</td></tr>\n";
  $t += $c;
}

$page_content .= "</table>
$t Общо\n";

$page_title = 'Статистика за посещението на страниците';

include($idir.'lib/build_page.php');
?>
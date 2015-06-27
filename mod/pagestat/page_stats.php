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

// ������� ����������� � ������� hostory ���������� �� ������������ �� ����������.
// ��� ��� ��������� $_GET['days'] ������� ������������ �� ����������, �������� � ���� ��������� ���� ���.
// $_GET['pid'] - ������������ �� ���� �� ���������� � ���� �����


error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_table_field.php');
include($idir.'lib/translation.php');

$d = 0; // ���� �� ���������� ���, �� ����� �� ������� ����������
// �������� 0 �������� ������ ���������� �� ������ ���
if (isset($_GET['days'])) $d = 1*$_GET['days'];

$d2 = db_table_field('MAX(`date`)', 'visit_history', '1');

$w = '1';
if ($d) $w = "`date`>'".date('Y-m-d', strtotime($d2)-$d*60*60*24)."'";

$d1 = db_table_field('MIN(`date`)', 'visit_history', $w);

// ���������� �� ����������
$page_title = '���������� �� ����������� �� ����������';

$page_content = "<p>��: $d1 ��: $d2</p>\n";

if (isset($_GET['pid'])){
  $pid = 1*$_GET['pid'];
  $page_content .= one_page($pid, $w);
}
else $page_content .= all_pages($w);

include($idir.'lib/build_page.php');

//
// ������� ������� � ������ ��������

function all_pages($w){
global $pth;
// ������ �� ������ �� ����������� �� ��������
$da = db_select_m('`page_id`, sum(`count`)', 'visit_history', "$w GROUP BY `page_id`");

$dt = array();

// ��������� �� ��� ����� � ������� �������� �� ���������� � ��������� - ������ �� ����������� ��
foreach($da as $d){ $dt[$d['page_id']] = $d['sum(`count`)']; }

// ���������� �� ������ �� ���������� �� ���� ���������
arsort($dt);

$page_content = '<table style="border-bottom:solid 1px black;">
<tr><th>���������</th><th>ID</th><th>��������</th></tr>';

$t = 0;
foreach($dt as $i=>$c){
  // ��� �� ���������� �� �������� � ����� $i
  $ptn = db_table_field('title','pages','`ID`='.$i);
  // ����� �� ���������� �� ����������
  $pt = translate($ptn);
  $page_content .= "<tr>
<td align=\"right\"><a href=".set_self_query_var('pid',$i).">$c</a></td>
<td align=\"center\"><a href=\"$pth"."index.php?pid=$i\" target=\"_blank\">$i</a>
</td><td>$pt</td>
</tr>\n";
  $t += $c;
}

$page_content .= "</table>
$t ����\n";

return $page_content;
}

//
// ������� ������� �� ���� ��������

function one_page($i, $w){
// ������ �� �������� �� �������� � ����� $i
$da = db_select_m('*', 'visit_history', "`page_id`=$i AND $w ORDER BY `date` DESC");
$min = db_table_field('MIN(`count`)', 'visit_history', "`page_id`=$i AND $w");
$max = db_table_field('MAX(`count`)', 'visit_history', "`page_id`=$i AND $w");
$m = 800;
$rz = "$min $max".'<table>
<tr><th>����</th><th>���������</th></tr>';
foreach($da as $d){
  $a = $d['count']/$max * $m;
  $rz .= '<tr><td>'.$d['date'].
         '</td><td><div style="background-color:red;width:'.$a.'px;">'.$d['count'].'</div></td>';
  $rz .= "</tr>\n";
}
$rz .= '</table>';
return $rz;
}
?>

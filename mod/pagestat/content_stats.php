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

//
// ������� ����������� � ������� content_hostory ���������� �� ���������� �� ����� ���� �� ������������ �� �����.
//

error_reporting(E_ALL); ini_set('display_errors',1);

date_default_timezone_set("Europe/Sofia");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_chart.php');
include_once($idir.'lib/f_db_table_status.php');

$page_content = encode('<h1>���������� �� ����� �� ������������ �� �����</h1>');

// ������ �� ������� �� ������� content_history
$da = db_select_m('MAX(date),size', 'content_history', "1 GROUP BY `size`",false);

// ��������� �� ����� ���� - ����
$dt = array();
foreach($da as $d){
  $dt[$d['MAX(date)']] = $d['size'];
}

// �������� � �� ������� ���
$td = date("Y-m-d", time() + 24*3600);
if (!isset($dt[$td])) $dt[$td] = db_table_status('content', 'Data_length');

// ������ �� ������� �� ������� outer_links
$ld = db_select_m('LEFT(`date_time_1`, 10), COUNT(*)', 'outer_links', "`link`>'' GROUP BY LEFT(`date_time_1`, 10)");

// ��������� �� ����� ���� - ����
$lt = array();
$v = 0;
foreach($ld as $d){
  $k = $d['LEFT(`date_time_1`, 10)'];
  $v += $d['COUNT(*)'];
  $lt[$k] = $v;
}

if(count($dt)) $page_content .= encode("<h2>���������� �� �����</h2>\n").chart($dt);
if(count($lt)) $page_content .= encode("<h2>���� �������� ������</h2>\n").chart($lt);

include($idir.'lib/build_page.php');

?>

<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// ��������� �� �������� ����� $_GET['pid']

if (!isset($_GET['pid'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");
include_once($idir."lib/translation.php");
//include_once($idir."lib/f_parse_content.php");
//include_once($idir."lib/f_db_insert_1.php");
//include_once($idir."lib/f_db_insert_m.php");
//include_once($idir."lib/o_form.php");

// ����� �� ����������
$page_id = 1*$_GET['pid'];

// ����� �� ����������
$page_data = db_select_1('*', 'pages', "`ID`=$page_id");

// ����������� ������� �� �����������
usermenu(true);


// ��� ������������ ���� ����� �� ������� ���������� - ����.
if (!$can_create || !$can_edit) echo die("Your have no permission to delete this page.");

// ����� �� �������� �������� �� �������
$pid = 0;

// ��� � ������ �������� �� ������
$p = db_select_1('*', 'menu_tree', "`index_page`=".$page_data['ID']." AND `group`=".$page_data['menu_group']);// print_r($p); die;
if ($p){
  // ���� �� ���������� �� �������
  $c = db_table_field('COUNT(*)', 'pages', "`menu_group`=".$page_data['menu_group']);
  // ��� ��� � ����� �������� - �� �� �������, ����.
  if ($c>1) die(translate('usermenu_cantdelindex'));
  // ��������� �� ������ �� ������� 'menu_tree'
  $q = "DELETE FROM `$tn_prefix"."menu_tree` WHERE `group`=".$page_data['menu_group'].";";
  mysqli_query($db_link,$q);
  $pid = db_table_field('index_page', 'menu_tree', "`group`=".$p['parent']);
}

// ��������� �� ����������
$q = "DELETE FROM `$tn_prefix"."pages` WHERE `ID`=$page_id;";
//echo "$q<br>";
mysqli_query($db_link,$q);

// ����� �� ������
$m = db_select_1('*', 'menu_items', "`group`=".$page_data['menu_group']." AND `link`=".$page_data['ID']);
// ��������� �� ����������� �� ������ ������, ����� ����� ��� ����������
$q = "DELETE FROM `$tn_prefix"."menu_items` WHERE `link`=".$page_data['ID'].";";
//echo "$q<br>";
mysqli_query($db_link,$q);

// ����� �� ���������
$t = db_select_m('*', 'content', "`name`='".$m['name']."' OR `name`='".$page_data['title']."' OR `name`='".$page_data['content']."'");
// ��������� �� ���������
$q = "DELETE FROM `$tn_prefix"."content` WHERE `name`='".$m['name']."' OR `name`='".$page_data['title']."' OR `name`='".$page_data['content']."';";
//echo "$q<br>";
mysqli_query($db_link,$q);

// ������� ��� �������� �������� �� �������
if (!$pid) $pid = db_table_field('index_page', 'menu_tree', "`group`=".$page_data['menu_group']);
header("Location: $pth"."index.php?pid=$pid");

?>

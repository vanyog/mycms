<?php
/* 
VanyoG CMS - a simple Content Management System
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

// �������� � ����� $_GET['p'] �� ��������� � ����� $_GET['g'].
// �������, � ����� �� ��������� ������ �� ���������� � �� ��� ������ ��������.

if (!isset($_GET['p'])||!isset($_GET['g'])) die("Insufficient parameters");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."lib/f_db_table_field.php");
//include_once($idir."conf_paths.php");
//include_once($idir."lib/f_db_insert_or_1.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/f_page_cache.php");

// ����� �� ����������, ����� �� ���������
$p = 1*$_GET['p'];
$page_id = $p;

// ����������� ������� �� ����������� �� �������� ����������
$tx = usermenu(true);
// ��� ������������ ���� ����� �� �������� ���������� - ����.
if (!$can_create) echo die("Your have no permission to move this page.");

// ����� �� �������� ���������� �� ������� 'pages'
$pd = db_select_1('`ID`,`menu_group`', 'pages', "`ID`=$p");

// ����� �� �������, � ����� �� ���������
$g = 1*$_GET['g'];

// ����� �� �������� �������� �� �������, � ����� �� ���������
$page_id = db_table_field('index_page', 'menu_tree', "`group`=$g");

$newg = false; // ���� � ��������� ���� �����

// ��� �� �� ������ ������ ��������, ������� �� ���������� �
// ���������� �������� ����� ������ �������� �� ���� �����, ����� �� �������.
if (!$page_id) {
  $page_id = $p;
  db_insert_1(array('group'=>$g, 'parent'=>$pd['menu_group'], 'index_page'=>$p), 'menu_tree');
  $newg = true;
}

// ����������� ������� �� ����������� �� ������� �������� � ������ �����
$tx = usermenu(true);
// ��� ������������ ���� ����� �� ������� ���������� � ������ ����� - ����.
if (!$can_create) echo die("Your have no permission to move pages in group $g.");

// ����� �� ������� �� �������� �������� �� ������� 'menu_tree'
$td = db_select_1('`ID`,`parent`,`index_page`', 'menu_tree', "`group`=".$pd['menu_group']);

// ��� ���������� � ������ �� �������, �� ��������� ������ �����
if ($td['index_page']==$p){

  // ����� �� ����� ��� ���������� � ������������ ����
  $ld = db_select_1('`ID`,`group`', 'menu_items', "`group`=".$td['parent']." AND `link`=$p");

  // ��� ��� ����� �� ��������� �����
  if ($ld){
    $ld['group']=$g;
    db_update_record($ld, 'menu_items');
  }

  // ������� �� �������� �� ������� �� ���������� � ������ �����
  unset($td['index_page']);
  $td['parent'] = $g;
  db_update_record($td, 'menu_tree');

}
else { // ��� ���������� �� � ������ �� �������, �� ��������� ���� ����������

  // ����� �� ����� ��� ���������� � �������� ����
  $ld = db_select_1('`ID`,`group`', 'menu_items', "`group`=".$pd['menu_group']." AND `link`=$p");

  // ������ �� ��� ����� �� ����� � ������ �� ����������
  $pd['menu_group'] = $g;
  db_update_record($pd, 'pages');

  // ��� ������� ����� �� � ���� � ��� �� ��������� � �����
  if(!$newg){
     $ld['group'] = $g;
     db_update_record($ld, 'menu_items');
  }

}

// ������� �� ����������, ����� �� ���������
$p = $main_index.'?pid='.$p;
$q = 'http://'.$_SERVER['HTTP_HOST'].$p;
purge_page_cache($q);
header("Location: $p");

?>

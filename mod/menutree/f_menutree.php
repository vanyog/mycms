<?php
/*
VanyoG CMS - a simple Content Management System
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

// ��������� menu_tree() ������� �������� ��� �� �������� �������� ��� �������� ��������.
// ��������� �� �������� �� ������� $tn_prefix.`menu_tree`, ����� �������� ������������ ���� �� ����� ����.
// ����������� menutree_last, �������� ����� ��������� (��������) �� �� ������� �� ���������� �����:
// current - ���������� �� �������� ��������
// index - ���� ��� �������� �������� �� ������� ������ (���� � �� ������������).

include_once($idir.'lib/f_db_select_1.php');

function menutree(){
global $pth, $page_id, $page_data, $main_index;
$rz = '';
// �� �������� �������� �� �� �������
if($page_id==stored_value('main_index_pageid',1)) return $rz;
// ��� ���������� � �� ������� �� �������� ��������, ���� �� �� ������
if($page_data['menu_group']==1) return $rz;
// ������ ������ �� ������ �� ����������
$pr = db_select_1('*','menu_tree',"`group`=".$page_data['menu_group']);
if (!$pr) return $rz;
$what = stored_value('menutree_last','index');
$pg = $page_data;
if ($what == 'index'){
  // ������ ������ �� �������� �������� �� ������
  $pg = db_select_1('*','pages','ID='.$pr['index_page']);
}
// ��� �������� �������� � ������ �� ������� ��� ����
if ($page_id==$pg['ID']) $rz = '<span>'.translate($pg['title']).'</span>';
// ����� �� ������� � ����
else $rz = '<a href="'.$main_index.'?pid='.$pg['ID'].'">'.translate($pg['title']).'</a>'.$rz;
// ��� ������ ��� �������� �� ������� � ��.
$psd = array(0=>$pr['group']);
while ($pr['parent'])
{
  // ����� ������ ���������
  if (in_array($pr['parent'],$psd)) break;
  $psd[] = $pr['parent'];
  $pi = $pr['parent'];
  $pr = db_select_1('*','menu_tree',"`group`=".$pr['parent']);
  if (!$pr) $pg = db_select_1('*','pages',"`menu_group`=$pi");
  else $pg = db_select_1('*','pages','ID='.$pr['index_page']);
  if ($rz) $rz = ' &gt;&gt; '.$rz;
  $rz = '<a href="'.$main_index.'?pid='.$pg['ID'].'">'.translate($pg['title']).'</a>'.$rz;
}
add_style('menu_tree');
return '<div id="menu_tree">
'.translate('menutree_start').$rz.'
</div>';
}

?>
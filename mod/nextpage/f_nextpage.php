<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// ���� ��� ����������, ������ ������� �� ����� ��������

function nextpage(){
global $page_data, $page_id, $main_index;
// ����� �� ����� ��� �������� �������� � ������ �.
$ld = db_select_1('*', 'menu_items', "`group`='".$page_data['menu_group']."' AND `link`='$page_id'");
if (!$ld) return '';
// ����� �� ��������� ���� � ������
$nl = db_select_1('*', 'menu_items', "`group`='".$page_data['menu_group']."' AND `place`>".$ld['place'].
      " ORDER BY `place`");
if (!$nl) $nl = nextpage_from_parent($ld);
if (!$nl) return ''; // die(print_r($ld, true));
// ����� �� ���������� ��������
$pd = db_select_1('*', 'pages', "`ID`=".$nl['link'] );
// �������� �� ����������
$t = translate('nextpage_next').'<a href="'.$main_index.'?pid='.$pd['ID'].'">'.strip_tags(translate($pd['title'], false)).'</a>';
return $t;
}

// ������� ���� �� ������������ ����

function nextpage_from_parent($ld){
global $page_id;
// ���������� ����
$p = db_select_1('*', 'menu_tree', "`group`=".$ld['group']);
if (!$p) return false;
// ����� �� ����� ��� �������� �������� �� ������� � ������������ ����.
$ld = db_select_1('*', 'menu_items', "`group`='".$p['parent']."' AND `link`=".$p['index_page']);
if (!$ld) return false;
// ����� �� ��������� ���� � ������������ ����
$nl = db_select_1('*', 'menu_items', "`group`='".$ld['group']."' AND `place`>".$ld['place']." ORDER BY `place`");
return $nl;
}

?>
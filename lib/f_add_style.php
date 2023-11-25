<?php
/* 
VanyoG CMS - a simple Content Management System
Copyright (C) 2021 Vanyo Georgiev <info@vanyog.com>

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

// ��������� ������� �� ������������ ������������ �� ������������� CSS ���.
// � ���� ��� � ������� `options` �� ������� ������ � ����� 'css_��������' � 
// ���� ������� �� ������� � ��������� $n = '��������'.
// �� ��������� ���� ��� ����� ����� � ��� ��� 
// �� ������ ��� ���������� ���������� $added_styles,
// ����� ������ �� �� ������� ���� ������� �� ����������.

function add_style($n){
if(empty($GLOBALS['db_link'])) return '';
global $added_styles;
if(!isset($added_styles)) $added_styles = '';
$v = '';
$vd = db_select_m('value', 'options', "`name`='css_$n'");
if(!$vd) store_value("css_$n", '');
else $v = $vd[0]['value'];
if($v && strpos($added_styles, $v)===false) $added_styles .= $v;
}

?>

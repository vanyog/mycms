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

// � ����� �� ��������� ��� ���� place, ����� ����� �� �������� ���� �� ��������� �� ��������.
// ���� ���� ������� ����������� ��������� ���� 10.
// ��� ������������ �� ������� �� ���� �� ��������� �� ������� ������ place �� ������, 
// ����� ������ �� �� ��������. ��������, �� �� �� 
// �������� ����� ������ � place=20 � place=30, �� ������ place=25.

// ��������� ������ ������� ������ ����������� �� ���� place ���� 10
// � ������ �� �� �������� ���� ���� �� ���������� ������������.

include("conf_manage.php"); 
include_once($idir."conf_paths.php");
include_once($idir."lib/f_db_select_m.php");
include_once($idir."lib/f_db_places10.php");

$t = $_GET['t']; // ��� �� ���������

db_places10($t);

// ������� �� ����������, �������� �������
header('Location: '.$_SERVER['HTTP_REFERER']);

?>

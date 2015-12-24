
<?php

/*
MyCMS - a simple Content Management System
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

// ���� ���� � ������������ �� �� ������� �������� � include("build_page.php");
// �� ����� php �������, �� �� ������ ����������� � ��� ��������.
// �������� �� ���� �� ���������� �� ���������� �� �������������� �� ���������� $adm_pth (��� conf_paths.php).
// ����� ����������� �� ������������ �� ���������� ������ �� � ��������� �� ������������: 

// $page_content

// ��� � ���������� �� ���������� ��������� � ��:

// $page_title - ���������� �� ����������
// $page_header - ������������ ������, ����� �� ������� ����� <head></head>
// $body_adds - ������������ �������� �� <body> ����

// ����� �� ��������������� �� ����������
//include("count-visits.php");

$idir = dirname(dirname(__FILE__)).'/';

if (!isset($page_content)) $page_content = 'This page is still empty.';
if (!isset($page_title)) $page_title = '';
if (!isset($page_header)) $page_header = '';
if (!isset($body_adds)) $body_adds = '';

if (!isset($pth)) $pth = '../';

header("Content-Type: text/html; charset=windows-1251");
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   <title>'.$page_title.'</title>
   <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
   <link href="/3/_T35/'.$pth.'_style.css" rel="stylesheet" type="text/css">
   '.$page_header.'
</head>

<body'.$body_adds.'>
'.$page_content.
//visit_count().  // ����� �� ��������������� �� ����������
'
</body>
</html>
';

?>

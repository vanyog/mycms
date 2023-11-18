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

// ���� ���� � ������������ �� �� ������� �������� � include("build_page.php");
// �� ����� php �������, �� �� ������ ����������� � ��� ��������.
// �������� �� ���� �� ���������� �� ���������� �� �������������� �� ���������� $adm_pth (��� conf_paths.php).
// ����� ����������� �� ������������ �� ���������� ������ �� � ��������� �� ������������: 

// $page_content

// ��� � ���������� �� ���������� ��������� � ��:

// $page_title - ���������� �� ����������
// $page_header - ������������ ������, ����� �� ������� ����� <head></head>
// $body_adds - ������������ �������� �� <body> ����
// $added_styles - CSS ������� �� �����������

// ����� �� ��������������� �� ����������
//include("count-visits.php");

$idir = dirname(dirname(__FILE__)).'/';

if (!isset($page_content)) $page_content = 'This page is still empty.';
if (!isset($page_title)) $page_title = '';
if (!isset($page_header)) $page_header = '';
if (!isset($body_adds)) $body_adds = '';
if (!isset($site_encoding)) $site_encoding = 'windows-1251';
if (!isset($added_styles)) $added_styles = '';

if (!isset($pth)) $pth = '../';

header("Content-Type: text/html; charset='.$site_encoding.'");

// ��������� ���� �� MYSQL ��������, ��� � ���������� �� �� ��������
if(isset($exe_time)){
  $exe_time = number_format(microtime(true) - $exe_time, 3);
  $page_content = str_replace('DB_REQ_COUNT',"$db_req_count $exe_time ", $page_content);
}

echo '<!DOCTYPE html>
<html>
<head>
   <title>'.$page_title.'</title>
   <meta http-equiv="Content-Type" content="text/html; charset='.$site_encoding.'">
   '.$page_header.'
<style>
'.$added_styles.'
</style>
</head>

<body'.$body_adds.'>
'.$page_content.
//visit_count().  // ����� �� ��������������� �� ����������
'
</body>
</html>
';

?>

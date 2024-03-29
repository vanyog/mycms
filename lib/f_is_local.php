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

include_once($idir."lib/f_query_or_cookie.php");
include_once($idir."lib/f_stored_value.php");
include_once($idir."lib/f_adm_links.php");

// ���������� � �������, ����� ����� ���������� �� �������� � ����������������

$web_host = stored_value('host_web','mysite.org'); // ������ �� �����.
if ((substr($_SERVER['SERVER_ADDR'],0,8)!='127.0.0.') &&
     ($_SERVER['SERVER_ADDR']!='::1') && ($web_host=='mysite.org')
   )
   $page_content = "<p>Please set 'host_web' option. ";

$local_host = stored_value('host_local','localhost'); // ������� ������ �� �����, 
                      // ����� �� � �������� ���� ��������.
                      // �������� ��, ������ �� ������� ������ ������� ����� �� �����.

// ����� �� phpMyAdmin �� ����������� ������
$phpmyadmin_site = stored_value('phpmyadmin_web','http://mysite.com/phpmyadmin');

// ����� �� phpMyAdmin �� �������� ������
$phpmyadmin_local = stored_value('phpmyadmin_local','http://localhost/phpmyadmin');

// ����� ������, ��� ������ �� ������ �� ������� ������.
function is_local(){
global $web_host;
return empty($web_host) || ($_SERVER['HTTP_HOST']!=$web_host);
}

// ��������� in_edit_mode() ����� ������ ��� ������ � � ����� �� �����������
// � ����� ����� �� �������� ������� �� ����������� �� �������, ��������, ������ � ��.
// ������ � � ����� �� ����������� ���:
// - ������ ��������� im=admin
// - ������ $_GET['im']=='admin'

function in_edit_mode(){
global $edit_name, $edit_value;// die(print_r($_COOKIE,true ));
if (isset($_COOKIE['PHPSESSID']) || show_adm_links()) return query_or_cookie($edit_name,$edit_value);
return false;
}



?>

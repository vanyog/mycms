<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// �����, ����� �� ������������ �� ���������� $page_content ����� �������-��������,
// �� ������ �� SEO � �������� �����. ��� �� �������� ���������� $og_description
// � ��������� ��������, �� ����� ���� ��������.
// (������������ �� ���������� � ��������� �� ���������� $page_content �� ������� 
// � ��� CONTENT, ����� �� ���� � ������� 'scripts'.)

function ogdescription(){
global $page_content, $og_description, $site_encoding;// die($og_description);
if(!empty($og_description)) return $og_description;
if(empty($page_content)) return '';
$a = strip_tags($page_content);
$a = str_replace('&nbsp;',' ',$a);
$a = str_replace("\n",' ',$a);
$a = str_replace("\r",'',$a);
$a = trim($a);
$l = 300;
$fl = strlen($a)-1;
while ( ($l<$fl) && !in_array($a[$l], array(' ', ',', '.', ':', '-', '&') ) ) $l++;
$rz = substr($a,0,$l);
if (strlen($a)>strlen($rz)) $rz .= '...';
return htmlspecialchars($rz, ENT_QUOTES, $site_encoding);
}

?>

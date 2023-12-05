<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// ������� ����������, ��� ������ � �� ������� ������.
// ����������� $a �� ������ �� ��� �����, ��������� � |.
// ������� ���� ������ �� � 'true' ��� 'false'. 
// ��� 'true' - ������������ �� ������� ���� �� ������� ������.
// ��� 'false' - ������������ �� ������� ���� �� ��������� ������.
// ������� ����: ��� ������� � 'content_' � ��� �� ����� �� ������� `content`.
// ��� �� ������� � 'content_' � ������������, ����� �� �������.

function iflocal($a){
$m =  array();
$rz = '';
if(!preg_match('/(true|false)\|((content_)?.*)/s', $a, $m)) die("Module IFLOCAL: wrong parameter format.<p>$a");
if((($m[1]=='true') && is_local()) || (($m[1]=='false') && !is_local())){
  if(isset($m[3])) $rz = translate($m[2]);
  else $rz = $m[2];
}
return $rz;
}

?>
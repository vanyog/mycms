<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// ��������� �� ��� ������

include('conf_manage.php');
include_once($idir.'conf_paths.php');

// ��� �� �������
$fn = $_GET['f'];
// ��������� ���������� �� �������
$afn = $apth."filter/$fn";

// ��������� �� ������������, ��� �� ����������
if (!file_exists($afn)) $r = mkdir($afn, 0644);

// ��������� ��� �� ����� �� �������
$affn = "$afn/$fn.php";

// ���������� �� �����
$cnt = '<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// ������ '.$fn.'

function '.$fn.'($t){
return "$t
<p>--Filtered by '.$fn.' filter--</p>";
}

?>';

// �������� �� ����� �� �����
$f = fopen($affn,'w');

// ��������� �� ������������
fwrite($f, $cnt);

// ��������� �� �����
fclose($f);

// ������� ��� ����������� ��������
header('Location: '.$_SERVER['HTTP_REFERER']);

?>

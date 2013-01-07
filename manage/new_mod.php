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

// ������� ��� ����� � ��� $_GET['n']

$idir = dirname(dirname(__FILE__)).'/';

// ��� �� ������
$mf = strtolower($_GET['n']);

// ��� �� ������������ �� ������
$md = $idir.'mod/'.$mf;

// ��������� �� ������������, ��� �� ����������
if (!file_exists($md)) mkdir($md);

// ��� �� ����� � ��������� �������
$ff = "$md/f_$mf.php";

// ���������� �� �����
$fc = '<?php
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

function '.$mf.'(){
return \'Module '.$mf.' works\';
}

?>';

$f = fopen($ff,'w');
fwrite($f,$fc);
fclose($f);

?>
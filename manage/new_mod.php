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

// Създава нов модул с име $_GET['n']

include("conf_manage.php");
include($idir.'conf_paths.php');

// Име на модула
$mf = strtolower($_GET['n']);

// Име на директорията на модула
$md = $_SERVER['DOCUMENT_ROOT'].$mod_pth.$mf; //echo "$md<br>"; die;

// Създаване на директорията, ако не съществува
if (!file_exists($md)) if (!mkdir($md, 0755, true)) die("Can't create directory: $md");

// Име на файла с модулната функция
$ff = "$md/f_$mf.php";
$el = $adm_pth.'edit_file.php?f='.current_pth($ff);

// Съдържание на файла
$fc = '<?php
/*
MyCMS - a simple Content Management System
Copyright (C) '.date("Y").'  Vanyo Georgiev <info@vanyog.com>

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
$rz = \'\';
$rz .= \'Module <a href="'.$el.'">'.$mf.'</a> works\';
$rz .= \'\';
return $rz;
}

?>';

// Създаване и записване на файла
$f = fopen($ff,'w');
fwrite($f,$fc);
fclose($f);

// Връщане към рефериращата страница
header("Location: ".$_SERVER['HTTP_REFERER']);

?>

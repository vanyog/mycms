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

// Връща текущата директория
function current_pth($f = __FILE__){
$p1 = $_SERVER['DOCUMENT_ROOT'];         $n1 = strlen($p1);
if ($p1[$n1-1]=='/') $n1--;
$p2 = str_replace('\\','/',dirname($f)); $n2 = strlen($p2);
if(substr($p2, 0, $n1)!=$p1){
  $or = stored_value('uploadfile_otherroot');
  if($or){
    $p1 = $or;
    $n1 = strlen($p1);
  }
}
$r = substr($p2,$n1,$n2-$n1).'/';
return $r;
}

?>
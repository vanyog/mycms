<?php
/*
VanyoG CMS - a simple Content Management System
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

// Преобразуване на пътя до файла в път за достъп през уеб

function uploadfile_href($fp){
  // Проверка дали файлът не идва от друг сървър
  $l = strlen($_SERVER['DOCUMENT_ROOT']);
  // document_root деректорията на другия сървър, зададена с настройката uploadfile_otherroot
  $or = stored_value('uploadfile_otherroot');
  // Път до файла на този сървър
  $thfn = $fp;
  $ne = false;
  if ($or){
    $l = strlen($or);
    // Истина, ако файлът не е бил в document_root на другия сървър
    $ne = $or != substr($fp, 0, $l);
    if(!$ne) $thfn = $_SERVER['DOCUMENT_ROOT'].substr($fp,$l);
  }
  if ($ne){
    $l = strlen($_SERVER['DOCUMENT_ROOT']);
    // Истина ако не е в document_root и на този сървър
    $ne = $_SERVER['DOCUMENT_ROOT'] != substr($fp, 0, $l);
  }
  // href - атрибут на файла
  $f = substr($fp,  $l, strlen($fp)-$l);
  $f = str_replace(' ', '%20', $f);
  $f = str_replace('_', '%5F', $f);
  return $f;
}

?>

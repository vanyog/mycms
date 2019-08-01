<?php
/*
MyCMS - a simple Content Management System
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

// Изтриване на доклад с номер $_GET['proc'] от неговия автор

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'mod/userreg/f_userreg.php');
include_once($idir.'lib/f_db_delete_where.php');

// Тип на потребителите
$utype = stored_value('conference_usertype', 'basa2019');

// Номер на влезлия потребител
$id = userreg_id($utype);

if(is_numeric($_GET['proc'])){
  $pr = 1*$_GET['proc'];
  $d = db_select_1('*', 'proceedings', "`ID`=$pr");
  delete_file($d['fulltextfile']);
  delete_file($d['fulltextfile2']);
  delete_file($d['fulltextfile3']);
  db_delete_where('proceedings', "`ID`=$pr AND `user_id`=$id");
}

function delete_file($f){
if(empty($f)) return;
$p = $_SERVER['DOCUMENT_ROOT'].stored_value('conference_files').$f;
if(file_exists($p)) unlink($p);
}

header("Location: ".$_SERVER['HTTP_REFERER'] );

?>
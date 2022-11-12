<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2022  Vanyo Georgiev <info@vanyog.com>

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

// Заключване ($_GET['locked']=1) или отключване ($_GET['locked']=0) 
// на рецензия с номер $_GET['ID']

error_reporting(E_ALL); ini_set('display_errors',1);

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'mod/usermenu/f_usermenu.php');
include($idir.'lib/f_db_update_record.php');

// Проверяване правата на потребителя 
$tx = usermenu(true);

// Ако няма право за модул conference - край
if(empty($can_manage['conference'])) die("Not permitted for current user");

$d = array(
  'ID'=>1*$_GET['ID'],
  'locked'=>1*$_GET['locked']
);

db_update_record($d, 'reviewer_work', false);

// Адрес на който да се върне
$b = $_SERVER['HTTP_REFERER'];
header('Location: '.$b);

?>

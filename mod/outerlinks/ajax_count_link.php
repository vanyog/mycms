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

// Изброяване кликването на линк с ID съдържащ се в $_GET['l'];

if(!isset($_GET['l'])) die('Bad request.');

$id = substr($_GET['l'],2);
if(!is_numeric($id)) die('Bad request.');
else $id = intval($id);

$idir = dirname(dirname(__DIR__)).'/'; 
$ddir = $idir;

include_once($idir.'lib/f_db_update_record.php');

header("Content-Type: text/html; charset=windows-1251");

$d = array('ID'=>$id, 'clicked'=>'`clicked`+1');

echo db_update_record($d, 'outer_links');

?>
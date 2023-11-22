<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2021  Vanyo Georgiev <info@vanyog.com>

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

if( !(isset($_GET['cid']) && is_numeric($_GET['cid']) ) &&
    !(isset($_GET['lid']) && is_numeric($_GET['lid']) )
  ) 
    die("Unsufficien parameters");

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

// Отваряне на страница по номер на заглавието
if(isset($_GET['cid'])){
  $n = db_select_1('name,language', 'content', 'ID='.(1*$_GET['cid']) );
  $i = db_table_field('ID', 'pages', "`title`='".$n['name']."'");
  header("Location: $main_index?pid=$i&lang=".$n['language']);
  die;
}
// Отваряне на раздел от връзки
else {
  $j = 1 * $_GET['lid'];
  $l = db_select_1('link,up', 'outer_links', "`ID`=$j" );
  if(empty($l['link'])) $i = "6&lid=$j";
  else $i = "6&lid=".$l['up'];
  $i .= "#outer_links";
}

header("Location: $main_index?pid=$i");

?>
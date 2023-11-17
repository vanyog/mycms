<?php
/* 
VanyoG CMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Невидима страница се прави видима и обратно

if (!isset($_GET['pid'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."conf_paths.php");
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/f_page_cache.php");

// Номер на страницата, на която се променя видимостта
$page_id = 1*$_GET['pid'];

// Видимост на страницата
$h = db_table_field('hidden','pages',"`ID`=$page_id");

// Проверяване правата на потребителя
$tx = usermenu(true);

// Ако потребителят няма право да редактира страницата - край.
if (!$can_edit) echo die("Your have no permission to edit this page.");

// Променяне на видимостта
if ($h==1) $h=0; else $h=1;
db_update_record(array('ID'=>$page_id,'hidden'=>$h),'pages');

// Връщане на страницата
$p = $main_index.'?pid='.$page_id;
$q = 'http://'.$_SERVER['HTTP_HOST'].$p;
purge_page_cache($q);
header("Location: $p");

?>

<?php
/* 
MyCMS - a simple Content Management System
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

// Страница с номер $_GET['p1'] се прави да показва съдържанието на страница $_GET['p2'].

if (!isset($_GET['p1'])||!isset($_GET['p2'])) die("Insufficient parameters");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."conf_paths.php");
include_once($idir."lib/f_db_insert_or_1.php");
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/f_page_cache.php");

// Номер на страницата, на която се променя съдържанието
$page_id = 1*$_GET['p2'];

// Проверяване правата на потребителя
$tx = usermenu(true);

// Ако потребителят няма право да редактира страницата - край.
if (!$can_edit) echo die("Your have no permission to edit this page.");

$i = 1*$_GET['p1'];

$n1 = db_table_field('content','pages',"`ID`=$i");

$n2 = db_table_field('content','pages',"`ID`=$page_id");

$lk = array_keys($languages);

$d = array();

foreach($lk as $k){
  $d1 = db_select_1('*', 'content', "`name`='$n2' AND `language`='$k'");
  if (!$d1){
    $d1 = array(
      'name' => $n2,
      'nolink' => 0,
      'date_time_1' => 'NOW()',
      'date_time_2' => 'NOW()',
      'language' => $k
    );
  }
  $d1['text'] = '<!--$$_CONTENT_'.$n1.'_$$-->';
  $d[] = $d1;
  db_insert_or_1($d1, 'content', "`name`='$n2' AND `language`='$k'", 'b');
}

// Връщане на страницата
$p = stored_value('main_index_file',$pth.'index.php').'?pid='.$page_id;
$q = 'http://'.$_SERVER['HTTP_HOST'].$p;
purge_page_cache($q);
header("Location: $p");

?>

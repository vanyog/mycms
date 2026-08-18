<?php

/*
VanyoG CMS - a simple Content Management System
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

// Справка за страниците създадени в зададен период

include_once("conf_manage.php");
include_once($idir.'conf_paths.php');
$language = 'bg';

$fromDate = '2024-01-01 00:00:00';  // От дата
$toDate   = '2024-12-30 00:00:00';  // До дата

// Нови страници
$d = db_select_m('name', 'content', 
                 "`date_time_1`>'$fromDate' AND `date_time_1`<'$toDate'". 
                 " AND `name` LIKE 'p%_content'",false);
show_data($d);

// Обновени страници
$d = db_select_m('name', 'content', 
                 "NOT (`date_time_1`>'$fromDate' AND `date_time_1`<'$toDate')". 
                 " AND `date_time_2`>'$fromDate' AND `date_time_2`<'$toDate'".                 
                 " AND `name` LIKE 'p%_content'",false);
echo "<p>";
show_data($d);

function show_data($d){
$n = 0; $v = 0;
foreach($d as $d1){
  $n++;
  $d2 = db_select_1('*','pages',"`content`='".$d1['name']."'");
  $t = translate($d2['title'],false);
  $c = db_table_field('text', 'content', "`name`='".$d2['content']."'");
  $c = strip_tags($c);
  $l = strlen($c); $v = $v + $l;
  $l1 = number_format($l/1800,1);
  $h = 'https://sci.vanyog.com/index.php?pid='.$d2['ID'];
//  die($c);
//print_r($d2); 
  echo "$n. <a href=\"$h\">$t</a>";
//  echo " - $l1";
  echo "<br>";
}
echo number_format($v/1800,1);
}

?>
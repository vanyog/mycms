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

// ѕочистване на таблица 'visit_history' от дублиращи се записи

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_m.php');

ini_set('memory_limit','512M');
$da = db_select_m('*','visit_history', '1 ORDER BY `page_id`,`date`');
$q = "DELETE FROM `$tn_prefix"."visit_history` WHERE ";
$d0 = $da[0];
$c = 0;
for($i=1; $i<count($da); $i++){
  $d = $da[$i];
  if ( ($d['page_id']==$d0['page_id']) && ($d['date']==$d0['date']) && ($d['count']==$d0['count']) )
  {
     $q .= "`ID`=".$d['ID']." OR\n";
     $c++;
  }
  if ($c==3000) break;
  $d0 = $d;
}
$q = substr($q, 0, strlen($q)-4).';';
if ($c) mysqli_query($db_link,$q);
die("$c records deleted");

?>

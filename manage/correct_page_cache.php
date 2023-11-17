<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2014  Vanyo Georgiev <info@vanyog.com>

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

//
// Изчистване на таблица $tn_prefix.'page_cashe' от неприемливи адреси

error_reporting(E_ALL); ini_set('display_errors',1);

include('conf_manage.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_delete_where.php');
include_once($idir.'lib/f_page_cache.php');

// Четене на всички полета `name`
$nms = db_select_m('ID,name','page_cache','1'); 

$c = 0;
foreach($nms as $n) {
  $an = acceptable($n['name'],true);
//  echo "<p>$c<br>".$n['name']."<br>$an</p>";
 if ($an!=$n['name']){
     $c++;
     db_delete_where('page_cache',"`ID`=".$n['ID']);
     echo "<p>$c<br>".$n['name']."<br>$an</p>";
  }
  if ($c==100) break;
}

?>

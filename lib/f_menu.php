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

// Функцията menu($i) съставя последователност от хипервръзки (Меню).
// $i е номер на групата хипервръзки от таблица $tb_preffix.'menu_items'

include_once($idir."lib/f_is_local.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');

function menu($i){
global $pth, $adm_pth, $page_id;
$d = db_select_m('*','menu_items',"`group`=$i ORDER BY `place`");
$rz = '<div id="page_menu">
';
foreach($d as $m){
  $lnn = 1*$m['link'];
  $ln = $m['link'];
  if ($lnn) $ln = $pth.'index.php?pid='.$lnn;
  $pl = '';
  if (in_edit_mode()) $pl = $m['place'];
  if ($page_id!=$lnn) $rz .= '<a href="'.$ln.'">'.$pl.translate($m['name']).'</a> '."\n";
  else $rz .= '<span>'.$pl.translate($m['name'])."</span> \n";
}
if (in_edit_mode()){
  $ni = db_table_field('MAX(`ID`)','menu_items','1')+1;
  $rz .= '<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$i.'&link='.$page_id.
         '&name=p'.$ni.'_link">New</a> '."\n";
}
return $rz.'</div>';
}

?>

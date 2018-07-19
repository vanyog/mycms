<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2018  Vanyo Georgiev <info@vanyog.com>

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

// Ако страницата е в скрит раздел, се извършва пренасочване към главната страница на външния рездел, който не е скрит.

function redirifhidden(){
if(in_edit_mode()) return '';
global $page_data, $main_index;
$rz = '';
$pid = stored_value('main_index_pageid',1);
$pd = $page_data;
//$rz .= "<p>".print_r($pd,true);
$redir = $pd['hidden'];
//if(!$redir) return '';
$gd = db_select_1('*', 'menu_tree', "`group`=".$pd['menu_group']);
//$rz .= "<p>".print_r($gd,true);
if($gd['index_page']!=$pd) $pd = db_select_1('*', 'pages', "`ID`=".$gd['index_page']);
//$rz .= "<p>".print_r($pd,true);
while ($gd['parent']) {
  if($redir && !$pd['hidden']){ $pid = $pd['ID']; break; }
  $redir = $pd['hidden'];
  $gd = db_select_1('*', 'menu_tree', "`group`=".$gd['parent']);
  //$rz .= "<p>".print_r($gd,true);
  $pd = db_select_1('*', 'pages', "`ID`=".$gd['index_page']);
  //$rz .= "<p>".print_r($pd,true);
};
if($redir) header("Location: $main_index?pid=$pid");
return $rz;
}

?>
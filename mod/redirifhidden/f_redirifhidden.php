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

// Ако страницата е в скрит раздел, се извършва пренасочване 
// към главната страница на по-външен рездел, който не е скрит.

// Пренасочване на се извършва:
// - в режим на редактиране
// - при наличие на глобална променлива $redirifhidden_cancel с непразна стойност
// - при наличие на параметър $_GET['noredir'] със стойност равна на стойността на 
//   настройка с име 'redirifhidden_cancel' от таблица 'options'

function redirifhidden(){
global $redirifhidden_cancel, $adm_pth, $page_data, $main_index;
if(!empty($redirifhidden_cancel)) return '';
$v = stored_value('redirifhidden_cancel', false);
$pd = $page_data;
$redir = $pd['hidden'];
if(in_edit_mode() && $pd['hidden']){
  if(empty($v)) return "<a href=\"$adm_pth".
                       "new_record.php?t=options&name=redirifhidden_cancel\">".
                       "Create value for 'redirifhidden_cancel' option</a>\n";
  $a = set_self_query_var('noredir',$v);
  return "<a href=\"$a\">$v</a>\n";
}
if(isset($_GET['noredir'])){
  if($v && ($_GET['noredir']==$v)) return '';
}
$pid = stored_value('main_index_pageid',1);
$gd = db_select_1('*', 'menu_tree', "`group`=".$pd['menu_group']);
if($gd['index_page']!=$pd) $pd = db_select_1('*', 'pages', "`ID`=".$gd['index_page']);
while ($gd['parent']) {
  if($redir && !$pd['hidden']){ $pid = $pd['ID']; break; }
  $redir = $pd['hidden'];
  $gd = db_select_1('*', 'menu_tree', "`group`=".$gd['parent']);
  $pd = db_select_1('*', 'pages', "`ID`=".$gd['index_page']);
};
if($redir) header("Location: $main_index?pid=$pid");
return '';
}

?>
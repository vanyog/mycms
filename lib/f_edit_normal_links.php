<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2012 Vanyo Georgiev <info@vanyog.com>

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

// Хипервръзка "Edit/Normal" за превключване от режим на редактиране към нормален режим.

include_once($idir."lib/f_is_local.php");
include_once($idir."lib/f_set_query_var.php");

function edit_normal_link($y = true){
global $edit_name, $edit_value, $page_data, $language, $adm_pth;
$p = array(false=>'', true=>'Page ');
if (in_edit_mode()) {
  $el = '';
  if(!empty($GLOBALS['page_id'])){
    $id = db_table_field('ID', 'content', "`name`='".$page_data['content']."' AND `language`='$language'", 0);
    if($id) $el = ' <a href="'.$adm_pth."edit_record.php?t=content&r=$id\">*</a>";
  }
  return '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.set_query_var($edit_name,'0').'" title="Switch to normal mode">'.
         $p[$y].'Normal</a>'.$el;
}
else
  return '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.set_query_var($edit_name,$edit_value).'" title="Switch to edit mode">'.$p[$y].'Edit</a>';
}

?>

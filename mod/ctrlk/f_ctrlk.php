<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Този модул активира възможността в режим на редактиране с натискане на Ctrl+K
// да се отваря за редактиране съдържанието на текущата страница
// Използване: <body <!--$$_BODYADDS_$$--> ... ><!--$$_CTRLK_$$-->

function ctrlk(){
if(!in_edit_mode()) return '';
global $body_adds, $page_header, $page_data, $language, $adm_pth;
$id = db_table_field('ID', 'content', "`name`='".$page_data['content']."' AND `language`='$language'", 0);
$lk = $adm_pth.'edit_record.php?t=content&r='.$id;
$page_header .= '<script>
function ctrl_pus_e(e,v){
if((e.ctrlKey || e.metaKey) && (e.key=="k")){
  document.location = "'.$lk.'";
  v.preventDefault();
}
}
</script>
';
$body_adds .= ' onkeydown="ctrl_pus_e(event, this);"';

return '';
}

?>
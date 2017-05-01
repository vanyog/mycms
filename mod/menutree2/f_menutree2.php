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

// Функцията menu_tree() показва обратния път от текущата страница към главната страница.
// Използват се записите на таблица $tn_prefix.`menu_tree`, които посочват родителското меню на всяко меню.
// Настройката menutree_last, определя каква препратка (заглавие) да се показва на последното място:
// current - заглавието на текущата страница
// index - линк към главната страница на текущия раздел (така е по подразбиране).

include_once($idir.'lib/f_db_select_1.php');

function menutree2(){
global $pth, $page_id, $page_data, $main_index, $page_header, $body_adds;
$page_header .= '<script type="text/javascript"><!--
var visible_sub;
function hide_sub(){
var d = document.getElementById("sub_"+visible_sub);
if(d) d.style.display = "none";
}
function show_sub(a){
hide_sub();
var d = document.getElementById("sub_"+a);
var l = document.getElementById("sm_"+a);
d.style.display = "inline-block";
d.style.left = l.offsetLeft+"px";
d.style.top = (l.offsetTop+l.offsetHeight)+"px";
visible_sub = a;
}
--></script>';
$rz = '';
// Четене записа на менюто на страницата
$pr = db_select_1('*','menu_tree',"`group`=".$page_data['menu_group']);
// Съставяне на подменю
$sm = menutree2_submenu($page_data['menu_group']);
if (!$pr) return $rz;
$pg = $page_data;
// Четене записа на главната страница на менюто
$pg = db_select_1('*','pages','ID='.$pr['index_page']);
$rz = '<a id="sm_'.$page_data['menu_group'].'" href="'.$main_index.'?pid='.$pg['ID'].
           '" onclick="show_sub('.$page_data['menu_group'].');return false;">'.translate($pg['title']).' &#9660;</a>'.$rz;
//if($page_id!=$pr['index_page']) $rz .= "&nbsp;&#10093; \n".'<span>'.translate($page_data['title'])."</span>\n";
// Ако менюто има родители се добавят и те.
$psd = array(0=>$pr['group']);
while ($pr['parent'])
{
  // Мярка против зацикляве
  if (in_array($pr['parent'],$psd)) break;
  $psd[] = $pr['parent'];
  $pi = $pr['parent'];
  $pr = db_select_1('*','menu_tree',"`group`=".$pr['parent']);
  if (!$pr) $pg = db_select_1('*','pages',"`menu_group`=$pi");
  else $pg = db_select_1('*','pages','ID='.$pr['index_page']);
  if ($rz) $rz = "&nbsp;&#10093; \n".$rz;
  $rz = '<a id="sm_'.$pg['menu_group'].'" href="'.$main_index.'?pid='.$pg['ID'].
        '" onclick="show_sub('.$pg['menu_group'].');return false;">'.translate($pg['title']).' &#9660;</a>'.$rz;
  $sm .= menutree2_submenu($pg['menu_group']);
}
return '<div id="menu_tree">
'.translate('menutree_start').$rz.'
</div>'.$sm;
}


function menutree2_submenu($g){
global $main_index, $page_id;
$rz = '<div id="sub_'.$g.'">
<a href="#" class="ra" onclick="hide_sub();return false;">'.translate('close', false).'</a>
';
$tm = '';
if(isset($_GET['template'])){
  $t = 1*$_GET['template'];
  $at = stored_value('allowed_templates');
  if(!(strpos($at, ",$t,")===false)) $tm = "&template=$t";
}
$md = db_select_m('*','menu_items',"`group`=$g ORDER BY `place` ASC");
foreach($md as $d){
  $rf = 1*$d['link'];
  if($rf){
     $h = db_table_field('hidden', 'pages', "`ID`=$rf") && !in_edit_mode();
     if($h) continue;
     $rf = "$main_index?pid=$rf$tm";
  }
  else $rf = $d['link'];
  $cr = '';
  if($page_id==$d['link']) $cr = ' class="current"';
  $rz .= "<a href=\"$rf\"$cr>".translate($d['name'], false)."</a>\n";
}
$rz .= '</div>';
return $rz;
}

?>

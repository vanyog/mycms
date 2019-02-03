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
$page_header .= '<script>
var visible_sub;
var donot_hide;
function hide_sub(){// alert("hide "+visible_sub+" "+donot_hide);
var dh = donot_hide;
donot_hide = 0;
if(dh && (dh==visible_sub)) return;
//alert("hide "+visible_sub);
var d = document.getElementById("sub_"+visible_sub);
if(d) d.style.display = "none";
}
function show_sub(a){// alert("show A");
hide_sub();
var d = document.getElementById("sub_"+a);
var l = document.getElementById("sm_"+a);
d.style.display = "inline-block";
d.style.left = l.offsetLeft+"px";
d.style.top = (l.offsetTop+l.offsetHeight)+"px";
visible_sub = a;
donot_hide = a;
return false;
//alert("show B");
}
</script>';

$rz = '';

// Четене записа на менюто на страницата
$pr = db_select_1('*','menu_tree',"`group`=".$page_data['menu_group']);
// Съставяне на подменю
$sm = menutree2_submenu($page_data['menu_group'], $page_id);
if (!$pr) return $rz;

$pg = $page_data;

// Четене записа на главната страница на менюто
$pg = db_select_1('*','pages','ID='.$pr['index_page']);
$rz1 = '<a id="sm_'.$page_data['menu_group'].'" href="'.$main_index.'?pid='.$pg['ID'].
       '" onclick="show_sub('.$page_data['menu_group'].');return false;">'.translate($pg['title']).'</a>';
if(in_edit_mode()) $rz1 .= " ".$pr['group'];
$rz = $rz1.$rz;

// Ако менюто има родители се добавят и те.
$psd = array(0=>$pr['group']);
while ($pr['parent'])
{
  // Мярка против зацикляве
  if (in_array($pr['parent'],$psd)) break;
  $psd[] = $pr['parent'];

  $pi = $pr['parent']; $ci = $pg['ID'];
  $pr = db_select_1('*','menu_tree',"`group`=".$pr['parent']);
  if (!$pr) $pg = db_select_1('*','pages',"`menu_group`=$pi");
  else $pg = db_select_1('*','pages','ID='.$pr['index_page']);

  $rz = '<a id="sm_'.$pg['menu_group'].'" href="'.$main_index.'?pid='.$pg['ID'].
        '" onclick="show_sub('.$pg['menu_group'].');return false;">'.translate($pg['title']).'</a>'.$rz;
  $sm .= menutree2_submenu($pg['menu_group'], $ci);
}
return '<div id="menu_tree">
'.translate('menutree_start').$rz.'
</div>'.$sm;
}


function menutree2_submenu($g, $i){
global $main_index, $pth, $adm_pth;
$rz = '<div id="sub_'.$g.'">
<a href="#" class="ra" onclick="hide_sub();return false;">'.translate('close', false).'</a>
';
$tm = '';
if(isset($_GET['template'])){
  if(is_numeric($_GET['template'])) $t = 1*$_GET['template'];
  else $t = 0;
  $at = stored_value('allowed_templates');
  if(!(strpos($at, ",$t,")===false)) $tm = "&template=$t";
}
$md = db_select_m('*','menu_items',"`group`=$g ORDER BY `place` ASC");
foreach($md as $d){
  $rf = is_numeric($d['link']) ? 1*$d['link'] : 0;
  if($rf){
     $h = db_table_field('hidden', 'pages', "`ID`=$rf") && !in_edit_mode();
     if($h) continue;
     $rf = "$main_index?pid=$rf$tm";
  }
  else $rf = $d['link'];
  $cr = ''; $pl = '';
  if($i==$d['link']) $cr = ' class="current"';
  $el = '';
  // Добавяне на * за редактиране
  if (in_edit_mode()){
    $el = '<a href="'.$pth.'mod/usermenu/edit_menu_link.php?pid='.$i.'&id='.$d['ID'].
           '"  style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
    $pl = ' '.$d['place'];
  }
  $rz .= "<a href=\"$rf\"$cr>".translate($d['name'], false).$pl."</a>$el\n";
}
if(in_edit_mode()){
  $m = db_table_field('MAX(`ID`)', 'menu_items', 1) + 1;
  $rz .= "$g <a href=\"$adm_pth"."new_record.php?t=menu_items&group=$g&name=p$m"."_link&link=$i\">new</a>\n";
}
$rz .= '</div>';
return $rz;
}

?>

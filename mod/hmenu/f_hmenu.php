<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Функция за показване на хоризонтално, падащо меню.
// Данните за менюто се четат от таблица 'menu_items'.
// Параметърът $a е стойността на полето `group` в таблица 'menu_items'.

function hmenu($a){
global $ind_fl, $page_header, $pth, $adm_pth, $page_id;
$p = current_pth(__FILE__);
$page_header .= '<script>'."\n";
// Цветове
$c1 = stored_value('hmenu_color1');  $page_header .= "var color1 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor1'); $page_header .= "var bcolor1 = \"$c1\";\n";
$c1 = stored_value('hmenu_color2');  $page_header .= "var color2 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor2'); $page_header .= "var bcolor2 = \"$c1\";\n";
$c1 = stored_value('hmenu_color3');  $page_header .= "var color3 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor3'); $page_header .= "var bcolor3 = \"$c1\";\n";
$page_header .= '</script>'."\n";
$page_header .= '<script src="'.$p.'functions.js"></script>'."\n";
$il = db_select_m('*','menu_items',"`group`=$a ORDER BY `place` ASC");
$sm = '';
$rz = '';
$ia = index_array();
$j = 1;
foreach($il as $i){
  $lk = 1*$i['link'];
  $c = '';
  if ($lk) {
    $sm .= hsubmenu($lk,$ia,$j);
    if (in_array($lk,$ia)) $c = ' class="current"';
    $lk = $ind_fl.'?pid='.$lk;
  }
  else $lk = $i['link'];
  $rz .= '<a href="'.$lk.'"'.$c.' onMouseOver="show_hlayer('.$j.',this)">'.translate($i['name'],false);
  // Добавяне на * за редактиране 
  if (in_edit_mode()){
     $rz .= '<a href="'.$pth.'mod/usermenu/edit_menu_link.php?pid='.$page_id.'&amp;id='.$i['ID'].
     '" style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
  }
  $rz .= "</a>\n";
  $j++;
}
if (in_edit_mode()){
  $ni = db_table_field('MAX(`ID`)','menu_items','1')+1;
  $rz .= " ".'<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$a.'&link='.$page_id.
         '&name=p'.$ni.'_link">New</a> '."\n";
}
return $sm.'<div id="menu_'.$a."\">\n".$rz.'</div>';
}

// Връща подменюто към линк $lk

function hsubmenu($lk,$ia,$j){
global $ind_fl;
// Номер на менюто, на което страница $lk е главна
$g = db_table_field('`group`','menu_tree',"`index_page`=$lk");
// Записите на това меню
$da = db_select_m('*','menu_items',"`group`=$g ORDER BY `place` ASC");
$rz = '';
if (count($da)>1) foreach($da as $d){
  $lk = 1*$d['link'];
  $c = '';
  if ($lk) {
    if (in_array($lk,$ia)) $c = ' class="current"';
    $lk = $ind_fl.'?pid='.$lk;
  }
  else $lk = $d['link']; 
  $rz .= '<a href="'.$lk.'"'.$c.'>'.translate($d['name'], false)."</a>\n";
}
if (in_edit_mode()) $rz .= 'id '.$g;
if ($rz) $rz = '<div id="HLayer'.$j.'">'."\n".$rz."</div>\n";
return $rz;
}

// Връща масив от номера на главните страници на подменютата, в които участва текущата страница.
// Използва се за определяне дали даден линк в меню сочи към подменю, в което участва текущата страница, 
// за да се покаже този линк с друг цвят.

function index_array(){
global $page_id;
$rz = array($page_id=>0);
// Четене номерата на менютата на всички линкове към текущата страница
$da = db_select_m('`group`','menu_items',"`link`='$page_id'");
foreach($da as $d){
 $g = $d['group'];
 // Мярка против зацикляне
 // Масив с проверените в цикъла групи
 $c = array($g);
 do {
   $t = db_select_1('parent,index_page','menu_tree',"`group`=$g");
   $g = $t['parent'];
   $rz[$t['index_page']] = 0;
   if (in_array($g, $c)) { // Установено е зацикляне
     // Запазване на проблемния запис
     store_value('hmenu_error', 'page:'.$t['index_page'].' group:'.$g.' parent:'.$t['parent']);
     // Прекратяване на цикъла
     break;
   }
   $c[] = $g;
 } while ($g);
}
return array_keys($rz);
}


?>

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
// $i е номер на групата хипервръзки от таблица $tb_preffix.'menu_items'.
// Ако е изпратен втори параметър $id той се слага за id атрибут на <div> елемента,
// в който се слагат хипервръзките от менюто.

include_once($idir."lib/f_is_local.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');

function menu($i, $id = 'page_menu'){
global $ind_fl, $adm_pth, $page_id, $page_data, $pth;
$d = db_select_m('*','menu_items',"`group`=$i ORDER BY `place`");
$rz = ''; // Връщания резултат
$once = false; // Флаг, който се използва за да се покаже само веднъж различно линка от менюто на текущата страница
$sm = ''; // Изскачащо подменю, ако има такова
$si = 1; // Номер на изскачащото подменю
$lk = stored_value('menu_aslink'); // Дали линкът на текущата страница да се покаже като линк
$pp = stored_value('menu_popup');  // Дали да се показват изскачащи подменюта
$hm = stored_value('menu_hide',1); // Дали да се скриват линкове към скрити страници
foreach($d as $m){
  $lnn = 1*$m['link'];
  $ln = $m['link']; 
  if ($lnn){
    $h = db_table_field('hidden', 'pages', "`ID`=$lnn");
    if ($hm && $h && !in_edit_mode() && !show_adm_links() ) continue;
    $ln = $ind_fl.'?pid='.$lnn;
  }
  $pl = '';
  if (in_edit_mode()) $pl = $m['place'].".";
  $js = '';
  $sm1 = '';
  if ($pp && ($i==$page_data['menu_group'])) $sm1 = submenu($m,$si);
  if ($pp && ($i==$page_data['menu_group'])) $js = ' onMouseOver="show_layer('.$si.',this);"';
  if ($once || !is_parrent_menu($i, $m['link'])) {
     $rz .= "<a href=\"$ln\"$js>".$pl.translate($m['name'],false).'</a> '."\n";
  }
  else {
     $once = true;
     if ($lk) $rz .= '<a href="'.$ln.'" class="current"'.$js.'>'.$pl.translate($m['name'],false).'</a> '."\n";
     else $rz .= '<span class="current">'.$pl.translate($m['name'],false)."</span> \n";
  }
  // Добавяне на * за редактиране
  if (in_edit_mode()){
    $rz .= '<a href="'.$pth.'mod/usermenu/edit_menu_link.php?pid='.$page_id.'&amp;id='.$m['ID'].
           '"  style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
  }
  if ($sm1){ $sm .= $sm1; }  $si++;
}
if (in_edit_mode()){
  $ni = db_table_field('MAX(`ID`)','menu_items','1')+1;
  $rz .= "id $i ".'<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$i.'&link='.$page_id.
         '&name=p'.$ni.'_link" style="font-size:80%">New Item</a> '."\n";
}
if ($rz) $rz = "\n$sm<div id=\"$id\">\n".translate('menu_start')."$rz</div>\n";
return $rz;
}

//
// Тази функция определяне дали линкът от менюто, сочещ към страница 
// $mlk, да се покаже като текуща или не.

function is_parrent_menu($i, $mlk){
global $page_data, $ind_fl;
  // Ако $mlk е текущата страница - истина.
  if ($mlk==$page_data['ID']) return true;
  // Ако $mlk не е число, а друг линк
  if (!(1*$mlk)) return html_entity_decode($mlk) == $_SERVER['REQUEST_URI'];
  // Ако $i е номер на менюто на текущата страница не търси повече - неистина
  // защото менюто на текущата страница, се обхожда цялото и се стига до текущата страница.
  if ($i==$page_data['menu_group']) return false;
  // Ако $i не номер на менюто на текущата страница,
  // се проверява дали текущата страница не в дървото от страници на меню $i
  return is_subpage_of($mlk);
}

//
// Проверява дали текущата страница е подстраница от раздела, в който е страница с номер $i.
// Функцията е рекурсивна. Флагът $frst е true при извикване на функцията отвън, но при рекурсивното
// й извикване се задава да е false.

function is_subpage_of($i, $frst = true){//   echo "==$i==<br>";
global $page_id;
  // За запомняне кои менюта са проверени
  static $chm = array();
  // За запомняне кои страници са проверени
  static $chp = array();
  // Инициализиране на $chm и $chp
  if ($frst){ $chm = array(); $chp = array(); }

  // Ако страница $i е текуща, връща истина 
  if ($i==$page_id) return true;

  // Ако страницата не е проверена се проверява
  if (!in_array($i,$chp)){
     // Добавяне към проверените страници
     $chp[] = $i;
//     echo "Проверени страници: ".print_r($chp,true)."<br>";
     // Номер на менюто, към което принадлежи страницата с номер $i 
     $ip = db_table_field('menu_group','pages',"`ID`=$i");
     // Ако страницата няма меню, връща неистина
     if ($ip==0) return false;
     // Ако това меню още не е проверено, се проверява
     if ($ip && !in_array($ip,$chm))
     {
        // Добавяне към проверените менюта
        $chm[] = $ip;
//        echo "Проверени менюта: ".print_r($chm,true)."<br>";
        // Страници с меню $ip
        $pgs = db_select_m('ID','pages',"`menu_group`=$ip");
//        echo "--$ip ".print_r($pgs,true)."<br>";
        foreach($pgs as $pg){
          // Проверява се рекурсивно
          $y = is_subpage_of($pg['ID'], false);
          if ($y) return true;
        }
     }
     // Подменюта, които има за родител меню $ip
     $ms = db_select_m('`group`','menu_tree',"`parent`=$ip");
//     echo "Подменюта: ".print_r($ms,true)."<br>";
     foreach($ms as $m){
        // Добавяне към проверените менюта
        $chm[] = $m['group'];
        // Страници с меню $m['group']
        $pgs = db_select_m('ID','pages',"`menu_group`=".$m['group']);
//        echo "--".$m['group']."--".print_r($pgs,true)."<br>";
        foreach($pgs as $pg){
          // Проверява се рекурсивно
          $y = is_subpage_of($pg['ID'], false);
          if ($y) return true;
        }
     }
  }
//  echo "-$i-<br>";
  return false;
}

//
// Изскачащо подменю на меню с номер $i, към линк със запис $m
//
function submenu($m,$si){
// Четене номера на меню, което има за родител $m
$sm = db_select_1('*','menu_tree','`parent`='.$m['group'].' AND `index_page`='.$m['link']);
if (!$sm) return '';
// Четене на линковете от намереното меню
$sd = db_select_m('*','menu_items','`group`='.$sm['group'].' ORDER BY `place` ASC');
// Съставяне на изскачащото меню
$rz = "<div id=\"Layer$si\">\n";
foreach($sd as $d){
  $a = translate($d['name']);
  $rz .= "<a href=\"index.php?pid=".$d['link']."\">$a</a> \n";
}
$rz .= "</div>\n";
return $rz;
}

?>

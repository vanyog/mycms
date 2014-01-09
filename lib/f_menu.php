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

function menu($i, $id = 'page_menu'){
global $ind_fl, $adm_pth, $page_id, $page_data;
$d = db_select_m('*','menu_items',"`group`=$i ORDER BY `place`");
$rz = ''; // Връщания резултат
$once = false; // Флаг, който се използва за да се покаже само веднъж различно точката от менюто на текущата страница
$sm = ''; // Изскачащо подменю, ако има такова
$si = 1; // Номер на изскачащото подменю
$lk = stored_value('menu_aslink'); // Дали точката на текущата страница да се покаже като линк
$pp = stored_value('menu_popup'); // Дали да се показва изскачащо миню
foreach($d as $m){
  $lnn = 1*$m['link'];
  $ln = $m['link']; 
  if ($lnn) $ln = $ind_fl.'?pid='.$lnn;
  $pl = '';
  if (in_edit_mode()) $pl = $m['place'];
  $js = '';
  $sm1 = '';
  if ($pp && ($i==$page_data['menu_group'])) $sm1 = submenu($m,$si);
  if ($pp && ($i==$page_data['menu_group'])) $js = ' onMouseOver="show_layer('.$si.',this);"';
  if ($once || !is_parrent_menu($i, $m['link'])) {
     $rz .= "<a href=\"$ln\"$js>".$pl.translate($m['name']).'</a> '."\n";
  }
  else {
     $once = true;
     if ($lk) $rz .= '<a href="'.$ln.'" class="current"'.$js.'>'.$pl.translate($m['name']).'</a> '."\n";
     else $rz .= '<span class="current">'.$pl.translate($m['name'])."</span> \n";
  }
  if ($sm1){ $sm .= $sm1; }  $si++;
}
if (in_edit_mode()){
  $ni = db_table_field('MAX(`ID`)','menu_items','1')+1;
  $rz .= "id $i ".'<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$i.'&link='.$page_id.
         '&name=p'.$ni.'_link">New</a> '."\n";
}
if ($rz) $rz = "\n$sm<div id=\"$id\">\n$rz</div>\n";
return $rz;
}

//
// Тази функция проверява дали от меню с номер $i, номерът на страница $mlk,
// към която соми точка от това меню, или друга страница от същото разклонение
// на сайта е текуща. Ако е така връща истина. Шзползва се за определяне дали
// в менюто страницата да се покаже с линк или не.

function is_parrent_menu($i, $mlk){
global $page_data;
  // Ако страницата, към която сочи точката от менюто е текущата - истина
  if ($mlk==$page_data['ID']) return true;
  // Ако меню $i е менюто на текущата страница не търси повече - неистина
  if ($i==$page_data['menu_group']) return false;
  // Търсене в дървото от страници
//  echo "++$mlk++<br>";
  return is_subpage_of($mlk);
}

//
// Проверява дали текущата страница e подстраница на раздела, в който е страница с номер $i

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
// Изскачащо подменю
//
function submenu($m,$si){
// Четене номера на меню, което има за родител $m
$sm = db_select_1('*','menu_tree','`parent`='.$m['group'].' AND `index_page`='.$m['link']);
if (!$sm) return '';
// Четене на точките от намереното меню
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
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

// Функцията sitemap($i) генерира html код за показване карта на сайта.
// $i е номера на менюто на страницата, от която се разклонява картата,
// но може да съдържа и втори, отделен с | параметър,
// който се задава като id атрибут на <div> тага, в който се представя картата.
// Когато втори параметър не е зададен id="site_map".

// Ако е дефинирана глобална променлива $max_level, се показват само връзките до това ниво на вложеност.

include_once($idir.'lib/f_db_table_field.php');

global $smfile, $smday;

$page_passed = array(); // Номера на менюта, които вече са обработени,
                        // използва са за да не се получи зацикляне
$map_level = 0; // Ниво на рекурсията
$i_root    = 0; // Номер на входното меню
$id_pre    = ''; // Представка, с която започват id атрибутите на <div> елементите

$smday     = 0;
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml'))
       $smday = date('j', filemtime($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml') );
$smfile    = stored_value("today");

function sitemap($a = ''){
global $page_passed, $map_level, $i_root, $id_pre, $page_header, $smfile, $smday;
$page_passed = array();
$map_level = 0;
$i_root    = 0;
$ar = explode('|',$a);
if(!$ar[0]) $ar[0] = stored_value('main_index_pageid',1);
$page_header .= '<script><!--
function mapHideShow(e){
var p = e.parentElement;
var ls = document.links;
for(i=0;i<ls.length;i++) if(ls[i].parentElement==p) break;
var sl = window.getComputedStyle(ls[i]);
var h = p.style.height;
var sp = window.getComputedStyle(p);
var v = "'.stored_value('sitemap_colapsed_height', '1.45em').'";
var lh = sp.lineHeight.slice(0,-2);
if(lh=="norm") lh = Number(sp.fontSize.slice(0,-2))  * 1.5;
else lh = Number(lh);
var v = ( lh
          + Number(sl.paddingTop.slice(0,-2))
          + Number(sl.paddingBottom.slice(0,-2))
        ) + "px";
if (h!=v){
  e.innerHTML = "&#9658;"
  p.style.height = v;
  p.style.overflow = "hidden";
}
else {
  e.innerHTML = "&#9660;"
  p.style.height = "auto";
  p.style.overflow = "visible";
}
}
--></script>';
$id_pre = 'map'.$ar[0];
$id = 'site_map';
if (isset($ar[1])) $id = $ar[1];
$rz = '<div id="'.$id.'">'."\n".sitemap_rec($ar[0], 1)."
<p class=\"clear\"></p></div>\n";
if($smday != $smfile){
   $smfile = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
             '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".
             $smfile.
             "</urlset>";
   file_put_contents($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml', $smfile);
}
return $rz;
}

//
// Рекурсивна функция, която съставя картата

function sitemap_rec($i, $j){
global $pth, $page_passed, $map_level, $max_level, $i_root, $ind_fl, $id_pre, $page_id, $smfile, $smday;
if(!isset($max_level)) $max_level = 100;

$page_passed[] = $i;

$count = 1; // Номер на поредната връзка

$rz = "\n"; 
if (!$i_root) $i_root = $i;

// Извличат се данни за хипервръзките от меню $i
$mi = db_select_m('*', 'menu_items', "`group`=$i ORDER BY `place`");

// Прочита се номера на главната страница на менюто
$index = db_table_field('index_page','menu_tree',"`group`=$i");

// Цикъл за обработка на всяка хипервръзка от менюто $i
foreach($mi as $m){
  $rz .= '<div id="map'.$m['ID']."\">\n";
  $rz1 = '';
  $rz2 = '';
  $h = false;
  
  // Номер на страницата от поредния линк
  if(is_numeric($m['link'])) $pid = 1*$m['link'];
  else $pid = 0;
  $p['hidden'] = 0;
  if($pid){
     // Извличат се данните за страницата
     $p = db_select_1('*','pages','`ID`='.$pid);
  }
  if (($i==$i_root)||($pid!=$index))
  {
    $lk = $m['link'];
    if ($pid){
       $lk = $ind_fl.'?pid='.$pid;
       if(isset($_GET['template'])){
          $t = 1*$_GET['template'];
          $at = stored_value('allowed_templates');
          if(!(strpos($at, ",$t,")===false)) $lk .= "&template=$t";
       }
    }
//    if ($pid!=$page_id)
    {
       $h = $p['hidden'];
       if( !$h || in_edit_mode() ){
          if($smday != $smfile)
             $smfile .= "<url>\n".
                        "<loc>http://".$_SERVER['HTTP_HOST']."$lk</loc>\n".
                        "<lastmod>".date("Y-m-d")."</lastmod>\n".
                        "<changefreq>monthly</changefreq>\n".
                        "<priority>".(1-0.2*$map_level)."</priority>\n".
                        "</url>\n";
          $rz1 .= '<a href="'.$lk.'">'.translate($m['name']).'</a>';
          if( in_edit_mode() ) {
             if ($h) $rz .= 'hidden ';
             $rz .= $m['place'];
          }
          $rz1 .= "<br>\n";
       }
    }
    $count++;
  }

  if ($pid) { // Обработва се ако е страница с номер от настоящата CMS
    if ($p['menu_group']!=$i){ // Ако е страница от друго меню
      $mtd = db_select_1('parent,index_page', 'menu_tree', '`group`='.$p['menu_group']);
      // Рекурсивно извикване за получаване карта на подменюто
      if ( !in_array($p['menu_group'],$page_passed) && ($p['ID']==$mtd['index_page']) && ($i==$mtd['parent']) ){
        $map_level++;
        if ($map_level<$max_level){
           if(in_edit_mode() || !$h) $rz1 .= sitemap_rec($p['menu_group'], $count);
        }
        else $rz1 .= '...';
        $map_level--;
        $rz2 = '<span onclick="mapHideShow(this);" class="bullet">&#9660;</span>&nbsp;';
      }
    }
  }
  if ($rz1) $rz1 = $rz2.$rz1;
  $rz .= $rz1;
  if ($count>1) $rz .= "</div>\n";
  else $rz = '';

} // Край на цикъла за обработка на всяка хипервръзка от менюто $i

return $rz;

}

?>

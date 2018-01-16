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

// ��������� sitemap($i) �������� html ��� �� ��������� ����� �� �����.
// $i � ������ �� ������ �� ����������, �� ����� �� ���������� �������,
// �� ���� �� ������� � �����, ������� � | ���������,
// ����� �� ������ ���� id ������� �� <div> ����, � ����� �� ��������� �������.
// ������ ����� ��������� �� � ������� id="site_map".

// ��� � ���������� �������� ���������� $max_level, �� �������� ���� �������� �� ���� ���� �� ���������.

include_once($idir.'lib/f_db_table_field.php');

$page_passed = array(); // ������ �� ������, ����� ���� �� ����������,
                        // �������� �� �� �� �� �� ������ ���������
$map_level = 0; // ���� �� ����������
$i_root    = 0; // ����� �� �������� ����
$id_pre    = ''; // ����������, � ����� �������� id ���������� �� <div> ����������

function sitemap($a = ''){
global $page_passed, $map_level, $i_root, $id_pre, $page_header;
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
var v = ( Number(sp.lineHeight.slice(0,-2)) +
          Number(sl.paddingTop.slice(0,-2)) +
          Number(sl.paddingBottom.slice(0,-2))
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
return '<div id="'.$id.'">'."\n".sitemap_rec($ar[0], 1)."
<p class=\"clear\"></p></div>\n";
}

//
// ���������� �������, ����� ������� �������

function sitemap_rec($i, $j){
global $pth, $page_passed, $map_level, $max_level, $i_root, $ind_fl, $id_pre, $page_id;
if(!isset($max_level)) $max_level = 100;

$page_passed[] = $i;

$count = 1; // ����� �� ��������� ������

$rz = "\n"; 
if (!$i_root) $i_root = $i;

// �������� �� ����� �� ������������� �� ���� $i
$mi = db_select_m('*', 'menu_items', "`group`=$i ORDER BY `place`");

// ������� �� ������ �� �������� �������� �� ������
$index = db_table_field('index_page','menu_tree',"`group`=$i");

// ����� �� ��������� �� ����� ����������� �� ������ $i
foreach($mi as $m){// die(print_r($m,true));
  $rz .= '<div id="map'.$m['ID']."\">\n";
  $rz1 = '';
  $rz2 = '';
  $h = false;
  
  // ����� �� ���������� �� �������� ����
  if(is_numeric($m['link'])) $pid = 1*$m['link'];
  else $pid = 0;
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
    if ($pid!=$page_id){
       $h = db_table_field('hidden', 'pages', "`ID`=".$pid);
       if( !$h || in_edit_mode() ){
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

  if ($pid) { // ��������� �� ��� � �������� � ����� �� ���������� CMS

    // �������� �� ������� �� ����������
    $p = db_select_1('*','pages','`ID`='.$pid);

    if ($p['menu_group']!=$i){ // ��� � �������� �� ����� ����
      // ���������� ��������� �� ���������� ����� �� ���������
      if (!in_array($p['menu_group'],$page_passed)){
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

} // ���� �� ������ �� ��������� �� ����� ����������� �� ������ $i

return $rz;

}

?>

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

// ��������� menu($i) ������� ���������������� �� ����������� (����).
// $i � ����� �� ������� ����������� �� ������� $tb_preffix.'menu_items'.
// ��� � �������� ����� ��������� $id ��� �� ����� �� id ������� �� <div> ��������,
// � ����� �� ������ ������������� �� ������.

include_once($idir."lib/f_is_local.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');

function menu($i, $id = 'page_menu'){
global $ind_fl, $adm_pth, $page_id, $page_data, $pth;
$d = db_select_m('*','menu_items',"`group`=$i ORDER BY `place`");
$rz = ''; // �������� ��������
$once = false; // ����, ����� �� �������� �� �� �� ������ ���� ������ �������� ����� �� ������ �� �������� ��������
$sm = ''; // ��������� �������, ��� ��� ������
$si = 1; // ����� �� ����������� �������
$lk = stored_value('menu_aslink'); // ���� ������ �� �������� �������� �� �� ������ ���� ����
$pp = stored_value('menu_popup');  // ���� �� �� �������� ��������� ���������
$hm = stored_value('menu_hide',1); // ���� �� �� ������� ������� ��� ������ ��������
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
  // �������� �� * �� �����������
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
// ���� ������� ���������� ���� ������ �� ������, ����� ��� �������� 
// $mlk, �� �� ������ ���� ������ ��� ��.

function is_parrent_menu($i, $mlk){
global $page_data, $ind_fl;
  // ��� $mlk � �������� �������� - ������.
  if ($mlk==$page_data['ID']) return true;
  // ��� $mlk �� � �����, � ���� ����
  if (!(1*$mlk)) return html_entity_decode($mlk) == $_SERVER['REQUEST_URI'];
  // ��� $i � ����� �� ������ �� �������� �������� �� ����� ������ - ��������
  // ������ ������ �� �������� ��������, �� ������� ������ � �� ����� �� �������� ��������.
  if ($i==$page_data['menu_group']) return false;
  // ��� $i �� ����� �� ������ �� �������� ��������,
  // �� ��������� ���� �������� �������� �� � ������� �� �������� �� ���� $i
  return is_subpage_of($mlk);
}

//
// ��������� ���� �������� �������� � ����������� �� �������, � ����� � �������� � ����� $i.
// ��������� � ����������. ������ $frst � true ��� ��������� �� ��������� �����, �� ��� ������������
// � ��������� �� ������ �� � false.

function is_subpage_of($i, $frst = true){//   echo "==$i==<br>";
global $page_id;
  // �� ��������� ��� ������ �� ���������
  static $chm = array();
  // �� ��������� ��� �������� �� ���������
  static $chp = array();
  // �������������� �� $chm � $chp
  if ($frst){ $chm = array(); $chp = array(); }

  // ��� �������� $i � ������, ����� ������ 
  if ($i==$page_id) return true;

  // ��� ���������� �� � ��������� �� ���������
  if (!in_array($i,$chp)){
     // �������� ��� ����������� ��������
     $chp[] = $i;
//     echo "��������� ��������: ".print_r($chp,true)."<br>";
     // ����� �� ������, ��� ����� ���������� ���������� � ����� $i 
     $ip = db_table_field('menu_group','pages',"`ID`=$i");
     // ��� ���������� ���� ����, ����� ��������
     if ($ip==0) return false;
     // ��� ���� ���� ��� �� � ���������, �� ���������
     if ($ip && !in_array($ip,$chm))
     {
        // �������� ��� ����������� ������
        $chm[] = $ip;
//        echo "��������� ������: ".print_r($chm,true)."<br>";
        // �������� � ���� $ip
        $pgs = db_select_m('ID','pages',"`menu_group`=$ip");
//        echo "--$ip ".print_r($pgs,true)."<br>";
        foreach($pgs as $pg){
          // ��������� �� ����������
          $y = is_subpage_of($pg['ID'], false);
          if ($y) return true;
        }
     }
     // ���������, ����� ��� �� ������� ���� $ip
     $ms = db_select_m('`group`','menu_tree',"`parent`=$ip");
//     echo "���������: ".print_r($ms,true)."<br>";
     foreach($ms as $m){
        // �������� ��� ����������� ������
        $chm[] = $m['group'];
        // �������� � ���� $m['group']
        $pgs = db_select_m('ID','pages',"`menu_group`=".$m['group']);
//        echo "--".$m['group']."--".print_r($pgs,true)."<br>";
        foreach($pgs as $pg){
          // ��������� �� ����������
          $y = is_subpage_of($pg['ID'], false);
          if ($y) return true;
        }
     }
  }
//  echo "-$i-<br>";
  return false;
}

//
// ��������� ������� �� ���� � ����� $i, ��� ���� ��� ����� $m
//
function submenu($m,$si){
// ������ ������ �� ����, ����� ��� �� ������� $m
$sm = db_select_1('*','menu_tree','`parent`='.$m['group'].' AND `index_page`='.$m['link']);
if (!$sm) return '';
// ������ �� ��������� �� ���������� ����
$sd = db_select_m('*','menu_items','`group`='.$sm['group'].' ORDER BY `place` ASC');
// ��������� �� ����������� ����
$rz = "<div id=\"Layer$si\">\n";
foreach($sd as $d){
  $a = translate($d['name']);
  $rz .= "<a href=\"index.php?pid=".$d['link']."\">$a</a> \n";
}
$rz .= "</div>\n";
return $rz;
}

?>

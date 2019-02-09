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

// ������� �� ��������� �� ������������, ������ ����.
// ������� �� ������ �� ����� �� ������� 'menu_items'.
// ����������� $a � ���������� �� ������ `group` � ������� 'menu_items'.

include_once($idir.'mod/rawfile/f_rawfile.php');

function hmenu($a){
global $ind_fl, $page_header, $pth, $adm_pth, $page_id, $seo_names, $rewrite_on;
$p = current_pth(__FILE__);
$page_header .= '<script>'."\n";
// �������
$c1 = stored_value('hmenu_color1');  $page_header .= "var color1 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor1'); $page_header .= "var bcolor1 = \"$c1\";\n";
$c1 = stored_value('hmenu_color2');  $page_header .= "var color2 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor2'); $page_header .= "var bcolor2 = \"$c1\";\n";
$c1 = stored_value('hmenu_color3');  $page_header .= "var color3 = \"$c1\";\n";
$c1 = stored_value('hmenu_bcolor3'); $page_header .= "var bcolor3 = \"$c1\";\n";
$page_header .= rawfile('mod/hmenu/functions.js');
$page_header .= "\n</script>\n";
// ������ �� �������� �� ������
$il = db_select_m('*','menu_items',"`group`=$a ORDER BY `place` ASC");
$sm = ''; // HTML ��� �� �����������
$rz = ''; // HTML ��� �� ��������� �� ������
$ci = hmenu_c($a);
//$ia = index_array();
$j = 1;
foreach($il as $i){
  $lk = 1*$i['link'];
  $c = '';
  if ($lk) {
    $sm .= hsubmenu($lk,/*$ia,*/$j);
//    if (in_array($lk,$ia)) $c = ' class="current"';
    if ($lk==$ci) $c = ' class="current"';
    if($seo_names) $lk = '/'.db_table_field('seo_name', 'seo_names', "`page_id`='$lk'").'/';
    else if($rewrite_on) $lk = "/$lk/";
         else $lk = $ind_fl.'?pid='.$lk;
  }
  else $lk = $i['link'];
  $rz .= '<a href="'.$lk.'"'.$c.' onMouseOver="show_hlayer('.$j.',this)" onmouseleave="hide_layer2('.$j.', this, event);">'.
         translate($i['name'],false);
  // �������� �� * �� ����������� 
  if (in_edit_mode()){
     $rz .= '<a href="'.$pth.'mod/usermenu/edit_menu_link.php?pid='.$page_id.'&amp;id='.$i['ID'].
     '" style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
  }
  $rz .= "</a>\n";
  $j++;
}
if (in_edit_mode()){
  $ni = db_table_field('MAX(`ID`)','menu_items','1')+1;
  $rz .= " $a ".'<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$a.'&link='.$page_id.'&name=p'.$ni.'_link">New</a> '."\n";
}
return $sm.'<div id="menu_'.$a."\">\n".$rz.'</div>';
}

// ����� ��������� ��� ���� $lk

function hsubmenu($lk,/*$ia,*/ $j){
global $ind_fl, $seo_names, $rewrite_on;
// ����� �� ������, �� ����� �������� $lk � ������
$g = db_table_field('`group`','menu_tree',"`index_page`=$lk");
$ci = hmenu_c($g);
// �������� �� ���� ����
$da = db_select_m('*','menu_items',"`group`=$g ORDER BY `place` ASC");
$rz = '';
if (count($da)>1) foreach($da as $d){
  if(is_numeric($lk)) $lk = 1*$d['link'];
  $ar = array();
  if(!$lk){
    if(preg_match_all('/pid=(\d+)/', $d['link'], $ar))
      { $lk = $ar[1][0]; }
  }
  $c = '';
  if ($lk) {
    // �������� ���� ���������� � ������
    $h = db_table_field('hidden', 'pages', "`ID`=$lk") && !in_edit_mode();
    if ($lk==$ci) $c = ' class="current"';
//    if (in_array($lk,$ia)) $c = ' class="current"';
    if(count($ar)) $lk = $d['link'];
    else if($seo_names) $lk = '/'.db_table_field('seo_name', 'seo_names', "`page_id`='$lk'").'/';
         else if($rewrite_on) $lk = "/$lk/";
         else $lk = $ind_fl.'?pid='.$lk;
  }
  else $lk = $d['link'];
  if(!$h){
    $pl = '';
    if(in_edit_mode()) $pl = $d['place']." ";
    $rz .= '<a href="'.$lk.'"'.$c.'>'.$pl.translate($d['name'], false)."</a>\n";
  }
}
if (in_edit_mode()) $rz .= 'id '.$g;
if ($rz) $rz = '<div id="HLayer'.$j.'" onmouseleave="hide_layer('.$j.',this);">'."\n".$rz."</div>\n";
return $rz;
}

// ����� ����� �� ������ �� �������� �������� �� �����������, � ����� ������� �������� ��������.
// �������� �� �� ���������� ���� ����� ���� � ���� ���� ��� �������, � ����� ������� �������� ��������, 
// �� �� �� ������ ���� ���� � ���� ����.

function index_array(){
global $page_id;
$rz = array($page_id=>0);
// ������ �������� �� �������� �� ������ ������� ��� �������� ��������
$da = db_select_m('`group`','menu_items',"`link`='$page_id'");
foreach($da as $d){
 $g = $d['group'];
 // ������ ��������� �� ������� ����� � ����������� � ������ �����
 $c = array($g);
 do {
   $t = db_select_1('parent,index_page','menu_tree',"`group`=$g");
   $g = $t['parent'];
   $rz[$t['index_page']] = 0;
   if (in_array($g, $c)) { // ���������� � ���������
     // ��������� �� ���������� �����
     store_value('hmenu_error', 'page:'.$t['index_page'].' group:'.$g.' parent:'.$t['parent']);
     // ������������ �� ������
     break;
   }
   $c[] = $g;
 } while ($g);
}
return array_keys($rz);
}

// ���� �� ��������� �� ������� �� ��������� �� ��� ���� �� ���� � ����� $m �� ����� �� �������� ��������

function hmenu_c($m){
global $page_data;

// ����� � ������������ ������. �������� �� �� ��������� �� ���������
$pa = array();

// ������ ��������
$pd = $page_data;

// ����� �� �������� ��������
$gr = $pd['menu_group'];

$c = 0;
do { //echo "$m $gr - ".$pd['ID']."<br>";

  // ��� � ����
  if($gr==0) return 0;

  // ��� �������� �������� � �� ���� $m
  if($m == $gr) return $pd['ID'];

  // �����(�) �� ������� �� �������� �������� �� ������� menu_tree
  $mt = db_select_m('*', 'menu_tree', "`group`=".$gr); //echo print_r($mt,true)."<br>";
  $c = count($mt);
  // ��� ���� ����� �������� �������� �� ���������� �� ������� �����
  if(!$c) return 0;
  // ��� ������ �� ���� ����� - ������� ������ � ���������
  if($c>1) die("More than one record for $m group in menu_tree table!");

  // ������ ����� �������� �������� �� �������
  $pd = db_select_1('*', 'pages', "`ID`=".$mt[0]['index_page']);

  // ����������� ��� ������������ ����
  $gr = $mt[0]['parent'];

  // �������� �� ���������
  if(in_array($gr, $pa)) die("Loop structue in menu system");
  $pa[] = $gr;

  $c++;
  if($c>20) die("Infinit Loop");
} while (true);

}

?>

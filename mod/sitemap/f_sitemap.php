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

include_once($idir.'lib/f_db_table_field.php');

$page_passed = array(); // ������ �� ������, ����� ���� �� ����������,
                        // �������� �� �� �� �� �� ������ ���������
$map_lavel = 0; // ���� �� ����������
$i_root    = 0; // ����� �� �������� ����
$id_pre    = ''; // ����������, � ����� �������� id ���������� �� <div> ����������

function sitemap($a){
global $page_passed, $map_lavel, $i_root, $id_pre;
$page_passed = array();
$map_lavel = 0;
$i_root    = 0;
$ar = explode('|',$a);
$id_pre = 'map'.$ar[0];
$id = 'site_map';
if (isset($ar[1])) $id = $ar[1];
return '<div id="'.$id.'">'."\n".sitemap_rec($ar[0])."
<p class=\"clear\"></p></div>\n";
}

function sitemap_rec($i){
global $pth, $page_passed, $map_lavel, $i_root, $ind_fl, $id_pre, $page_id;

$page_passed[] = $i;

$count = 1; // ����� �� ��������� ������

$rz = "\n"; 
$ind = (40*$map_lavel).'px';
if (!$i_root) $i_root = $i;

// �������� �� ����� �� ������������� �� ���� $i
$mi = db_select_m('*', 'menu_items', "`group`=$i ORDER BY `place`");

// ������� �� ������ �� �������� �������� �� ������
$index = db_table_field('index_page','menu_tree',"`group`=$i");

// ����� �� ��������� �� ����� ����������� �� ������ $i
foreach($mi as $m){
  $rz .= "<div id=\"$id_pre"."_$map_lavel"."_$count\">";
  
  // ��������� �� ����������� �� �������
  $pid = 1*$m['link'];
  if (($i==$i_root)||($pid!=$index))
  { 
    $lk = $m['link'];
    if ($pid) $lk = $ind_fl.'?pid='.$pid;
//    $rz .= '<span style="padding-left:'.$ind.'"><a href="'.$lk.'">'.translate($m['name']).'</a></span><br>'."\n";
    if ($pid!=$page_id){
       $rz .= '<a href="'.$lk.'">'.translate($m['name']).'</a>';
       if (in_edit_mode() && db_table_field('hidden', 'pages', "`ID`=".$pid)) $rz .= ' hiddeh';
       $rz .= "<br>\n";
    }
    $count++;
  }

  if ($pid) { // ��������� �� ��� � �������� � ����� �� ���������� CMS

    // �������� �� ������� �� ����������
    $p = db_select_1('*','pages','`ID`='.$pid);

    if ($p['menu_group']!=$i){ // ��� � �������� �� ����� ����
      // ���������� ��������� �� ���������� ����� �� ���������
      if (!in_array($p['menu_group'],$page_passed)){
        $map_lavel++;
        $rz .= sitemap_rec($p['menu_group']);
        $map_lavel--;
      }
    }
  }
  $rz .= '</div>';

} // ���� �� ������ �� ��������� �� ����� ����������� �� ������ $i

return $rz."\n";

}

?>

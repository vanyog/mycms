<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

//
// �������� ������� � ������� ���������� �� ��������.
// ����������, ���������� � ������ <h1>, <h2> � �.�. � ������� $s, �� �������� � �� �� �������� �������� id ��������.
// �� ������� �� ������� TOFCONTENTS �� ������ ��������� ���������� - ������� ��� ���������� ��������.

global $h_id, $tof_contents;

include_once($idir.'lib/f_add_style.php');

function toc($s){
// ��� � $s ���� ��������� 'TOFCONTENTS', �� ����� $s ��� �������
if(strpos($s, 'TOFCONTENTS')===false) return $s;
global $h_id, $tof_contents, $page_id, $adm_pth;
add_style('filter_toc');
$h_id = 0; // ������� ����� �� ��������, ����� �� �������� � id ��������, ������� � ����������
$tof_contents = ''; // Html ���� �� ����������� ����������
// ����������� �� ������� ��������.
// ����� �������� �������� �� ��������� � ��������� toc_cb()
$s = preg_replace_callback('/<h(\d+)\s*(id=".+?")*>(.*?)<\/h\1>/s', 'toc_cb', $s);
// ��� � �������� ���� 1 ��� ���� ���� ��������,
// ������������ � ������
if($h_id<2) $tof_contents = '';
// ��� ������ �������� �������� �� ������� <div> ����
else {
     $tf = "<div id=\"toc\"><div>\n".
           '<h2>'.translate('filtertoc_toc')."</h2>\n<div>\n";
     // ���� "���������� �� ������������" �� ������� ��� � ����������� � �������� 
     // �� �� ������� �������� �� ����� ��� �� ����������� ��������
     $y1 = stored_value('filter_toc', false);
     $y2 = stored_value('filter_toc_'.$page_id, false);
     if($y1 || $y2){
        $tf .= '<p><a href="#toc_end">'.translate('filtertoc_skiptoc');
        if(in_edit_mode()){
           $i = 0;
           if($y1!==false) $i = db_table_field('ID', 'options', "`name`=filter_toc", 0);
           else $i = db_table_field('ID', 'options', "`name`='filter_toc_".$page_id."'", 0, false);
           $tf .= ' <a href="'.$adm_pth.'edit_record.php?t=options&r='.$i.'">*</p>';
        }
        $tf .= "</a></p>\n";
     }
     else if(in_edit_mode()){
        $i = 0;
        if($y1!==false) $i = db_table_field('ID', 'options', "`name`=filter_toc", 0);
        else $i = db_table_field('ID', 'options', "`name`='filter_toc_".$page_id."'", 0, false);
        if($i) $tf .= ' <a href="'.$adm_pth.'edit_record.php?t=options&r='.$i.'">Edit skip content Link</p>';
        else $tf .= '<a href="'.$adm_pth.'new_record.php?t=options&name='.
                    'filter_toc_'.$page_id.'&value=1'.
                    '">Add skip content link</p>';
     }
     $tof_contents = $tf.$tof_contents."</div>\n<span id=\"toc_end\"></span></div>\n</div>\n";
     // �������� �� ����� �� ������� � ������������
     if(!empty($GLOBALS['filter_toc_link'])){
        $is = current_pth(__FILE__).'toc-button.svg';
        $tof_contents .= '<a href="#toc"><img alt="Go to TOC" src="'.$is.
                         '" title="'.translate('filter_toc_linktitle',false).'" id="toc-image"></a>'."\n";
     }
}
// ���������� �� ��������� 'TOFCONTENTS' ��� ����������� ����������
$s = str_replace('TOFCONTENTS', $tof_contents, $s);
return $s;
}

// ��������� �� ��������� �������� ��������

function toc_cb($a){
  global $h_id, $tof_contents;
  $h_id++;
//  print_r($a); die;
  $id = "ct$h_id"; // html ������� id
  if(!$a[2]){ // ��� ���������� ���� ����� html ��������, �� �� ������ ������� id
    $a[2] = 'id="ct'.$h_id.'"';
  }
  else {
    // ������� �� ������� id �������
    $m = array();
    preg_match_all('/id="(.*)"/', $a[2], $m);
    // ��� ���������� ��� id �������, �� �������� ���
    if(isset($m[1][0])) $id = $m[1][0];
  }
  // ���� ��� ����������, ����� �� ������ � ������������
  $tof_contents .= '<a href="#'.$id.'" class="lev'.$a[1].'">'.strip_tags($a[3])."</a>\n";
  // ������� �� ����������� �������� � ������, ����� �������� ������������
  return '<h'.$a[1].' '.$a[2].'>'.$a[3].'</h'.$a[1].'>';
}

?>

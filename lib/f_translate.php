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

// ��������� translate($n,$elink) ����� ����� � ��� $n �� �����, ��������� � ���������� ���������� $language.

// ��� � ������� `content` �� ������ ����� ���� ����� �����,
// �� ������� ������ ��� � ����� �� ����������� 
// �� ������� �� ������ �� ������� ����������� ��� �������� �� �������� �� �����,
// �� �� ������ ������ � ������� �����, ��������� ����� ����� �� ������������� �� ����.

// ��� ������� ��������� $elink = false, � ����� �� ����������� � ���� �� ������� �� �� ������� ���� �� �����������,
// ����� ���� ���� �� �� ������� ��� ������ �� ������ �� ������� nolink � 1.

include_once($idir."conf_paths.php");
include_once($idir."lib/f_is_local.php");
include_once($idir."lib/f_db_select_1.php");
include_once($idir."lib/f_parse_content.php");

$content_date_time    = '';// ����������, ����� ������� ������ � ���� �� ���������� �������� �� �������� ����� 
$content_create_time = ''; // ����������, ����� ������� ������ � ���� �� ������� ��������� �� �������� ����� 

function translate($n, $elink=true){

// �������� ���������� �� ���
static $string = array();
// ��� �������� ���� � �������� �� ����� �� ����
if (isset($string[$n]) && !in_edit_mode()) return $string[$n];

global $language, $pth, $adm_pth, $default_language, $content_date_time, $content_create_time, $can_edit, $page_data;

$content_date_time = '';
$content_create_time = '';

$el = ''; // ���� �� �����������. ������� �� ��� ������ � � ����� �� �����������.
if (in_edit_mode() && $elink){
  $id = db_select_1('ID','content',"name='$n' AND language='$language'");
  if ($can_edit) $h = $pth.'mod/usermenu/edit_text.php?i='.$id['ID'].'&amp;pid='.$page_data['ID'];
  else $h = $adm_pth.'edit_record.php?t=content&amp;r='.$id['ID'];
  $el = '<a href="'.$h.'" style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
}

// ������ ��������
$rz = ''; 

// ������ �� ������ �� ������ � ��� $n �� ���� $language
$r = db_select_1('*','content',"`name`='$n' AND `language`='$language'");
if ($r){ // ��� ��� ����� �����
  $content_create_time = $r['date_time_1']; 
  $content_date_time = $r['date_time_2'];
  $t = stripslashes($r['text']);
  $rz = apply_filters($n,parse_content($t));
  if ((!isset($r['nolink']) || !$r['nolink']) && $elink) $rz .= $el;
}
else if (/*is_local() ||*/ in_edit_mode()){
         // �� ������� ������ ��� � ����� �� ����������� �� ������� ����� �� ������� ���� ����,
         // ����� ������ ����� �� ��������� �� ��������� ������
         if ($can_edit) $h = $pth.'mod/usermenu/edit_text.php?i='.$n.
             '&amp;lang='.$language.
             '&amp;pid='.$page_data['ID'];
         else $h = $adm_pth."new_content.php?n=$n&l=$language";
         return "<a href=\"$h\">$n</a>";
       }
       else { // �� ��������� ������ � ������� �����
         // ������ �� ������ �� ����� �� ������������
         $r = db_select_1('*','content',"`name`='$n' AND `language`='$default_language'");
         // ��� ���� ����� �� ������� ����� �� ������
         if ( !$r ) $r['text'] = $n; 
         else {
           $content_create_time = $r['date_time_1'];
           $content_date_time = $r['date_time_2'];
         }
         $t = stripslashes($r['text']);
         // ��������� �� ��� ���������� ���������� <!--$$_XXX_$$--> ��������
         $rz = apply_filters($n,parse_content($t));
       }
// ��������� � ���
$string[$n] = $rz;

// ������� �� ���������
return $rz;

} // ���� �� ��������� translate($n)

// ��������� apply_filters($n, $t) ������� ����� ������ $t, ������������ �� ������ � ��� $n ������

function apply_filters($n, $t){
global $idir, $adm_pth;
$rz = $t; // ������ ��������
// ������ �� ������� �� ����� �� ������, ����� �� �������� ����� ������
$fl = db_select_1('*', 'filters', "`name`='$n'");
// ����� �� ����� �� ������
$fla = array();
if ($fl) $fla = explode(',', $fl['filters']);
// ��������� �� ������� �� ������      
foreach($fla as $fln){
  $flp = "filter/$fln/$fln.php"; // ��� �� ����� �� ������� �� ������������ �� �����
  $afp = "$idir$flp"; // ��������� ��� �� ����� �� �������
  if (file_exists($afp)){ // ��� ��� ����� ������
//    print_r($fl); die;
    include_once($afp);
    if(isset($fl['param']) && ($fl['param']>" ")) $rz = $fln($rz, $fl['param']);
    else $rz = $fln($rz);
  }
  else if (show_adm_links()) $rz .= '<p><br>Unknown fliter <a href="'.$adm_pth.'new_filter.php?f='.$fln.'">'.$fln.'</a><p>';
}
return $rz;
} 

?>

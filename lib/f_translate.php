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

// ��� ������� ��������� � $debu = true; �� �������� SQL ��������

include_once($idir."conf_paths.php");
include_once($idir."lib/f_is_local.php");
include_once($idir."lib/f_db_select_1.php");
include_once($idir."lib/f_parse_content.php");

$content_date_time    = '';// ����������, ����� ������� ������ � ���� �� ���������� �������� �� �������� ����� 
$content_create_time = ''; // ����������, ����� ������� ������ � ���� �� ������� ��������� �� �������� ����� 
// �������� ������, �������� CONTENT �� ������� scripts �� �������� ����� ������������ �� ����� ��������.
$global_filters = db_table_field('filters', 'filters', "`name` LIKE '*'");

function translate($n, $elink=true, $debug=false){// print_r(debug_backtrace()); die;

if(empty($n)) return '';

global $languages, $default_language, $language, $pth, $adm_pth, $content_date_time, $content_create_time, $can_edit,
       $page_content, $page_data, $debug_mode, $tn_prefix;

// �������� ���������� �� ���
static $string = array();

$editMode = in_edit_mode() && (show_adm_links() || $can_edit);

// ��� �������� ���� � �������� �� ����� �� ����
if (isset($string[$n][$language]) && !$editMode) return $string[$n][$language];

//if( !empty($debug_mode) ) echo "$n ";

$content_date_time = '';
$content_create_time = '';

$el = ''; // ���� �� �����������. ������� �� ��� ������ � � ����� �� �����������.
if ($editMode && $elink){
  $id = db_select_1('ID','content',"`name`='$n' AND `language`='$language'",$debug);
  if($id){
    if ($can_edit && isset($page_data['ID']) && !show_adm_links()) 
         $h = $pth.'mod/usermenu/edit_text.php?i='.$id['ID'].'&amp;pid='.$page_data['ID'];
    else $h = $adm_pth.'edit_record.php?t=content&amp;r='.$id['ID'];
    $el = '<a href="'.$h.
          '" style="color:#000000;background-color:#ffffff;margin:0;padding:0;">*</a>';
  }
}

// ������ ��������
$rz = '';

// ������ �� ������ �� ������ � ��� $n �� ���� $language
$r1 = db_select_1('c.*, f.filters',
                  'content` c LEFT JOIN `'.$tn_prefix.'filters` f ON c.name=f.`name', "c.name='$n' AND `language`='$language'",
                  $debug);
//if(substr($n, 0, 6)=='Vladim') { var_dump($r1); die; }

if ($r1){ // ��� ��� ����� �����
  $content_create_time = $r1['date_time_1'];
  $content_date_time = $r1['date_time_2'];
  $t = isset($r1['text']) ? stripslashes($r1['text']) : '';
  $rz = apply_filters($r1['filters'], parse_content($t));
  if ((!isset($r1['nolink']) || !$r1['nolink']) && $elink) $rz .= $el;
}
else if ($editMode && $elink){
         // �� ������� ������ ��� � ����� �� ����������� �� ������� ����� �� ������� ���� ����,
         // ����� ������ ����� �� ��������� �� ��������� ������.
         if ($can_edit) $h = $pth.'mod/usermenu/edit_text.php?i='.$n.
             '&amp;lang='.$language.
             '&amp;pid='.(isset($page_data['ID']) ? $page_data['ID'] : '');
         else $h = $adm_pth."new_content.php?n=$n&l=$language";
         return "<a href=\"$h\">$n</a>";
     }
     else { // �� ��������� ������ � ������� �����
         // ��� ��� �� ����� �� ������������ � ��� ����� �����
         if(($language == $default_language) && count($languages)) {
             // ������ �� ���������� ����
             $r2 = db_select_1('c.*, f.filters', 'content` c LEFT JOIN `'.$tn_prefix.'filters` f ON c.name=f.`name', "c.name='$n'",$debug);
         }
         // ������ �� ������ �� ����� �� ������������
         else $r2 = db_select_1('c.*, f.filters', 'content` c LEFT JOIN `'.$tn_prefix.'filters` f ON c.name=f.`name', "c.name='$n' AND `language`='$default_language'",$debug);
         // ��� ���� ����� �� ������� ����� �� ������
         if ( !$r2 ){ $r2['text'] = $n; $r2['filters'] = ''; }
         else {
           $content_create_time = $r2['date_time_1'];
           $content_date_time = $r2['date_time_2'];
         }
         $t = stripslashes($r2['text']);
         // ��������� �� ��� ���������� ���������� <!--$$_XXX_$$--> �������� � �� �������� ������
         $rz = apply_filters($r2['filters'], parse_content($t));
     }
// ��������� � ���
$string[$n][$language] = $rz;

// ������� �� ���������
return $rz;

} // ���� �� ��������� translate($n)

// ��������� apply_filters($fs, $t) ������� ��������, ����� ����� �� �������� � ������ $fs, �������� ���� �� ����� ��� �������,
// ����� ������ $t.

function apply_filters($fs, $t){
$rz = $t; // ������ ��������
if(empty($fs)) return $rz;
global $idir, $adm_pth;
// ����� �� ����� �� ������
$fla = array();
if ($fs) $fla = explode(',', $fs);
// ��������� �� ������� �� ������      
foreach($fla as $fln){
  $flp = "filter/$fln/$fln.php"; // ��� �� ����� �� ������� �� ������������ �� �����
  $afp = "$idir$flp"; // ��������� ��� �� ����� �� �������
  if (file_exists($afp)){ // ��� ��� ����� ������
    include_once($afp);
    if(isset($fl['param']) && ($fl['param']>" ")) $rz = $fln($rz, $fl['param']);
    else $rz = $fln($rz);
  }
  else // ��� �������������� �� ������� ���� �� ��������� �� ������
    if (show_adm_links()) $rz .= '<p><br>Unknown fliter <a href="'.$adm_pth.'new_filter.php?f='.$fln.'">'.$fln.'</a><p>';
}
return $rz;
} 

?>

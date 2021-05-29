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

// ��������� sitemap($i) �������� html ��� �� ��������� ����� �� �����
// � ��������� �������� ���� sitemap.xml, ����� �� ������� � DOCUMENT_ROOT.

// ����������� $i � ������ �� ������ �� ����������, �� ����� �� ���������� �������,
// �� ���� �� ������� � �����, ������� � | ���������,
// ����� �� ������ ���� id ������� �� <div> ����, � ����� �� ��������� �������.
// ������ ����� ��������� �� � ������� �� �������� id="site_map".

// ��� � ���������� �������� ���������� $max_level, �� �������� ���� �������� �� ���� ���� �� ���������.

include_once($idir.'lib/f_db_table_field.php');

global $smfile, $smday, $page_header;

$page_passed = array(); // ������ �� ������, ����� ���� �� ����������,
                        // �������� �� �� �� �� �� ������ ���������
$map_level = 0;                 // ���� �� ����������
$GLOBALS['has_levels'] = false; // ����� ������ ��� ��� ���� �� ��������. �������� �� �� �� �� �� ��������,
                                // ������ "��������� ������" - "������� ������", ��� �� � ����������.
$i_root    = 0;  // ����� �� �������� ����
$id_pre    = ''; // ����������, � ����� �������� id ���������� �� <div> ����������

$smday     = 0; // ��� �� ������ �� ���� sitemap.xml
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml'))
   $smday = date('j', filemtime($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml') );
             // ����� �� ������� ��� �� ������
             // �� ������ ���������� �� ��������� ������������ �� ���� sitemap.xml, ��� ������ �� �� ��������
$smfile    = stored_value("today");
             // ����� �� �������� ���� �� �����
$GLOBALS['mpg_id'] = stored_value('site_map_root',1);
//$GLOBALS['mpg_id'] = db_table_field('menu_group', 'pages', "`ID`=".$GLOBALS['mpg_id']);

// ������ ������� �� ������

function sitemap($a = ''){
global $page_passed, $map_level, $i_root, $id_pre, $page_header, $smfile, $smday, $mpg_id,
       $db_link, $tn_prefix;
$ar = explode('|',$a);
if(!$ar[0]) $ar[0] = stored_value('main_index_pageid',1);
$id = 'site_map';
if (isset($ar[1])) $id = $ar[1];
$page_header .= '<script>
function mapHideShow(e, a=0){
var p = e.parentElement;
var h = p.style.height;
var ls = document.links;
for(var i=0;i<ls.length;i++) if(ls[i].parentElement==p) break;
var v = ls[i].offsetHeight + 2 * (ls[i].offsetTop - p.offsetTop) + "px";
// �������
if ( ( (h!=v)&&(a==0) ) || (a==1) ){
  e.innerHTML = "&#9658;"
  p.style.height = v;
  p.style.overflow = "hidden";
}
// ���������
if ( ( (h==v)&&(a==0) ) ||(a==2)){
  e.innerHTML = "&#9660;"
  p.style.height = "auto";
  p.style.overflow = "visible";
}
};
function mapContractExpandAll(a){
var sm = document.getElementById("'.$id.'");
var c = sm.children.length;
for(var i=0; i<c; i++) if(sm.children[i].nodeName=="DIV"){
  if(sm.children[i].children[0] && (sm.children[i].children[0].nodeName=="SPAN")){
    mapHideShow(sm.children[i].children[0], a);
  }
}
}
</script>';
$cache_name = 'sitemap_'.$ar[0].'_cache';
if(in_edit_mode()) $cache_name = $cache_name.'_edit';
if( !( isset($_GET['clear']) && ($_GET['clear']=='on') ) ){
    $rz = stored_value($cache_name);
    if($rz) return $rz;
}
$page_passed = array();
$map_level = 0;
$i_root    = 0;
$id_pre = 'map'.$ar[0];
$rz = sitemap_rec($ar[0], 1, $ar[0]==$mpg_id);
$clear_link = '';
if(in_edit_mode()) $clear_link = '<a href="'.set_self_query_var('clear','on').'">Clear cache</a>';
$rz = '<div id="'.$id.'">'."\n".$clear_link.
site_map_buttons().$rz.site_map_buttons()."
<p class=\"clear\"></p></div>";
// ��������� �� ��� sitemap.xml ����
if(    ($smday !== $smfile) // ��� ���
    && ($ar[0]  == $mpg_id) // �������� �� ����� �� ����� ����
  )
{
   $smfile = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
             '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n".
             $smfile.
             "</urlset>";
   $smfn = $_SERVER['DOCUMENT_ROOT'].'/sitemap.xml';
   if(is_writable($smfn)) file_put_contents($smfn, $smfile);
}
if( isset($_GET['clear']) && ($_GET['clear']=='on') ){
   $q = "UPDATE `$tn_prefix"."options` SET `value`='' WHERE `name` REGEXP 'sitemap_\\\d+_cache.*'";
   mysqli_query($db_link, $q);
}
store_value($cache_name, $rz);
return $rz;
}

//
// ������ "�������"-"���������"

function site_map_buttons(){
global $has_levels;
if(!$has_levels) return;
return '<p class="buttons">
<a href="" onclick="mapContractExpandAll(1); return false;">'.translate("site_map_contract").'</a>
<a href="" onclick="mapContractExpandAll(2); return false;">'.translate("site_map_expand").'</a>
</p>
';
}

//
// ���������� �������, ����� ������� �������
// $i - ����� �� ����
// $j - ? (�� �� �������� ���)
// $y - ����, ���� �� �� �������� ���� sitemap.xml

function sitemap_rec($i, $j, $y){
global $pth, $page_passed, $map_level, $has_levels, $max_level, $i_root, $ind_fl, $id_pre, $page_id, $smfile, $smday, $rewrite_on;
if(!isset($max_level)) $max_level = 100;

$page_passed[] = $i;

$count = 1; // ����� �� ��������� ������

$rz = "\n";
if (!$i_root) $i_root = $i;

// �������� �� ����� �� ������������� �� ���� $i
$mi = db_select_m('*', 'menu_items', "`group`=$i ORDER BY `place`");

// ������� �� ������ �� �������� �������� �� ������
$index = db_table_field('index_page','menu_tree',"`group`=$i");

// ���� �� �� �������� � ������ ������ � �������. �� ������������ �� �� ��������.
$ext = stored_value('sitemap_external')=='on';

// ����� �� ��������� �� ����� ����������� �� ������ $i
foreach($mi as $m){
  $rz .= '<div id="map'.$m['ID']."\">\n";
  $rz1 = '';
  $rz2 = '';
  $h = false;
  
  // ����� �� ���������� �� �������� ����
  if(is_numeric($m['link'])) $pid = 1*$m['link'];
  else $pid = 0;
  $p['hidden'] = 0;
  if($pid){
     // �������� �� ������� �� ����������
     $p = db_select_1('*','pages','`ID`='.$pid);
  }
  if (($i==$i_root)||($pid!=$index))
  {
    $lk = $m['link'];
    if ($pid){
       if($rewrite_on) $lk = "/$pid/";
       else $lk = $ind_fl.'?pid='.$pid;
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
          // �������� �� ���������� ��� ���� sitemap.xml
          if($y && ($smday !== $smfile) && (substr($lk, 0, 4) != 'http')){
            if( strlen($smfile) < 3 ) $smfile = '';
            $smfile .= "<url>\n".
                       "<loc>".$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].str_replace('&','&amp;',$lk)."</loc>\n".
                       "<lastmod>".date("Y-m-d")."</lastmod>\n".
                       "<changefreq>monthly</changefreq>\n".
                       "<priority>".(1-0.1*$map_level)."</priority>\n".
                       "</url>\n";
          }
          // �������� � html ���� �� ���������
          if( (substr($lk, 0, 4) != 'http') || $ext ){
            $rz1 .= '<a href="'.$lk.'">'.translate($m['name']).'</a>';
            if( $pid==$page_id ) $rz1 .= translate('sitemap_currentpage');
            if( in_edit_mode() ) {
               $rz1 .= " place:".$m['place'].' group:'.$m['group'];
               if ($h) $rz .= ' hidden ';
            }
            $rz1 .= "<br>\n";
          }
       }
    }
    $count++;
  }

  if ($pid) { // ��������� �� ��� � �������� � ����� �� ���������� CMS
    if ($p['menu_group']!=$i){ // ��� � �������� �� ����� ����
      $mtd = db_select_1('parent,index_page', 'menu_tree', '`group`='.$p['menu_group']);
      // ���������� ��������� �� ���������� ����� �� ���������
      if ( !in_array($p['menu_group'],$page_passed) && ($p['ID']==$mtd['index_page']) && ($i==$mtd['parent']) ){
        $map_level++;
        $has_levels = true;
        $n = db_table_field('COUNT(*)', 'menu_items', "`group`=".$p['menu_group'],0);
        if ($map_level<$max_level){
           if(in_edit_mode() || !$h) $rz1 .= sitemap_rec($p['menu_group'], $count, $y);
        }
        else {
           if($n>1) $rz1 .= '...';
        }
        $map_level--;
//        if($n>1)
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

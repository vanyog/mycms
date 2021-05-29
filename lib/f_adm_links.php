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

// ��������� adm_links() �������� html ��� �� ��������� �� ����� ������� �� ��������������.

// �� ������������ ���� ���� � ������� �� ������ ������ ���.
// ���� �������� ������ �������� ������ � ����� ��������� ��-����� ������, �� �� ��������,
// ��������� �������� � position:absolute �� �������� ����������� �� ���� ��������. ������ ��
// �������� ��������� adm_links_over = 1, �� �� �� �� ������ ������ ���.

// adm_links_custom - ������������, ������� �� �������������� ����� (�������)
// adm_links_cpanel - ����� �� ����� �� ���������� �� ��������

if(empty($idir)) $idir = str_replace('\\', '/', dirname(dirname(__FILE__))).'/';

include_once($idir.'conf_paths.php');
include_once($idir."lib/f_relative_to.php");
include_once($idir."lib/f_is_local.php");
include_once($idir.'lib/f_set_query_var.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_table_exists.php');
include_once($idir.'lib/f_parse_content.php');
include_once($idir."lib/f_edit_normal_links.php");

function adm_links(){
global $idir, $pth, $apth, $adm_pth, $edit_name, $edit_value, $web_host, $local_host, $main_index,
       $phpmyadmin_site, $phpmyadmin_local, $page_data;
if ( !show_adm_links() ) return '';
else {
  // $lpid - ����� �� ���-������ �������� �� �����
  if (db_table_exists('pages')) $lpid = db_select_1('ID','pages','1 ORDER by `ID` DESC');
  if (isset($lpid['ID'])) $lpid = $lpid['ID']; else $lpid = 1;

  // ������ �� �������� � �������� ��������
  $ppid = db_table_field('ID', "pages", "`ID`<".$page_data['ID']." ORDER BY `ID` DESC LIMIT 1");
  if (!$ppid) $ppid = 1;
  $npid = db_table_field('ID', "pages", "`ID`>".$page_data['ID']." ORDER BY `ID` ASC LIMIT 1");
  if (!$npid) $npid = $lpid;

  $mphp = $phpmyadmin_site;
  $go = 'http://'.$local_host.$_SERVER['REQUEST_URI'];
  $gon = 'go to LOCAL';
  if (is_local()){
    $mphp = $phpmyadmin_local;
    $go = 'http://'.$web_host.$_SERVER['REQUEST_URI'];
    if (substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth){
       $wp = stored_value('admin_path','manage').'/';
       if ($wp[0]!='/') $wp = $pth.$wp;
       $go = str_replace($adm_pth, $wp, $go);
    }
    $gon = 'go to WEB';
    $w3c = ''; $mob = ''; $spt = '';
  }
  else {
    if (substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth){
       $lp = $pth.'manage/';
       $go = str_replace($adm_pth, $lp, $go);
       $w3c = '';  $mob = ''; $spt = '';
    }
    else {
       $w3c = ' &#x25C7; <a href="http://validator.w3.org/check?uri='.
              urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" target="_blank">w3c</a>';
       $mob = ' &#x25C7; <a href="https://www.google.com/webmasters/tools/mobile-friendly/?url='.
              urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" target="_blank">mob</a>';
       $spt = ' &#x25C7; <a href="https://developers.google.com/speed/pagespeed/insights/?url='.
              urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'" target="_blank">sp</a>';
    }
  }
  
  $clink = stored_value('adm_links_custom','');

  $enmch = '';
  if ($pth!='/') $enmch = '<a href="/">/</a> &#x25C7; '."\n";
  if (!in_admin_path()) $enmch .= edit_normal_link(false).' &#x25C7;
<a href="" onclick="doNewPage();return false">New page</a> &#x25C7; ';

//if(in_admin_path())
   $f = relative_to($apth, $idir);
//else
//   $f = relative_to($apth, dirname($_SERVER['DOCUMENT_ROOT'].$main_index).'/');

$rp = random_page();

  $rz = '<script>
function doNewPage(){
if (confirm("Do you want to create new page?"))
na = "'.$adm_pth.'new_record.php?t=pages&menu_group='.$page_data['menu_group'].
'&title=p'.($lpid+1).'_title&content=p'.($lpid+1).'_content&template_id='.$page_data['template_id'].'";
document.location=na;
}
function hide(){
if (confirm("Hide this menu?")){
  deleteAllCookies();
  window.location.reload();
}
}
function gotoPageNumber(e){
if (e.keyCode==13){
  var n = document.getElementById("gtpNumber").value;
  if (n){
    var l = "'.$main_index.'?pid="+n;
    if (e.ctrlKey || e.metaKey) window.open(l);
    else document.location = l;
  }
}
}
function closeAdminLinks(){
var e = document.getElementById("adm_links");
e.style.display = "none";
}
</script>
<p id="adm_links">&nbsp; '.translate('admin_style').'
<a href="'.$adm_pth.'">'.$_SERVER['REMOTE_ADDR'].'</a>
<a href="'.$main_index.'">Home</a> &#x25C7; '.$enmch.'
<a href="'.$main_index.'?pid='.$ppid.'">&lt;</a>
<input type="text" size="4" id="gtpNumber" onkeypress="gotoPageNumber(event);">
<a href="'.$main_index.'?pid='.$npid.'">&gt;</a>
<a href="'.$main_index.'?pid='.$lpid.'&amp;'.$edit_name.'='.urlencode($edit_value).'">'.$lpid.'</a>
<a href="'.$rp.'">R</a> &#x25C7;
<a href="'.$pth.'mod/all_pages.php?t=1">all</a> &#x25C7;
<a href="'.$adm_pth.'edit_file.php?f='.$f.'">File system</a> &#x25C7;
<a href="'.$adm_pth.'edit_data.php">Database</a> &#x25C7;
<a href="'.$pth.'lib/f_page_cache.php?purge='.(1*( isset($_GET['pid']) && is_numeric($_GET['pid']) ? $_GET['pid'] : 0 )).'">Purge</a> &#x25C7;
<a href="'.stored_value('adm_links_cpanel').'" target="_blank">cPanel</a> &#x25C7;
<a href="'.$mphp.'" target="_blank">phpMyAdmin</a> &#x25C7;
<a href="'.$adm_pth.'showenv.php?AAAAAAA" target="_blank">$_SERVER</a> &#x25C7;
<a href="https://github.com/vanyog/mycms/wiki" target="_blank">Help</a> &#x25C7;
<a href="'.$go.'">'.$gon.'</a><!--&#x25C7;
<a hr  ="'.$adm_pth.'dump_data.php">Dump</a-->
'.$w3c.$mob.$spt.' &#x25C7;
'.$clink.' DB_REQ_COUNT
<a href="#" onclick="closeAdminLinks();return false;">x</a>&nbsp;
</p>';
//  if (stored_value('adm_links_over',0)!=1) $rz .= '<p>&nbsp;</p>';
  return $rz;
  }
}

// �������� ���� �� �� �������� ��������� �� ��������������

function show_adm_links(){
global $adm_pth,$adm_name,$adm_value;
// �� �� �������� ��� ��� ��������� noadm = yes
//print_r($_COOKIE); die;
if (isset($_COOKIE['noadm']) && ($_COOKIE['noadm']=='yes')) return false;
// ������ ��� �� ������� �������� �� ������������ �� ��������������
$a = substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth;
// ������� �� �������������� �� ��������� � ������, ��:
// - ������ � �� ������� ������
// - ������ � � ����� �� �����������
// - ������� �� �������� �� ������������ �� ��������������
// - �������� � �������� $_GET[$adm_name] = $adm_value
// - ��� ��������� � ��� $adm_name � �������� $adm_value
return is_local() /*|| in_edit_mode()*/ || $a || query_or_cookie($adm_name,$adm_value);
}

function random_page(){
global $main_index;
$mid = db_table_field('MAX(`ID`)', 'pages', 1, 0);
$c = 0;
do {
  $rid = rand(1,$mid);
  $c++;
} while ((db_table_field('ID', 'pages', "`ID`=$rid",0)<1) && ($c<10) );
return "$main_index?pid=$rid";
}

?>
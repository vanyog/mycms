<?php
/*
VanyoG CMS - a simple Content Management System
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

// Функцията adm_links() генерира html код за показване на линкове за главно администриране.

// По подразбиране след реда с линкове се вмъква празен ред.
// Това отмества цялата страница надолу и прави ленковете по-лесно четими, но на страници,
// съдържащи елементи с position:absolute се нарушава положението на тези елементи. Тогава се
// зададава настройка adm_links_over = 1, за да не се вмъкне празен ред.

// adm_links_custom - допълнителен, зададен от администратора текст (линкове)
// adm_links_cpanel - адрес на панел за управление на хостинга

if(empty($idir)) $idir = str_replace('\\', '/', dirname(dirname(__FILE__))).'/';

include_once($idir.'conf_paths.php');
include_once($idir."lib/f_relative_to.php");
include_once($idir."lib/f_is_local.php");
include_once($idir.'lib/f_set_query_var.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_table_exists.php');
include_once($idir.'lib/f_parse_content.php');
include_once($idir."lib/f_edit_normal_links.php");
//include_once($idir."lib/translation.php");
include_once($idir."lib/f_add_style.php");

function adm_links(){
global $idir, $pth, $apth, $adm_pth, $edit_name, $edit_value, $web_host, $local_host, $main_index,
       $phpmyadmin_site, $phpmyadmin_local, $page_data;
if ( !show_adm_links() ) return '';
else {
  add_style("adm_links");
  // $lpid - Номер на най-новата страница на сайта
  if (db_table_exists('pages')) $lpid = db_select_1('ID','pages','1 ORDER by `ID` DESC');
  if (isset($lpid['ID'])) $lpid = $lpid['ID']; else $lpid = 1;

  // Номера на предишна и следваща страница
  $ppid = 1;
  if(isset($page_data['ID'])) $ppid = db_table_field('ID', "pages", "`ID`<".$page_data['ID']." ORDER BY `ID` DESC LIMIT 1");
  $npid = $lpid;
  if(isset($page_data['ID'])) $npid = db_table_field('ID', "pages", "`ID`>".$page_data['ID']." ORDER BY `ID` ASC LIMIT 1");

  $mphp = $phpmyadmin_site;
  $go = $_SERVER['REQUEST_SCHEME'].'://'.$local_host.$_SERVER['REQUEST_URI'];
  $gon = 'go to LOCAL';
  if (is_local()){
    $mphp = $phpmyadmin_local;
    $go = $_SERVER['REQUEST_SCHEME'].'://'.$web_host.substr($_SERVER['REQUEST_URI'],strlen($pth)-1);
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
       $w3c = ' <a href="http://validator.w3.org/check?uri='.
              urlencode($_SERVER['REQUEST_SCHEME'].'//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).
              '" target="_blank">w3c</a>';
       $mob = ' <a href="https://www.google.com/webmasters/tools/mobile-friendly/?url='.
              urlencode($_SERVER['REQUEST_SCHEME'].'//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).
              '" target="_blank">mob</a>';
       $spt = ' <a href="https://developers.google.com/speed/pagespeed/insights/?url='.
              urlencode($_SERVER['REQUEST_SCHEME'].'//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).
              '" target="_blank">sp</a>';
    }
  }
  
  $clink = stored_value('adm_links_custom','');

  $enmch = '';
  if ($pth!='/') $enmch = '<a href="/">/</a> '."\n";
  if (!in_admin_path()) $enmch .= edit_normal_link(false).' 
<a href="" onclick="doNewPage();return false">New page</a> ';

//if(in_admin_path())
   $f = relative_to($apth, $idir);
//else
//   $f = relative_to($apth, dirname($_SERVER['DOCUMENT_ROOT'].$main_index).'/');

$rp = random_page();

  $rz = '<script>
function doNewPage(){
if (confirm("Do you want to create new page?"))
na = "'.$adm_pth.'new_record.php?t=pages&menu_group='.
(isset($page_data['menu_group']) ? $page_data['menu_group'] : 0).
'&title=p'.($lpid+1).'_title&content=p'.($lpid+1).'_content&template_id='.
(isset($page_data['template_id']) ? $page_data['template_id']: 1).'";
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
<p id="adm_links">DB_REQ_COUNT 
<a href="'.$adm_pth.'">'.$_SERVER['REMOTE_ADDR'].'</a>
<a href="'.$main_index.'">Home</a> '.$enmch.'
<a href="'.$main_index.'?pid='.$ppid.'">&lt;</a>
<input type="text" size="4" id="gtpNumber" onkeypress="gotoPageNumber(event);">
<a href="'.$main_index.'?pid='.$npid.'">&gt;</a>
<a href="'.$main_index.'?pid='.$lpid.'&amp;'.$edit_name.'='.urlencode($edit_value).'">'.$lpid.'</a>
<a href="'.$rp.'">Random page</a>
<a href="'.$pth.'mod/all_pages.php?t=1">All pages</a>
<a href="'.$adm_pth.'edit_file.php?f='.$f.'">File system</a>
<a href="'.$adm_pth.'edit_data.php">Database</a>
<a href="'.$pth.'lib/f_page_cache.php?purge='.(1*( isset($_GET['pid']) && is_numeric($_GET['pid']) ? $_GET['pid'] : 0 )).'">Purge</a> 
<a href="'.stored_value('adm_links_cpanel').'" target="_blank">cPanel</a>
<a href="'.$mphp.'" target="_blank">phpMyAdmin</a> 
<a href="'.$adm_pth.'showenv.php?AAAAAAA" target="_blank">$_SERVER</a> 
<a href="https://github.com/vanyog/VanyoG CMS/wiki" target="_blank">Help</a> 
<a href="'.$go.'">'.$gon.'</a><!--
<a hr  ="'.$adm_pth.'dump_data.php">Dump</a-->
'.$w3c.$mob.$spt.' 
'.$clink.'
<a href="#" onclick="closeAdminLinks();return false;">x</a>
</p>';
  return $rz;
  }
}

// Определя дали да се показват линковете за администриране

function show_adm_links(){
global $adm_pth,$adm_name,$adm_value;
// Не се показват ако има бисквитка noadm = yes
//print_r($_COOKIE); die;
if (isset($_COOKIE['noadm']) && ($_COOKIE['noadm']=='yes')) return false;
// Истина ако се зарежда страница от директорията за администриране
$a = substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth;
// Линкове за администриране се генерират в случай, че:
// - сайтът е на локален сървър
// - сайтът е в режим на редактиране
// - показва се страница от директорията за администриране
// - получена е стойност $_GET[$adm_name] = $adm_value
// - има бисквитка с име $adm_name и стойност $adm_value
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
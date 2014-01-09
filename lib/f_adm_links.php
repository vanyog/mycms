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

// Функцията adm_links() генерира html код за показване на някои линкове за администриране.

// По подразбиране след реда с линкове се вмъква празен ред.
// Това отмества цялата страница надолу и прави ленковете по-лесно четими, но на страници,
// съдържащи елементи с position:absolute се нарушава положението на тези елементи. Тогава се
// се зададава настройка adm_links_over = 1, за да не се вмъкне празен ред.

// adm_links_custom - допълнителен, зададен от администратора текст (линкове)
// adm_links_cpanel - адрес на панел за управление на хостинга

$idir = dirname(dirname(__FILE__)).'/';

include_once($idir.'conf_paths.php');
include_once($idir."lib/f_is_local.php");
include_once($idir.'lib/f_set_query_var.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_table_exists.php');
include_once($idir.'lib/f_parse_content.php');

function adm_links(){
global $pth, $adm_pth, $edit_name, $edit_value, $web_host, $local_host, 
       $phpmyadmin_site, $phpmyadmin_local, $page_data;
if ( !show_adm_links() ) return '';
else {
  // $lpid - Номер на най-новата страница на сайта
  if (db_table_exists('pages')) $lpid = db_select_1('ID','pages','1 ORDER by `ID` DESC');
  if (isset($lpid['ID'])) $lpid = $lpid['ID']; else $lpid = 1;

  // Номера на предишна и следваща страница
  $ppid = $page_data['ID']-1; if ($ppid<1) $ppid = 1;
  $npid = $ppid+2; if ($npid>$lpid) $npid = $lpid;

  $mphp = $phpmyadmin_site;
  $go = $local_host; $gon = 'go to LOCAL';
  if (is_local()){
    $mphp = $phpmyadmin_local;
    $go = $web_host; $gon = 'go to WEB'; $w3c = '';
  }
  else {
    if (substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth) $w3c = '';
    else $w3c = ' :: <a href="http://validator.w3.org/check?uri='.
         urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'">w3c</a>';
  }
  $go = 'http://'.$go.$_SERVER['REQUEST_URI'];
  
  $clink = stored_value('adm_links_custom','');

  $enmch = '';
  if ($pth!='/') $enmch = '<a href="/">/</a> :: '."\n";
  if (!in_admin_path()) $enmch .= '<a href="'.$_SERVER['PHP_SELF'].'?'.set_query_var($edit_name,$edit_value).'">Edit</a> :: 
<a href="'.$_SERVER['PHP_SELF'].'?'.set_query_var($edit_name,'0').'">Normal</a> :: 
<a href="" onclick="doNewPage();return false">New page</a> :: ';

  $rz = '<script type="text/javascript"><!--
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
--></script>
<p id="adm_links">&nbsp;
<a href="'.$pth.'">Home</a> :: '.$enmch.'
<a href="'.$pth.'index.php?pid='.$ppid.'">&lt;</a>  
<a href="'.$pth.'index.php?pid='.$npid.'">&gt;</a> 
<a href="'.$pth.'index.php?pid='.$lpid.'&amp;'.$edit_name.'='.urlencode($edit_value).'">'.$lpid.'</a> :: 
<a href="'.$adm_pth.'edit_file.php">File system</a> :: 
<a href="'.$adm_pth.'edit_data.php">Database</a> :: 
<a href="'.stored_value('adm_links_cpanel').'" target="_blank">cPanel</a> :: 
<a href="'.$mphp.'" target="_blank">phpMyAdmin</a> :: 
<a href="'.$adm_pth.'showenv.php?AAAAAAA" target="_blank">$_SERVER</a> :: 
<a href="'.$go.'">'.$gon.'</a><!--:: 
<a hr  ="'.$adm_pth.'dump_data.php">Dump</a-->'.$w3c.' :: 
'.$clink.'
<a href="'.$pth.'lib/exit.php">x</a>&nbsp; 
</p>';
  if (stored_value('adm_links_over',0)!=1) $rz .= '<p>&nbsp;</p>';
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
return is_local() || in_edit_mode() || $a || query_or_cookie($adm_name,$adm_value);
}

?>
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

// Функцията adm_links() генерира html код за показване на някои линкове за администриране

$idir = dirname(dirname(__FILE__)).'/';

include_once($idir.'conf_paths.php');
include_once($idir."lib/f_is_local.php");
include_once($idir.'lib/f_set_query_var.php');
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_table_exists.php');

function adm_links(){
global $pth, $adm_pth, $edit_name, $edit_value, $web_host, $local_host, 
       $phpmyadmin_site, $phpmyadmin_local, $page_data;
if ( !show_adm_links() ) return '';
else {

  // $lpid - Номер на най-новата страница на сайта
  if (db_table_exists('pages')) $lpid = db_select_1('ID','pages','1 ORDER by `ID` DESC');
  if (isset($lpid['ID'])) $lpid = $lpid['ID']; else $lpid = 1;

  $mphp = $phpmyadmin_site;
  $go = $local_host; $gon = 'go to LOCAL';
  if (is_local()){
    $mphp = $phpmyadmin_local;
    $go = $web_host; $gon = 'go to WEB'; $w3c = '';
  }
  else {
    if (substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth) $w3c = '';
    else $w3c = ' :: 
<a href="http://validator.w3.org/check?uri='.urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'">w3c</a>';
  }
  $go = 'http://'.$go.$_SERVER['REQUEST_URI'];

  $enmch = '';
  if ($pth!='/') $enmch = '<a href="/">/</a> :: '."\n";
  if (!in_admin_path()) $enmch .= '<a href="'.$_SERVER['PHP_SELF'].'?'.set_query_var($edit_name,$edit_value).'">Edit</a></a> :: 
<a href="'.$_SERVER['PHP_SELF'].'?'.set_query_var($edit_name,'0').'">Normal</a></a> :: 
<a href="" onclick="doNewPage();return false">New page</a> :: ';

  return '<script type="text/javascript"><!--
function doNewPage(){
if (confirm("Do you want to create new page?"))
na = "'.$adm_pth.'new_record.php?t=pages&menu_group='.$page_data['menu_group'].
'&title=p'.($lpid+1).'_title&content=p'.($lpid+1).'_content&template_id='.$page_data['template_id'].'";
document.location=na;
}
--></script>
<p style="position:fixed; font-size:80%; margin:0; padding:0; border-style:solid; border-color:grey; border-width:0 0 1px 0; z-index:100;">
<a href="'.$pth.'">Home</a> :: '.$enmch.'
<a href="'.$pth.'index.php?pid='.$lpid.'&amp;'.$edit_name.'='.urlencode($edit_value).'">'.$lpid.'</a> :: 
<a href="'.$adm_pth.'edit_file.php">File system</a> :: 
<a href="'.$adm_pth.'edit_data.php">Database</a> :: 
<a href="'.$mphp.'" target="_blank">phpMyAdmin</a> :: 
<a href="'.$adm_pth.'showenv.php?AAAAAAA" target="_blank">$_SERVER</a> :: 
<a href="'.$go.'">'.$gon.'</a> <!--:: 
<a hr  ="'.$adm_pth.'dump_data.php">Dump</a-->'.$w3c.'
</p><p>&nbsp</p>';
  }
}

// Определя дали да се показват линковете за администриране

function show_adm_links(){
global $adm_pth,$adm_name,$adm_value;
// Истина ако се зарежда страница от директорията за администриране
$a = substr($_SERVER['REQUEST_URI'],0,strlen($adm_pth))==$adm_pth;
// Линкове за администриране се генерират в случай, че:
// - сайтът е на локален сървър
// - сайтът е в режим на редактиране
// - показва се страница от директорията за администриране
// - получена е стойност $_GET[$adm_name] = $adm_value.
// - има бисквитка с име $adm_name и стойност $adm_value.
return is_local() || in_edit_mode() || $a || query_or_cookie($adm_name,$adm_value);
}

?>

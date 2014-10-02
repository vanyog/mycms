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

// Проверяване правата на влязъл потребител и показване на меню с позволените му действия.

// Когато $nom=false (по подразбиране) се показва меню с разрешените на влезлия потребител действия.
// Ако $nom=true само се проверяват правата без да се показва меню.
// $nom = адрес на страница за излизане



global $can_visit, $can_manage;

//include_once($idir."conf_paths.php");
include_once($idir."lib/f_db_select_m.php");
include_once($idir."lib/f_mod_list.php");
include_once($idir."lib/f_edit_normal_links.php");
include_once($idir."mod/user/f_user.php");
//include_once($idir."lib/f_mod_path.php");

if (!session_id()) session_start();

function usermenu($nom = false){

global $page_data, $can_edit, $can_create, $can_manage, $can_visit, $pth, $page_header;

// Ако в сесията няма данни за потребител, връща празен стринг.
if (!isset($_SESSION['user_username'])||!isset($_SESSION['user_password'])) return '';

// Име на таблицата с данни за потребители
$user_table = stored_value('user_table','users');

// $id - номер на влязъл потребител
$ud = db_select_1('*',$user_table, 
      "`username`='".addslashes($_SESSION['user_username'])."' AND `password`='".$_SESSION['user_password']."'");

// Ако няма потребител със запазените в сесията име и парола, връща празен стринг.
if (!$ud) return '';
$id = $ud['ID'];

// Четене на правата на потребителя
$p = db_select_m('*', 'permissions', "`user_id`=$id");// print_r($p); die;
$rz = '';

// Установяване на правата от различните типове
$can_edit = false;    // Право на потребителя да редактира надписите по страницата 
$can_create = false;  // Право на потребителя да създава/изтрива страници в дадения раздел(подменю) на сайта
$can_manage = array();// Права за администриране на модули

foreach($p as $q) switch($q['type']) {
case 'all':
  $can_edit = $q['yes_no'];
  $can_create = $q['yes_no'];
  $ml = mod_list(true);
  foreach($ml as $m){
    $n = pathinfo($m,PATHINFO_FILENAME);
    $yn = db_select_m('yes_no','permissions',"`user_id`=$id AND `type`='module' AND `object`='$n'");
    if (!count($yn)){ $can_manage[$n] = $q['yes_no']; }
    else { $can_manage[$n] = $yn[0]['yes_no']; }
  }
  $can_visit = true;
  break;
case 'menu':// print_r($page_data); die;
  if (in_that_branch($page_data['menu_group'], $q['object'])) $can_create = $q['yes_no'];
  $can_edit = $can_create;
  if ($can_create) $can_visit = true;
  break;
case 'page':
  if ($q['object']==$page_data['ID']) $can_edit = $q['yes_no'];
  if ($can_edit) $can_visit = true;
  break;
case 'module':
  $can_manage[$q['object']]=$q['yes_no'];
  if ($q['yes_no']) $can_visit = true;
  break;
}

if ($nom===true) return '';

// Съставяне на менюто
$pt = current_pth(__FILE__);
if ($can_create){
 $rz .= '<a href="'.$pt.'new_page.php?p='.$page_data['ID']."\">Page New</a><br>\n";
 // Брой на страниците в раздела
 $gc = db_table_field('COUNT(*)','menu_items','`group`='.$page_data['menu_group']);
 // Индекс на главната страница на раздела
 $mi = db_table_field('index_page','menu_tree','`group`='.$page_data['menu_group']);
 // Главната страница на сайта и главната страница на раздел, в който има и други страници,
 // не могат да се трият
 if ($can_edit && ($page_data['ID']>1) && ( ($gc==1)||($mi!=$page_data['ID']) ) ){
  $page_header .= '<script type="text/javascript"><!--
function confirm_page_deleting(){
if (confirm("'.translate('usermenu_confirdeleting').'")) document.location = "'.$pt.'delete_page.php?pid='.$page_data['ID'].'";
}
--></script>';
  $rz .= '<a href="" onclick="confirm_page_deleting();return false;">Page Delete</a><br>'."\n";
 }
 if ($page_data['hidden'])
     $rz .= '<a href="'.$pt.'/toggle_visibility.php?pid='.$page_data['ID'].'">Page Visible</a><br>'."\n";
 else
     $rz .= '<a href="'.$pt.'/toggle_visibility.php?pid='.$page_data['ID'].'">Page Hidden</a><br>'."\n";
  $page_header .= '<script type="text/javascript"><!--
function getPage(){
var a = prompt("ID of the page to get content from");
if (a){
  var r = "'.$pt.'get_content.php?p1="+a+"&p2='.$page_data['ID'].'";
  alert(r);
  document.location = r;
}
}
--></script>';
 $rz .= '<a href="javascript:void(0);" onclick="getPage();">Page Get</a><br>'."\n";
}
if ($can_edit) $rz .= edit_normal_link()."<br>\n";
foreach($can_manage as $m=>$yn) if( $yn) {
  $fn = dirname(mod_path($m)).'/f_menu_items.php';
  if (file_exists($fn)){
    include_once($fn);
    eval('$rz .= '.$m.'_menu_items();');
  }
}
if (strlen($nom)&&!strlen($ud['type'])){
  $rz .= '<span class="user">'.$_SESSION['user_username'].
         ' <a href="'.$nom.'">'.translate('user_logaut').'</a></span>'."<br>\n";
}
return '<div id="user_menu">'."\n".$rz."\n</div>";
}

//
// Проверява дали менюто на страницата е подменю на разрешеното за редактиране от потребителя меню
//
function in_that_branch($pi,$j){// echo "$pi $j<br>";
if ($pi==$j) return true;
$rz = false;
do{
 $pi = db_table_field('parent', 'menu_tree', "`group`=$pi");// print_r($pi);// die;
 $rz = $pi==$j;
} while ( !($rz || ($pi==0)) );
//echo "$rz $pi"; die;
return $rz;
}

?>
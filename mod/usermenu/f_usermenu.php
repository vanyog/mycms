<?php
/*
VanyoG CMS - a simple Content Management System
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
// $nom = стринг - адрес на страница за излизане

global $can_edit, $can_create, $can_managee, $can_visit, $page_header;

include_once($idir."lib/f_db_select_m.php");
include_once($idir."lib/f_mod_list.php");
include_once($idir."lib/f_edit_normal_links.php");
include_once($idir."lib/f_add_style.php");
include_once($idir."mod/user/f_user.php");

if (!session_id() && isset($_COOKIE['PHPSESSID'])) session_start();

function usermenu($nom = false){

global $page_id, $page_data, $can_edit, $can_create, $can_manage, $can_visit, $pth, 
       $adm_pth, $page_header, $added_styles, $main_index;

// Ако не е задаадена стойност на $page_id
if(!isset($page_id)){
  if(!isset($_SERVER['HTTP_REFERER'])) // Търсене в адреса на страницата, от която се идва
     die('Error in modul USERMENU. No HTTP_REFERER is set.');
  $r = array();
  preg_match_all('/pid=(\d+)/', $_SERVER['HTTP_REFERER'], $r);//die($_SERVER['HTTP_REFERER']);
  if(isset($r[1][0])) $page_id = $r[1][0];
  else {
     // Проверка дали не се идва от главната страница
     $h = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."$main_index?";
     $l = strlen($h);
     // Ако се идва от главната страница $page_id е нейния ID
     if(substr($_SERVER['HTTP_REFERER'], 0, $l)==$h) $page_id = stored_value('main_index_pageid',1);
     // Ако и това не става - фатална грешка
     if(!$page_id) die('Error in modul USERMENU. Cannot determine pid parameter.');
  }
}

if(!isset($page_data)) $page_data = db_select_1('*', 'pages', "`ID`=$page_id", false);

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
$p = db_select_m('*', 'permissions', "`user_id`=$id");
$rz = '';

// Установяване на правата от различните типове
$can_edit = false;    // Право на потребителя да редактира надписите по страницата 
$can_create = false;  // Право на потребителя да създава/изтрива страници в дадения раздел(подменю) на сайта
$can_manage = array();// Права за администриране на модули

foreach($p as $q) if($q['yes_no']) switch($q['type']) {
case 'all':
  $rz .= "<a href=\"$adm_pth\">Admin path</a> \n<div class=\"sep\"></div>\n";
  $ap = stored_value('admin_page');
  if($ap) $rz .= "<a href=\"$ap\">Admin page</a> \n";
  $can_edit = $q['yes_no'];
  $can_create = $q['yes_no'];
  $ml = mod_list(true);
  $can_visit = false;
  foreach($ml as $m){
    $n = pathinfo($m, PATHINFO_BASENAME);
    $yn = db_select_m('yes_no','permissions',"`user_id`=$id AND `type`='module' AND `object`='$n'");
    if (!count($yn)){ $can_manage[$n] = $q['yes_no']; }
    else { $can_manage[$n] = $yn[0]['yes_no']; }
    $can_visit = $can_visit || $can_manage[$n];
  }
  break;
case 'menu':
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
if ($can_edit) $rz .= edit_normal_link()." \n";
if ($can_create){
 $rz .= '<a href="'.$pt.'new_page.php?p='.$page_data['ID']."\">Page New</a> \n";
 $rz .= '<a href="'.$pt.'new_page_from_h.php?p='.$page_data['ID'].'">Page From H</a> '."\n";
 $rz .= '<a href="'.$pt.'correct_h_tags.php?p='.$page_data['ID'].'">Page Crrect H</a> '."\n";
 // Брой на страниците в раздела
 $gc = db_table_field('COUNT(*)','menu_items','`group`='.$page_data['menu_group']);
 // Индекс на главната страница на раздела
 $mi = db_table_field('index_page','menu_tree','`group`='.$page_data['menu_group']);
 // Главната страница на сайта и главната страница на раздел, в който има и други страници,
 // не могат да се трият
 if ($can_edit && ($page_data['ID']>1) && ( ($gc==1)||($mi!=$page_data['ID']) ) ){
  $page_header .= '<script>
function confirm_page_deleting(){
if (confirm("'.translate('usermenu_confirdeleting').'")) document.location = "'.$pt.'delete_page.php?pid='.$page_data['ID'].'";
}
</script>'."\n";
  $rz .= '<a href="" onclick="confirm_page_deleting();return false;">Page Delete</a> '."\n";
 }
 if (isset($page_data['hidden']) && $page_data['hidden'])
     $rz .= '<a href="'.$pt.'/toggle_visibility.php?pid='.$page_data['ID'].'">Page Public</a> '."\n";
 else
     $rz .= '<a href="'.$pt.'/toggle_visibility.php?pid='.$page_data['ID'].'">Page Hide</a> '."\n";
 $new_group = db_table_field('MAX(`menu_group`)', 'pages', '1') + 1;
 $page_header .= '<script>
function getPage(){
var a = prompt("ID of the page to get content from");
if (a){
  var r = "'.$pt.'get_content.php?p1="+a+"&p2='.$page_data['ID'].'";
  document.location = r;
}
}
function moveTo(){
var g = prompt("ID of the page group to move the page to", '.$new_group.');
if (g){
  var r = "'.$pt.'move_page.php?p='.$page_data['ID'].'"+"&g="+g;
  document.location = r;
}
}
</script>'."\n";
 $rz .= '<a href="javascript:void(0);" onclick="getPage();">Page Get</a> '."\n";
 $rz .= '<a href="javascript:void(0);" onclick="moveTo();">Page Move</a> '."\n";
}
$page_header .= '<script>
function closeUMemu(){
var m = document.getElementById("user_menu");
if(true || confirm("Would you like to hide user menu? It will appear again after page reload."))
  m.style.display = "none";
}
</script>'."\n";
foreach($can_manage as $m=>$yn) if( $yn ) {
  $fn = dirname(mod_path($m)).'/f_menu_items.php';
  if (file_exists($fn)) {
    include_once($fn);
    $added_styles .= 'div.sep { border-bottom:solid 1px; padding:0; margin:0; }';
    $mi = '';
    eval('$mi = '.$m.'_menu_items();');
    if($mi) $rz .= "<div class=\"sep\"></div>".$mi;
  }
}
$hp = stored_value('usermenu_helppage');
if ( strlen($nom) && strlen($rz) ){
  if ($hp) $rz .= "<a href=\"$hp\" target=\"_blank\">Help</a> \n";
  $rz .= '<span class="user">'.$_SESSION['user_username'].
         ' <a href="'.$nom.'">'.translate('user_logaut').'</a></span>'." \n";
}
if($rz){
  add_style('usermenu');
  return '<div id="user_menu">
<a href="" class="cLink" onclick="closeUMemu();return false;">close</a> 
'."\n".$rz."\n</div>
";
}
else return '';
}

//
// Проверява дали менюто на страницата е подменю на разрешеното за редактиране от потребителя меню
//
function in_that_branch($pi,$j){
if ($pi==$j) return true;
$rz = false;
do{
 $pi = db_table_field('parent', 'menu_tree', "`group`=$pi");// print_r($pi);// die;
 $rz = $pi==$j;
} while ( !($rz || ($pi==0)) );
return $rz;
}

?>

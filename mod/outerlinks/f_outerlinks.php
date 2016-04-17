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

include_once($idir.'lib/f_set_self_query_var.php');
include_once($idir.'lib/f_db_table_field.php');

function outerlinks(){
return outer_links();
}

// Главна функция за показване на Интернет връзки
// ----------------------------------------------
function outer_links(){
global $tn_prefix, $db_link;

// Общ брой на връзките
$lc = db_table_field('COUNT(*)','outer_links',"`link`>''");

// Общ брой на категориите
$cc = db_table_field('COUNT(*)','outer_links',"`link`=''");

// Номер на линка, за отваряне
$lid = 0;
if (isset($_GET['lid'])) switch (strtolower($_GET['lid'])){
// Показване на всички връзки в разгърнат вид
case 'all': return '<div id="outer_links">'."\n".outerlenks_all(0, '')."</div>\n"; break;
default: $lid = 1*$_GET['lid'];
}

// Път към началната страница
$tr = link_tree($lid);

$rz = '';

// Ако сме на началната страница - добавяне на начално съобщение
if (!$tr) $rz = translate('outerlinks_homemessage');

// Показване на бройките
$rz .= '<div id="outer_links">'.start_edit_form().'
<p class="counts">'.translate('outerlinks_totalcount')." $lc ".translate('outerlinks_in')." $cc ".translate('outerlinks_categories').
" &nbsp; <a href=".set_self_query_var('lid', 'all').">".translate('outerlinks_all')."</a></p>\n";

// Ако е извършено търсене се показва резултата от търсенето
if (count($_POST) && isset($_POST['search_by'])){
$rzr =  link_search();
if ($rzr) return $rz.$rzr.'
</div>';
}

// Ако са изпратени данни за редактиране
if (count($_POST)) edit_link();

// Четене на данните за линка
$l = db_select_1('*','outer_links',"`ID`=$lid");

// Ако е линк се изброява кликването и се препраща към адреса на линка
if (isset($l['link']) && $l['link']>''){
 if (!show_adm_links()){ // Броят се кликванията само ако посетителят не е администратор
   $q = "UPDATE `$tn_prefix"."outer_links` SET clicked = clicked+1 WHERE `ID`=".$l['ID'].";";
   mysqli_query($db_link,$q);
 }
 header('Location: '.$l['link']);
}

// Добавяне пътя към началната страница
if ($tr) $rz .= $tr;
else $rz .="\n";

// Четене и показване на (под)категориите
$ca = db_select_m('*','outer_links',"`up`=$lid AND (`link`='' OR `link` IS NULL) ORDER BY `place`");
//print_r($ca); die;
$p = current_pth(__FILE__);
foreach($ca as $c){// print_r($c); die;
 $rz .= '<p>'.edit_radio($c['ID'],$c['place']).'<img src="'.$p.'folder.gif" alt=""> <a href="'.
        set_self_query_var('lid',$c['ID']).'">'.stripslashes($c['Title'])."</a>";
 if (isset($c['Comment']) && $c['Comment']) $rz .= ' - '.stripslashes($c['Comment']);
 $rz .= "</p>\n";
}

// Четене и показване на линковете
$la = db_select_m('*','outer_links',"`up`=$lid AND `link`>'' ORDER BY `place`");
foreach($la as $l){
 $rz .= '<p>'.edit_radio($l['ID'],$l['place']).'<img src="'.$p.'go.gif" alt=""> <a href="'.
        set_self_query_var('lid',$l['ID']).'" title="'.$l['link'].
        '" target="_blank">'.stripslashes($l['Title'])."</a>";
 if (isset($l['Comment']) && ($l['Comment']>" ")) $rz .= ' - '.stripslashes($l['Comment']);
 $rz .= "</p>\n";
}

// Показване на формата за търсене
$rz .= '
'.end_edit_form($lid).search_link_form().'
</div>';
return $rz;
}

// Показване пътя до началната страница
// ------------------------------------
function link_tree($lid){
if (!$lid) return "";
$rz = ""; $lk = ''; $cm = '';
do {
  $l = db_select_1('*','outer_links',"`ID`=$lid");
  $lid = $l['up'];
  if ($rz) $rz = " > ".$rz;
  if ($lk) $rz = '<a href="'.$lk.'">'.$l['Title']."</a>".$rz;
  else{
     $rz = '<span>'.$l['Title'].'</span>'.$rz;
     $cm = '';
     if (isset($l['Comment'])) $cm = $l['Comment'];
  }
  $lk = set_self_query_var('lid',$l['up']);
} while ($lid);
if ($rz) $rz = " > ".$rz;
if ($lk) $rz = '<a href="'.$lk.'">'.translate('outerlinks_home')."</a>".$rz;
$rz = "<p class=\"link_tree\">\n".$rz."\n</p>\n";
if ($cm) $rz .= "<p>$cm</p>\n";
return "$rz\n<p>";
}

// Показване резултат от търсене
// -----------------------------
function link_search(){
global $pth, $page_id;
if (!isset($_POST['search_for']) || !$_POST['search_for']) return '';
$p = current_pth(__FILE__);
$q = '';
// Съставяне на SQL заявката
switch ($_POST['search_by']){
// За търсене в заглавията
case 'keyword': 
  $wa = explode(' ',$_POST['search_for'],4);
  foreach($wa as $i => $w){
    if ($i>2) break;
    if ($q) $q .= ' AND ';
    $q .= "`Title` LIKE '%".addslashes($w)."%'";
  }
  break;
// За търсене в адресите
case 'url': 
  $q = "`link` LIKE '%".addslashes($_POST['search_for'])."%'";
  break;
}
// Извличане на данните
$ra = db_select_m('*','outer_links',"$q ORDER BY `place`");
$rz1 = ''; // Намерени категории
$rz2 = ''; // Намерени линкове
foreach($ra as $r){
  // Линк
  $lk = set_self_query_var('lid',$r['ID']);
  // Заглавие на категорията на линка
  if (!$r['up']) $t2 = translate('outerlinks_home'); 
  else $t2 = db_table_field('Title','outer_links',"`ID`=".$r['up']);
  // Title - ако е линк
  if ($r['link']) $t1 = ($r['link']); else $t1 = '';
  // Сглобяване на реда с линка  
  $lk = '<a href="'.$lk.'" title="'.$t1.'" target="_blank">'.stripslashes($r['Title']).'</a>   '.'<a href="'.set_self_query_var('lid',$r['up']).'" title="'.$t2.'">>></a>';
  $lk .= "<br>\n";
  // Добавяне към резултата
  if ($r['link']) $rz2 .= '<img src="'.$p.'go.gif" alt=""> '.$lk;
  else  $rz1 .= '<img src="'.$p.'folder.gif" alt=""> '.$lk;
}
return '<p class="link_tree"><a href="'.$pth.'index.php?pid='.$page_id.'">'.translate('outerlinks_home').'</a>   '
.translate('outerlinks_found')." ".count($ra)."</p>
$rz1$rz2".search_link_form();
}

// Показване формата за търсене
// ----------------------------
function search_link_form(){
global $page_id;
return '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?pid='.$page_id.'">
<p class="search">'.translate('outerlinks_searchin').' 
<input type="radio" name="search_by" value="keyword" checked> '.translate('outerlinks_intitles').' 
<input type="radio" name="search_by" value="url"> '.translate('outerlinks_inurls').' 
<input type="text" name="search_for">
<input type="submit" value="'.translate('outerlinks_find').'">
</p></form>';
}

// Начало на формата за редактиране
// --------------------------------
function start_edit_form(){
if (!in_edit_mode()) return '';
else return '
<script type="text/javascript"><!--
function doDelete_link(){
var f = document.forms.link_edit_form;
var r = f.link_id;
var k = -1;
for(i=0; i<r.length; i++) if (r[i].checked) k = i;
if (k<0) { alert("Check a file to be deleted."); return; }
if (confirm("Do you really want to delete the checked link?")){
  f.action.value = "delete";
  f.submit();
}
}
--></script>
<form method="POST" name="link_edit_form">';
}

// Край на формата за редактиране
// --------------------------------
function end_edit_form($i){
if (!in_edit_mode()) return '';
else return '
<input type="hidden" name="action" value="update">
<p>URL: <input type="text" name="link" size="50"> 
Place: <input type="text" name="place" size="5"> 
Group: <input type="text" name="up" size="5" value="'.$i.'"></p>
<p>Title: <input type="text" name="title" size="100"></p>
<p>Comment: <textarea name="comment" cols="83" rows="4" style="vertical-align:top"></textarea></p>
<input type="submit" value="Add/Update"> 
<input type="button" value="Delete" onclick="doDelete_link();">
</form>';
}

// Радио бутони, които се показват в режим на редактиране
// ------------------------------------------------------
function edit_radio($id,$p){
if (!in_edit_mode()) return '';
else return '<input type="radio" name="link_id" value="'.$id.'">'.$p.' ';
}

// Добавяне/променяне на данните в режим на редактиране
// ----------------------------------------------------
function edit_link(){
if ( ! in_edit_mode() ) return;
global $tn_prefix,$db_link;

$id = 0;
if (isset($_POST['link_id'])) $id = 1*$_POST['link_id'];

$q0 = " `$tn_prefix"."outer_links` ";

if ($_POST['action']=='delete'){
  if (db_table_field('link', 'outer_links', "`ID`=$id")=='') die('This is a folder!');
  $q = "DELETE FROM$q0 WHERE `ID`=$id;";
}
else {
  $q1 = "INSERT INTO$q0 SET `date_time_1`=NOW(), "; $q2 = ''; $q3 = ';';
  if ($id) { $q1 = "UPDATE$q0 SET "; $q3 = "WHERE `ID`=$id;"; }

  if ($_POST['link'])    $q2 .= "`link`='".addslashes($_POST['link'])."', ";
  if ($_POST['title'])   $q2 .= "`Title`='".addslashes($_POST['title'])."', ";
  if ($_POST['comment']) $q2 .= "`Comment`='".addslashes($_POST['comment'])."', ";
  if ($_POST['up']>'')      $q2 .= "`up`=".(1*$_POST['up']).", ";
  if ($_POST['place'])   $q2 .= "`place`=".(1*$_POST['place']).", ";
  else if (!$id) $q2 .= "`place`=".(db_table_field('MAX(`place`)', 'outer_links', '1')+10).", ";
//  print_r($q2); die;
  if (!$q2) return;
  $q = $q1.substr($q2,0,strlen($q2)-2)." ".$q3;
}
mysqli_query($db_link,$q);

}

//
// Показване на всички връзки в разгърнат вид
// ------------------------------------------
function outerlenks_all($up, $tx, $lv = 1){
$rz = '';
$dt = db_select_m('*', 'outer_links', "`up`=$up AND (`link`>'') ORDER BY `place`");
foreach($dt as $d){
  $rz .= '<p><a href="'.
  set_self_query_var('lid',$d['ID']).'" title="'.$d['link'].
  '" target="_blank">'.$d['Title']."</a>";
  if ($d['Comment']) $rz .= ' - '.$d['Comment'];
  $rz .= "</p>\n";
}
$da = db_select_m('*', 'outer_links', "`up`=$up AND (`link`='' OR `link` IS NULL) ORDER BY `place`");
foreach($da as $d){
  $t = '<h'.($lv+1).'>'.$d['Title'].'</h'.($lv+1).">\n";
  if ($d['Comment']) $t .= '<p>'.$d['Comment']."</p>\n";
  $rz .= outerlenks_all( $d['ID'], $t, $lv + 1 );
}
$rz = "<div>\n$tx$rz</div>\n";
return $rz;
}

?>

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
include_once($idir.'lib/f_unset_self_query_var.php');
include_once($idir.'lib/f_db_table_field.php');

function outerlinks(){
return outer_links();
}

// ������ ������� �� ��������� �� �������� ������
// ----------------------------------------------
function outer_links(){

global $tn_prefix, $db_link, $site_encoding, $page_header;

if (!$site_encoding) $site_encoding = 'windows-1251';

// ��� ���� �� ��������
$lc = db_table_field('COUNT(*)','outer_links',"`link`>''");

// ��� ���� �� �����������
$cc = db_table_field('COUNT(*)','outer_links',"`link`=''");

$rz = '';

// ����� �� �����, �� ��������
$lid = 0;
// ����� �� �� ������
$what = '';
if (isset($_GET['lid'])){
  $what = strtolower($_GET['lid']);
  $lid = 1*$_GET['lid'];
}

// ��� ��� ��������� ��������
$tr = link_tree($lid);

// ��� ��� �� ��������� �������� - �������� �� ������� ���������
if (!$tr && !$what) $rz .= translate('outerlinks_homemessage');

// ��������� �� ��������
$rz .= '<div id="outer_links">'."\n".start_edit_form().'
<p class="counts">'.translate('outerlinks_totalcount')." $lc ".translate('outerlinks_in')." $cc ".translate('outerlinks_categories')." &nbsp; ";
if ( in_array($what, array('all','new','click')) )
   $rz .= "<a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat')."</a>";
else
   $rz .= "<a href=\"".set_self_query_var('lid', 'all')."\">".translate('outerlinks_all')."</a>"; 
$rz .= "</p>\n";

switch ($what){
// ��������� �� ������ ������ � ��������� ���
case 'all': $rz .= '<h2><a href="'.unset_self_query_var('lid').'">'.translate('outerlinks_home')."</a></h2>\n".
                   outerlenks_all(0, '').
                   "<p><a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat')."</a></p>";
            break;
case 'new': $rz .= outerlenks_new();
            break;
case 'click': $rz .= outerlenks_click();
            break;
}

// ��� � ��������� ������� �� ������� ��������� �� ���������
if (count($_POST) && isset($_POST['search_by'])){
$rzr =  link_search();
if ($rzr) return $rz.$rzr."\n</div>";
}

// ��� �� ��������� ����� �� �����������
if (count($_POST)) edit_link($lid);

// ������ �� ������� �� �����
$l = db_select_1('*','outer_links',"`ID`=$lid");

// ��� � ���� �� �������� ���������� � �� �������� ��� ������ �� �����
if (isset($l['link']) && $l['link']>''){
 if (!show_adm_links()){ // ����� �� ����������� ���� ��� ����������� �� � �������������
   $q = "UPDATE `$tn_prefix"."outer_links` SET clicked = clicked+1 WHERE `ID`=".$l['ID'].";";
   mysqli_query($db_link,$q);
 }
 header('Location: '.$l['link']);
 die;
}

if (!$what || $lid) {

// �������� ���� ��� ��������� ��������
if ($tr) $rz .= $tr;
else $rz .="\n";

// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
// ���� �� �������
$seng = stored_value('outerlenks_sengin', 'https://www.google.bg/search?q=');

if (in_edit_mode()) $page_header .= '<script type="text/javascript"><!--
function linkradioclicked(){
var f = document.forms.link_edit_form;
var r = f.link_id.value;
var l = document.getElementById("lk"+r);
f.link.value = l.title;
f.title.value = l.text;
var p = l.parentElement;
if (p.className=="private") f.private.value = 1;
else  f.private.value = 0;
t = p.innerText;
var i = t.indexOf(" ");
f.place.value = t.substring(0, i);
t = l.parentElement.innerHTML;
i = t.indexOf("</a>") + 7;
t = t.substring(i);
var j = t.indexOf("<a ");
if (j>-1) f.comment.value = t.substring(0, j);
else f.comment.value = "";
}
--></script>';

// ������ � ��������� �� (���)�����������
$ca = db_select_m('*','outer_links',"`up`=$lid AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
//print_r($ca); die;
$p = current_pth(__FILE__);
foreach($ca as $c){// print_r($c); die;
 $cl = '';
 if ($c['private']) $cl = ' class="private"';
 $rz .= "<p$cl>".edit_radio($c['ID'],$c['place']).'<img src="'.$p.'folder.gif" alt=""> <a href="'.
        set_self_query_var('lid',$c['ID']).'" id="lk'.$c['ID'].'">'.stripslashes($c['Title'])."</a>";
 if (isset($c['Comment']) && $c['Comment']) $rz .= ' - '.stripslashes($c['Comment']);
 if (in_edit_mode()) $rz .= ' <a href="'.$seng.
    urlencode( iconv($site_encoding, 'UTF-8', stripslashes($c['Title'])) ).'" target="_blank">g</a>';
 $rz .= "</p>\n";
}

// ������ � ��������� �� ���������
$la = db_select_m('*','outer_links',"`up`=$lid AND `link`>''$qp ORDER BY `place`");
foreach($la as $l){
$cl = '';
if ($l['private']) $cl = ' class="private"';
$rz .= "<p$cl>".edit_radio($l['ID'],$l['place']).'<img src="'.$p.'go.gif" alt=""> <a href="'.
        set_self_query_var('lid',$l['ID']).'" title="'.urldecode($l['link']).
        '" target="_blank" id="lk'.$l['ID'].'">'.stripslashes($l['Title'])."</a>";
 if (isset($l['Comment']) && ($l['Comment']>" ")) $rz .= ' - '.stripslashes($l['Comment']);
 if (in_edit_mode()) $rz .= ' <a href="'.$seng.
    urlencode( iconv($site_encoding, 'UTF-8', stripslashes($l['Title'])) ).'" target="_blank">g</a>';
 $rz .= "</p>\n";
}

}

// ��������� �� ������� �� �������
if ($what!='all') $rz .= "\n".end_edit_form($lid).search_link_form();

// ������� "���-����"...
$rz .= '<p class="counts">'."\n";
if ($what!='new') $rz .= '<a href="'.set_self_query_var('lid','new').'">'.translate('outerlinks_new')."</a> &nbsp; ";
if ($what!='click') $rz .= '<a href="'.set_self_query_var('lid','click').'">'.translate('outerlinks_click')."</a> &nbsp; ";
$rz .= "</p>\n</div>\n";
return $rz;
}

// ��������� ���� �� ��������� ��������
// ------------------------------------
function link_tree($lid){
if (!$lid) return "";
$rz = ""; $lk = ''; $cm = '';
do {
  $l = db_select_1('*','outer_links',"`ID`=$lid");
  $lid = $l['up'];
  if ($rz) $rz = " > \n".$rz;
  if ($lk) $rz = '<a href="'.$lk.'">'.$l['Title']."</a>".$rz;
  else{
     $rz = '<span>'.$l['Title'].'</span>'.$rz;
     $cm = '';
     if (isset($l['Comment'])) $cm = $l['Comment'];
  }
  $lk = set_self_query_var('lid',$l['up']);
} while ($lid);
if ($rz) $rz = " > \n".$rz;
if ($lk) $rz = '<a href="'.$lk.'">'.translate('outerlinks_home')."</a>".$rz;
$rz = "<p class=\"link_tree\">\n".$rz."\n</p>\n";
if ($cm) $rz .= "<p>$cm</p>\n";
return $rz;
}

// ��������� �������� �� �������
// -----------------------------
function link_search(){
global $pth, $page_id; //die(print_r($_POST,true));
if (!isset($_POST['search_for']) || !$_POST['search_for']) return '';
$p = current_pth(__FILE__);
$q = '';
// ��������� �� SQL ��������
switch ($_POST['search_by']){
// �� ������� � ����������
case 'keyword': 
  $wa = explode(' ',$_POST['search_for'],4);
  foreach($wa as $i => $w){
    if ($i>2) break; // �� �� �� ����� ���� �� ������� 3 ����
    if ($q) $q .= ' AND ';
    $q .= "`Title` LIKE '%".addslashes($w)."%'";
  }
  $q1 = '';
  foreach($wa as $i => $w){
    if ($i>2) break; // �� �� �� ����� ���� �� ������� 3 ����
    if ($q1) $q1 .= ' AND ';
    $q1 .= "`Comment` LIKE '%".addslashes($w)."%'";
  }
  $q = "($q) OR ($q1)";
//  die($q);
  break;
// �� ������� � ��������
case 'url': 
  $q = "`link` LIKE '%".addslashes($_POST['search_for'])."%'";
  break;
}
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
// ��������� �� �������
$ra = db_select_m('*','outer_links',"$q$qp ORDER BY `place`");
$rz1 = ''; // �������� ���������
$rz2 = ''; // �������� �������
foreach($ra as $r){
  // ����
  $lk = set_self_query_var('lid',$r['ID']);
  // �������� �� ����������� �� �����
  if (!$r['up']) $t2 = translate('outerlinks_home'); 
  else $t2 = db_table_field('Title','outer_links',"`ID`=".$r['up']);
  // Title - ��� � ����
  if ($r['link']) $t1 = ($r['link']); else $t1 = '';
  // ���������� �� ���� � �����  
  $lk = '<a href="'.$lk.'" title="'.urldecode($t1).'" target="_blank">'.stripslashes($r['Title']).'</a>   '.
        '<a href="'.set_self_query_var('lid',$r['up']).'" title="'.urldecode($t2).'">>></a>';
  $lk .= "<br>\n";
  // �������� ��� ���������
  if ($r['link']) $rz2 .= '<img src="'.$p.'go.gif" alt=""> '.$lk;
  else  $rz1 .= '<img src="'.$p.'folder.gif" alt=""> '.$lk;
}
return '<p class="link_tree"><a href="'.$pth.'index.php?pid='.$page_id.'">'.translate('outerlinks_home').'</a>   '
.translate('outerlinks_found')." ".count($ra)."</p>
$rz1$rz2".search_link_form();
}

// ��������� ������� �� �������
// ----------------------------
function search_link_form(){
global $page_id;
return '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?pid='.$page_id.'">
<p class="search">'.translate('outerlinks_searchin').' 
<input type="radio" name="search_by" value="keyword" checked> '.translate('outerlinks_intitles').' 
<input type="radio" name="search_by" value="url"> '.translate('outerlinks_inurls').' 
<input type="text" name="search_for">
<input type="submit" value="'.translate('outerlinks_find').'">
</p></form>'."\n";
}

// ������ �� ������� �� �����������
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
if (!(r.checked || (k>-1)) ) { alert("Check a file to be deleted."); return; }
if (confirm("Do you really want to delete the checked link?")){
  f.action.value = "delete";
  f.submit();
}
}
--></script>
<form method="POST" name="link_edit_form">';
}

// ���� �� ������� �� �����������
// --------------------------------
function end_edit_form($i){
if (!in_edit_mode()) return '';
else return '
<input type="hidden" name="action" value="update">
<p>URL: <input type="text" name="link" size="50"> 
Place: <input type="text" name="place" size="5"> 
Group: <input type="text" name="up" size="5" value="'.$i.'">
Private: <input type="text" name="private" size="1"></p>
<p>Title: <input type="text" name="title" size="100"></p>
<p>Comment: <textarea name="comment" cols="83" rows="4" style="vertical-align:top"></textarea></p>
<input type="submit" value="Add/Update"> 
<input type="button" value="Delete" onclick="doDelete_link();">
</form>';
}

// ����� ������, ����� �� �������� � ����� �� �����������
// ------------------------------------------------------
function edit_radio($id,$p){
if (!in_edit_mode()) return '';
else return '<input type="radio" name="link_id" value="'.$id.'" onclick="linkradioclicked();">'.$p.' ';
}

// ��������/��������� �� ������� � ����� �� �����������
// ----------------------------------------------------
function edit_link($lid){
if ( ! in_edit_mode() ) return;
global $tn_prefix,$db_link;

$id = 0;
if (isset($_POST['link_id'])) $id = 1*$_POST['link_id'];

$q0 = " `$tn_prefix"."outer_links` ";
$q2 = '';

if ($_POST['action']=='delete'){
  if (db_table_field('link', 'outer_links', "`ID`=$id")==''){
     // ��� �������������� �� � ������ �� �� �������
     if (db_table_field('COUNT(*)', 'outer_links', "`up`=$id")) die('This is a not empty folder!');
  }
  $q = "DELETE FROM$q0 WHERE `ID`=$id;";
}
else {
  $q1 = "INSERT INTO$q0 SET `date_time_1`=NOW(), "; $q2 = ''; $q3 = ';';
  if ($id) { $q1 = "UPDATE$q0 SET "; $q3 = "WHERE `ID`=$id;"; }

  if ($_POST['link'])    $q2 .= "`link`='".addslashes($_POST['link'])."', ";
  if ($_POST['title'])   $q2 .= "`Title`='".addslashes($_POST['title'])."', ";
  if ($_POST['comment']) $q2 .= "`Comment`='".addslashes($_POST['comment'])."', ";
  if ($_POST['up']>'')   $q2 .= "`up`=".(1*$_POST['up']).", ";
  if ($_POST['private']>'') $q2 .= "`private`=".(1*$_POST['private']).", ";
  if ($_POST['place'])   $q2 .= "`place`=".(1*$_POST['place']).", ";
  else if (!$id && ($q2!="`up`=".(1*$_POST['up']).", "))
          $q2 .= "`place`=".(db_table_field('MAX(`place`)', 'outer_links', '1')+10).", ";
  if (!$q2) return '';
  $q = $q1.substr($q2,0,strlen($q2)-2)." ".$q3;
//  die($q);
}
//die(print_r($_POST,true).'<p>'.$q2.'<p>'."`up`=$lid, "." $lid");
if ($q2!="`up`=$lid, ") mysqli_query($db_link,$q);
}

//
// ��������� �� ������ ������ � ��������� ���
// ------------------------------------------
function outerlenks_all($up, $tx, $lv = 1){
$rz = '';
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
$dt = db_select_m('*', 'outer_links', "`up`=$up AND (`link`>'')$qp ORDER BY `place`");
foreach($dt as $d){
  $rz .= '<p><a href="'.
  set_self_query_var('lid',$d['ID']).'" title="'.urldecode($d['link']).
  '" target="_blank">'.stripslashes($d['Title'])."</a>";
  if ($d['Comment']) $rz .= ' - '.$d['Comment'];
  $rz .= "</p>\n";
}
$da = db_select_m('*', 'outer_links', "`up`=$up AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
foreach($da as $d){
  $t = '<h'.($lv+1).'><a href="'.set_self_query_var('lid',$d['ID']).'">'.$d['Title'].'</a></h'.($lv+1).">\n";
  if ($d['Comment']) $t .= '<p>'.$d['Comment']."</p>\n";
  $rz .= outerlenks_all( $d['ID'], $t, $lv + 1 );
}
$rz = "<div>\n$tx$rz</div>\n";
return $rz;
}

// ��������� �� "���-������"

function outerlenks_new(){
$rz = '<h2>'.translate('outerlinks_newest')."</h2>\n";
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links', "`link`>' '$qp ORDER BY `date_time_1` DESC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

// ��������� �� "���-����������"

function outerlenks_click(){
$rz = '<h2>'.translate('outerlinks_clicked')."</h2>\n";
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links', "`link`>' ' AND `clicked`>0$qp ORDER BY `clicked` DESC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

function outerlinks_showlinks($da){
$rz = '';
foreach($da as $d){
  $rz .= '<p><a href="'.set_self_query_var('lid',$d['ID']).'" title="'.urldecode($d['link']).'" target="_blank">'.
         stripslashes($d['Title']).'</a>';
  if ($d['up']){
     $t2 = db_table_field('Title', 'outer_links', "`ID`=".$d['up']);
     if (show_adm_links()) $rz .= ' &nbsp; '.$d['clicked'];
     $rz .= ' &nbsp; <a href="'.set_self_query_var('lid',$d['up']).'" title="'.urldecode($t2).'">'.">></a>";
  }
  $rz .= "</p>\n";
}
return $rz;
}
?>

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
include_once($idir.'lib/f_encode.php');

function outerlinks(){
return outer_links();
}

// ������ ������� �� ��������� �� �������� ������
// ----------------------------------------------
function outer_links(){

global $tn_prefix, $db_link, $site_encoding, $page_header, $adm_pth;

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

// ���� �� ����� ������
$sr4 = (count($_POST) && isset($_POST['search_by']));

// ��� ��� �� ��������� �������� - �������� �� ������� ���������
if (!$tr && !$what && !$sr4) $rz .= translate('outerlinks_homemessage');

// ��������� �� ��������
$rz .= '<div id="outer_links">'."\n".'
<p class="counts">'.
translate('outerlinks_totalcount')." $lc ".
translate('outerlinks_in')." $cc\n".
translate('outerlinks_categories')." &nbsp; ";

// ����������� "���� �����������" ��� "������� �� ���������"
if($what!='cat')
   $rz .= "<a href=\"".set_self_query_var('lid', 'cat')."\">".translate('outerlinks_catonly').
   "</a> &nbsp; ";
else
   $rz .= "<a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat').
          "</a> &nbsp; ";

// ����������� "������� �� ���������" ��� "������� �� ������"
if ( in_array($what, array('all','new','click')) )
   $rz .= "<a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat')."</a>";
else
   $rz .= "<a href=\"".set_self_query_var('lid', 'all')."\">".translate('outerlinks_all')."</a>";

$rz .= "</p>\n";

// ������� "���-����", "���-�����", "���-��������"
$rzl = '<p class="most">'."\n";
if ($what!='new') $rzl .= '<a href="'.set_self_query_var('lid','new').'">'.translate('outerlinks_new')."</a> &nbsp; ";
if ($what!='old') $rzl .= '<a href="'.set_self_query_var('lid','old').'">'.translate('outerlinks_old')."</a> &nbsp; ";
if ($what!='click') $rzl .= '<a href="'.set_self_query_var('lid','click').'">'.translate('outerlinks_click')."</a> &nbsp; ";
$rzl .= "</p>";

$p = current_pth(__FILE__);

// ��� � ��������� ������� �� ������� ��������� �� ���������
if ($sr4){
   $rzs =  link_search();
   if ($rzs) return $rz.$rzs.$rzl."\n</div>\n";
}

switch ($what){
// ��������� �� ������ ������ � ��������� ���
case 'all': $rz .= '<h2><a href="'.unset_self_query_var('lid').'">'.translate('outerlinks_home')."</a></h2>\n".
                   outerlenks_all(0, '').
                   "<p><a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat')."</a></p>";
            break;
// ��������� ���� �� �����������
case 'cat': $rz .= search_link_form().
                   '<p><img src="'.$p.'folder.gif" alt=""> '.
                   '<a href="'.unset_self_query_var('lid').'">'.translate('outerlinks_home').
                   "</a></p>\n".
                   outerlenks_cat(0, '');
            break;
// ��������� �� ���-������
case 'new': $rz .= outerlenks_new();
            break;
// ��������� �� ���-�������
case 'old': $rz .= outerlenks_old();
            break;
// ��������� �� ���-����������
case 'click': $rz .= outerlenks_click();
            break;
}

// ��� �� ��������� ����� �� �����������
if (count($_POST) && isset($_POST['link'])) edit_link($lid);

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

// ��������� �� ������� �� �������
if ($what!='all') $rz .= search_link_form();

// ���� �� SQL �������� �� ���������� �� private ���������
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
var j = -1;
if(t.substr(0,6)!=" href=") j = t.indexOf("<a ");
if (j>-1) f.comment.value = t.substring(0, j);
else f.comment.value = "";
}
function sid_clicked(a){
var u = document.forms.link_edit_form.up;
u.value = a.innerText;
}
function pl_clicked(a){
var u = document.forms.link_edit_form.place;
var v = a.innerText;
var l = v.slice(-1);
var n = "0";
if(l=="0") n = "5";
u.value = v.substring(0, v.length-1) + n;
}
--></script>
<style>
.sid { cursor: pointer; }
</style>';

// �������� �������� �� ������� �� �����������
$rz .= start_edit_form();

// ������ � ��������� �� (���)�����������
$ca = db_select_m('*','outer_links',"`up`=$lid AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
//print_r($ca); die;
$p = current_pth(__FILE__);
$rzc = '';
$tc = 0;
foreach($ca as $c){// print_r($c); die;
   $cl = '';
   if ($c['private']) $cl = ' class="private"';
   $sid = ''; // ID �� ������
   // ������� �� ���� � ����� �� �����������
   if(in_edit_mode()) $sid = '<span class="sid" onclick="sid_clicked(this);">'.$c['ID']."</span> ";
   $rzc .= "<p$cl>".edit_radio($c['ID'],$c['place']).'<img src="'.$p.'folder.gif" alt=""> '.$sid.
          '<a href="'.
          set_self_query_var('lid',$c['ID']).'" id="lk'.$c['ID'].'">'.stripslashes($c['Title'])."</a>";
   $t1 = uoterlinks_count($c, $qp);
   $tc += $t1;
   $rzc .= " - $t1";
   $rzc .= outerlinks_autocomment($c);
   if (in_edit_mode()) $rzc .= ' <a href="'.$seng.
      urlencode( iconv($site_encoding, 'UTF-8', stripslashes($c['Title'])) ).'" target="_blank">g</a> '.
      '<a href="'.$adm_pth.'duplicate_record.php?t=outer_links&r='.$c['ID'].'">2</a>';
   $rzc .= "</p>\n";
}
if (count($ca)) $rz .= '<p>'.count($ca).' '.translate('outerlinks_sub').", $tc ".translate('outerlinks_tcs')."</p>\n".$rzc;


// ������ � ��������� �� ���������
$la = db_select_m('*','outer_links',"`up`=$lid AND `link`>''$qp ORDER BY `place`");
if (count($la)) $rz .= '<p>'.count($la).' '.translate('outerlinks_links')."</p>\n";
foreach($la as $l){
$cl = '';
if ($l['private']) $cl = ' class="private"';
$rz .= "<p$cl>".edit_radio($l['ID'],$l['place']).'<img src="'.$p.'go.gif" alt=""> <a href="'.
        set_self_query_var('lid',$l['ID']).'" title="'.urldecode($l['link']).
        '" target="_blank" id="lk'.$l['ID'].'">'.stripslashes($l['Title'])."</a>";
 $rz .= outerlinks_autocomment($l);
 if (in_edit_mode()) $rz .= ' <a href="'.$seng.
    urlencode( iconv($site_encoding, 'UTF-8', stripslashes($l['Title'])) ).'" target="_blank">g</a> '.
    '<a href="'.$adm_pth.'duplicate_record.php?t=outer_links&r='.$l['ID'].'">2</a>';
 $rz .= "</p>\n";
}

}

// ��������, ��������� ���������� ���������
setcookie('lid',$lid, 0, '/');

// ���� �� ������� �� �����������
if (($what!='all') && ($what!='cat')) $rz .= "\n".end_edit_form($lid);

$rz .= $rzl."\n</div>\n";

return $rz;

} // ���� �� function outer_links()



// ��������� ���� �� ��������� ��������
// ------------------------------------
function link_tree($lid){
global $page_data, $page_title;
if (!$lid) return "";
$rz = ""; $lk = ''; $cm = '';
do {
  $l = db_select_1('*','outer_links',"`ID`=$lid");
  $lid = $l['up'];
  if ($rz) $rz = " > \n".$rz;
  if ($lk) $rz = '<a href="'.$lk.'">'.$l['Title']."</a>".$rz;
  else{
     if (!$page_title) $page_title = translate($page_data['title'],false);
     $page_title .= ' - '.$l['Title'];
     $rz = '<span><a href="'.$lk.'">'.$l['Title'].'</a></span>'.$rz;
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
global $pth, $page_id;
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
.translate('outerlinks_found')." ".count($ra)." (".substr($_POST['search_for'],0,30).")</p>
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
// ��������� �� ����� �� ajax ������
if (window.XMLHttpRequest) ajaxO = new XMLHttpRequest();
else ajaxO = new ActiveXObject("Microsoft.XMLHTTP");
// ��������� �� ��� �������� �� ����� "Delete"
function doDelete_link(){
var f = document.forms.link_edit_form;
var r = f.link_id;
var k = -1;
for(i=0; i<r.length; i++) if (r[i].checked) k = i;
if (!(r.checked || (k>-1)) ) { alert("Check a link to be deleted."); return; }
if (confirm("Do you really want to delete the checked link?")){
  f.action.value = "delete";
  f.submit();
}
}
// ��������� �� ��� ����������� �� ������ � ���� Title:
function enter_title_field(){
// ������ �� ���� URL
var u = document.forms.link_edit_form.link.value;
// �� �� ����� ���� ��� ������ � ������
if(!u) return;
// ���� Title
var t = document.forms.link_edit_form.title;
// ��� �� � ������ �� �� ����� ����
if(t.value.length>0) return;
// �������� �� ������
ajaxO.open("GET", "'.current_pth(__FILE__).'get_site_title.php?url=" + encodeURIComponent(u) + "&a=" + Math.random(), false);
ajaxO.send(null);
// HTML ��� ������� �� ������
var h = ajaxO.responseText;
t.value = h;
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
Group: <input type="text" name="up" size="5" value="'.$i.'" onfocus="this.select();">
Private: <input type="text" name="private" size="1"></p>
<p>Title: <input type="text" name="title" size="100" onfocus="enter_title_field();"></p>
<p>Comment: <textarea name="comment" cols="83" rows="4" style="vertical-align:top"></textarea></p>
<input type="submit" value="Add/Update"> 
<input type="button" value="Delete" onclick="doDelete_link();">
</form>';
}

// ����� ������, ����� �� �������� � ����� �� �����������
// ------------------------------------------------------
function edit_radio($id,$p){
if (!in_edit_mode()) return '';
else return '<input type="radio" name="link_id" value="'.$id.'" onclick="linkradioclicked();">'.
            '<span onclick="pl_clicked(this);" class="sid">'.$p.'</span> ';
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
}
if ( ($q2!="`up`=$lid, ") && (($_POST['action']=='delete') || $_POST['link'] || $_POST['title']) ) mysqli_query($db_link,$q);
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
  $pr = '';
  if($d['private']) $pr = ' class="private"';
  $rz .= "<p$pr><a href=\"".
  set_self_query_var('lid',$d['ID']).'" title="'.urldecode($d['link']).
  '" target="_blank">'.stripslashes($d['Title'])."</a>";
  if ($d['Comment']) $rz .= outerlinks_autocomment($d);
  $rz .= "</p>\n";
}
$da = db_select_m('*', 'outer_links', "`up`=$up AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
foreach($da as $d){
  $pr = '';
  if($d['private']) $pr = ' class="private"';
  $t = '<h'.($lv+1).$pr.'><a href="'.set_self_query_var('lid',$d['ID']).'">'.$d['Title'].'</a></h'.($lv+1).">\n";
  if ($d['Comment']) $t .= '<p>'.outerlinks_autocomment($d)."</p>\n";
  $rz .= outerlenks_all( $d['ID'], $t, $lv + 1 );
}
$rz = "<div>\n$tx$rz</div>\n";
return $rz;
}

//
// ��������� ���� �� ������ ��������� � ��������� ���
// --------------------------------------------------
function outerlenks_cat($up, $tx, $lv = 1){
$rz = '';
$p = current_pth(__FILE__);
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
$da = db_select_m('*', 'outer_links', "`up`=$up AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
foreach($da as $d){
  $n = '';
  if(in_edit_mode()) $n = $d['ID'].' ';
  $t = '<p style="margin-left:'.(20*$lv).'px" class="lv'.$lv.'"><img src="'.$p.'folder.gif" alt=""> '.$n.
       '<a href="'.set_self_query_var('lid',$d['ID']).'">'.$d['Title'].
       "</a></p>\n";
  $rz .= outerlenks_cat( $d['ID'], $t, $lv + 1 );
}
$rz = "$tx$rz";
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

// ��������� �� "���-�������"

function outerlenks_old(){
$rz = '<h2>'.translate('outerlinks_oldest')."</h2>\n";
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links', "`link`>' '$qp ORDER BY `date_time_1` ASC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

// ��������� �� "���-����������"

function outerlenks_click(){
$rz = '<h2>'.translate('outerlinks_clicked')."</h2>\n";
// ������� �� ���������� �� private ���������
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links',
      "`link`>' ' AND `clicked`>0$qp ORDER BY `clicked` DESC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

function outerlinks_showlinks($da){
$rz = '';
foreach($da as $d){
  $rz .= '<p><a href="'.set_self_query_var('lid',$d['ID']).'" title="'.urldecode($d['link']).
         '" target="_blank">'.stripslashes($d['Title']).'</a>';
  if ($d['up']){
     $t2 = db_table_field('Title', 'outer_links', "`ID`=".$d['up']);
  }
  else $t2 = translate('outerlinks_home', false);
  if (show_adm_links()) $rz .= ' &nbsp; '.$d['clicked'];
  $rz .= ' &nbsp; <a href="'.set_self_query_var('lid',$d['up']).'" title="'.urldecode($t2).'">'.
         ">></a>";
  $rz .= "</p>\n";
}
return $rz;
}

// ����������� ��������
function outerlinks_autocomment($d){
if (!isset($d['Comment'])) return '';
if ($d['Comment']>' ') return ' - '.stripslashes($d['Comment']);
if (substr($d['link'],-4)=='.pdf') return encode(' - pdf ����');
if (substr($d['link'],-4)=='.doc') return encode(' - doc ����');
if (!(strpos($d['link'], 'scholar.google.bg')===false)) return ' - '.translate('outerlinks_sresult').' scholar.google.bg';
if (!(strpos($d['link'], 'google.bg')===false)) return ' - '.translate('outerlinks_sresult').' google.bg';
if (!(strpos($d['link'], 'bg.wikipedia.org')===false)) return ' - '.translate('outerlinks_wiki').'bg.wikipedia.org';
if (!(strpos($d['link'], 'en.wikipedia.org')===false)) return ' - '.translate('outerlinks_wiki').'en.wikipedia.org';
}

function uoterlinks_count($c, $qp){
$c1 = db_table_field('COUNT(*)','outer_links', "`up`=".$c['ID']." AND `link`>' ' $qp ORDER BY `place`");
$dt = db_select_m('ID',         'outer_links', "`up`=".$c['ID']." AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
if(count($dt)) foreach($dt as $d) $c1 += uoterlinks_count($d, $qp);
return $c1;
}

?>

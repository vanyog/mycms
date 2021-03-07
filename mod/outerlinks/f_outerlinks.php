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
include_once($idir.'lib/f_db_places10.php');
include_once($idir.'lib/f_encode.php');

function outerlinks(){
return outer_links();
}

// Главна функция за показване на Интернет връзки
// ----------------------------------------------
function outer_links(){

global $tn_prefix, $db_link, $site_encoding, $page_header, $adm_pth, $rewrite_on, $page_id;

if (!$site_encoding) $site_encoding = 'windows-1251';

// Общ брой на връзките
$lc = db_table_field('COUNT(*)','outer_links',"`link`>'' AND (1*`link`=0)");

// Общ брой на категориите
$cc = db_table_field('COUNT(*)','outer_links',"`link`=''");

$rz = '';

// Номер на линка, за отваряне
$lid = 0;

// Какво да се покаже
$what = '';

if (isset($_GET['lid'])){
  $what = strtolower($_GET['lid']);
  if(is_numeric($_GET['lid'])) $lid = 1*$_GET['lid'];
  else $lid = 0;
}

// Ако са изпратени данни за редактиране
if (count($_POST) && isset($_POST['link'])) edit_link($lid);

// Четене на данните за линка
$l = db_select_1('*','outer_links',"`ID`=$lid");

// Ако е линк се изброява кликването и се препраща към адреса на линка
if (isset($l['link']) && $l['link']>''){
 if(is_numeric($l['link'])) $l = db_select_1('*','outer_links',"`ID`=".$l['link']);
 if (!show_adm_links()){ // Броят се кликванията само ако посетителят не е администратор
   $q = "UPDATE `$tn_prefix"."outer_links` SET clicked = clicked+1 WHERE `ID`=".$l['ID'].";";
   mysqli_query($db_link,$q);
 }
 if($l['private'].""){ // Ако е лична връзка
   if (!show_adm_links()) die('Private link. Access denied.');
 }
 if(isset($_GET['just']) && ($_GET['just']=='data')) die("aaa");// die(json_encode($l));
 header('Location: '.$l['link']);
 die;
}

// Път към началната страница
$tr = link_tree($lid);

// Дали се прави търсне
$sr4 = (count($_POST) && isset($_POST['search_by']));

// Ако сме на началната страница - добавяне на начално съобщение
if (!$tr && !$what && !$sr4) $rz .= translate('outerlinks_homemessage');

// Показване на бройките
$rz .= '<div id="outer_links">'."\n".
translate('outer_links_intro').
'<p class="counts" id="counts">'.
translate('outerlinks_totalcount')." $lc ".
translate('outerlinks_in')." $cc\n".
translate('outerlinks_categories')." &nbsp; ";


if($cc){ // Ако има категории

// Хипервръзка "Само категориите" или "Преглед по категории"
if($what!='cat')
   if($rewrite_on)
      $rz .= "<a href=\"".set_self_query_var('lid', 'cat')."#outer_links\">".translate('outerlinks_catonly')."</a> &nbsp; ";
   else
      $rz .= "<a href=\"".set_self_query_var('lid', 'cat')."#outer_links\">".translate('outerlinks_catonly')."</a> &nbsp; ";
else
   if($rewrite_on)
      $rz .= "<a href=\"".unset_self_query_var('lid')."#outer_links\">".translate('outerlinks_cat')."</a> &nbsp; ";
   else
      $rz .= "<a href=\"".unset_self_query_var('lid')."#outer_links\">".translate('outerlinks_cat')."</a> &nbsp; ";

// Хипервръзка "Преглед по категории" или "Преглед на всички"
if ( in_array($what, array('all','new','click')) )
   $rz .= "<a href=\"".unset_self_query_var('lid')."#outer_links\">".translate('outerlinks_cat')."</a>";
else
   $rz .= "<a href=\"".set_self_query_var('lid', 'all')."#outer_links\">".translate('outerlinks_all')."</a>";

}

$rz .= "</p>\n";

// Линкове "Най-нови", "Най-стари", "Най-кликвани"
if($lc>10){
$rzl = '<p class="most">'."\n";
if ($what!='new') $rzl .= '<a href="'.set_self_query_var('lid','new').'#outer_links">'.translate('outerlinks_new')."</a> &nbsp; ";
if ($what!='old') $rzl .= '<a href="'.set_self_query_var('lid','old').'#outer_links">'.translate('outerlinks_old')."</a> &nbsp; ";
if ($what!='click') $rzl .= '<a href="'.set_self_query_var('lid','click').'#outer_links">'.translate('outerlinks_click')."</a> &nbsp; ";
$rzl .= "</p>";
}
else $rzl = '';

$p = current_pth(__FILE__);

// Ако е извършено търсене се показва резултата от търсенето
if ($sr4){
   $rzs =  link_search();
   if ($rzs) return $rz.$rzs.$rzl."\n</div>\n";
}

switch ($what){
// Показване на всички връзки в разгърнат вид
case 'all': $rz .= '<h2><a href="'.unset_self_query_var('lid').'">'.translate('outerlinks_home')."</a></h2>\n".
                   outerlenks_all(0, '').
                   "<p><a href=\"".unset_self_query_var('lid')."\">".translate('outerlinks_cat')."</a></p>";
            break;
// Показване само на категориите
case 'cat': $rz .= search_link_form().
                   '<p><img src="'.$p.'folder.png" alt=""> '.
                   '<a href="'.unset_self_query_var('lid').'">'.translate('outerlinks_home').
                   "</a></p>\n".
                   outerlenks_cat(0, '');
            break;
// Показване на най-новите
case 'new': $rz .= outerlenks_new();
            break;
// Показване на най-старите
case 'old': $rz .= outerlenks_old();
            break;
// Показване на най-клекваните
case 'click': $rz .= outerlenks_click();
            break;
}

if (!$what || $lid) {

// Добавяне пътя към началната страница
if ($tr) $rz .= $tr;
else $rz .="\n";

// Показване на формата за търсене
if ( $cc && ($what!='all') ) $rz .= search_link_form();

$page_header .= "<script>\n";

$spage = stored_value('outerlinks_spage');

if($spage){
// Невидима форма за отваряне на страницат "Търсене на информация", при щракване на "лупата"
$rz .= '<form method="POST" action="'.$spage.'" id="gotsearchpage" target="_blank">
<input type="hidden" name="words" value="aaa bbb">
</form>
';
$GLOBALS['page_header'] .= 'function onSearchClick(a){
var f = document.getElementById("gotsearchpage");
var t = a.parentElement.innerText;'.
// Премахване броя на линковете след имената на раздели
'var i = t.search(/ \- \d*/i);
if(i<0) i = t.length;
t = t.substring(0, i);
t = t.replace(/\"/g, "&quot;");
';
if(in_edit_mode()) $page_header .= 't = t.replace(/( *\d+ *){1,2}/g,"");
';
$page_header .= 'f.words.value = t;
f.submit();
}
';
}

$page_header .= 'function duDuplicate(e){
if(confirm("Duplicate link?")) document.location = e; //window.open(e);
}
</script>
<style>
.sid { cursor: pointer; }
</style>
';

// Част от SQL заявката за пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
// Сайт за търсене
$seng = stored_value('outerlenks_sengin', 'https://www.google.bg/search?q=');
//die("-$page_header-");
if (in_edit_mode()) $page_header .= '<script>
function chCaseClick(){
var f = document.forms.link_edit_form.title;
var s = f.selectionStart;
var e = f.selectionEnd;
var t = f.value;
if(e>s)
  t = t.substr(0,s) + t.substring(s, e).toLowerCase() + t.substring(e);
else
  t = t.substring(0,1) + t.substring(1).toLowerCase();
f.value = t;
}
function linkradioclicked(id,fi){
var f = document.forms.link_edit_form;
var r = f.link_id.value;
var l = document.getElementById("lk"+r);
var t = "";
f.ID.value = id;
f.link.value = l.title; // Link
f.title.value = l.innerHTML; // Title
var p = l.parentElement;
t = p.innerText;
var i = t.indexOf(" ", 1);
f.place.value = t.substring(0, i); // Place
if (p.className=="private") f.private.value = 1;
else  f.private.value = 0; // Private
t = l.parentElement.innerHTML;
i = t.indexOf("</a>") + 7;
t = t.substring(i);
if(t.substring(0,8)==" href=\"/") t = "";
else {
  var a = t.split(" ");
  t = a.slice(fi, a.length - 10).join(" ");
}
if(r != id) {
  f.link.value = r;
  f.title.value = "";
}
f.comment.value = t;
}
function sid_clicked(a){
var u = document.forms.link_edit_form.up;
u.value = a.innerText;
u.focus();
}
function pl_clicked(a,e){
var u = document.forms.link_edit_form.place;
var v = a.innerText;
var i = a.parentElement.children[0].value;
u.value = i;
u.select();
document.execCommand("copy");
var n = Number(v);
if(e.ctrlKey || e.metaKey) n -= 5; else n += 5;
u.value = "" + n;
u.focus();
}
</script>
';

// Добавяне началото на формата за редактиране
$rz .= start_edit_form();

// Четене и показване на (под)категориите
$ca = db_select_m('*','outer_links',"`up`=$lid AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");

$p = current_pth(__FILE__);
$rzc = '';
$tc = 0;
foreach($ca as $c){
   $cl = '';
   if ($c['private']) $cl = ' class="private"';
   $sid = ''; // ID на записа
   // Показва се само в режим на редактиране
   if(in_edit_mode()) $sid = '<span class="sid" onclick="sid_clicked(this);" title="Group ID">'.$c['ID']."</span> \n";
   $rzc .= "<p$cl>".edit_radio($c, $c['ID'], 2).'<img src="'.$p.'folder.png" alt=""> '."\n".$sid.
          '<a href="'.set_self_query_var('lid',$c['ID']).'#outer_links" id="lk'.$c['ID'].'">'.stripslashes($c['Title'])."</a>\n";
   $t1 = uoterlinks_count($c, $qp);
   $tc += $t1;
   $rzc .= " - $t1";
   $rzc .= outerlinks_autocomment($c);
   if (in_edit_mode()) $rzc .= ' <a href="'.$adm_pth.'duplicate_record.php?t=outer_links&r='.$c['ID'].
                               '" onclick="duDuplicate(this);return false;">2</a>';
   if($spage) $rzc .= ' <img class="sid" src="'.$p.'search.png" alt="" onclick="onSearchClick(this);"> ';
   $rzc .= "</p>\n";
}
if (count($ca)) $rz .= '<p>'.count($ca).' '.translate('outerlinks_sub').", $tc ".translate('outerlinks_tcs')."</p>\n".$rzc;


// Четене и показване на линковете
$la = db_select_m('*','outer_links',"`up`=$lid AND `link`>'' $qp ORDER BY `place` ASC");
if (count($la)) $rz .= '<p>'.count($la).' '.translate('outerlinks_links')."</p>\n";
foreach($la as $l){
  $l2 = $l['ID'];
  $tb = 'target="_blank" ';
  if(is_numeric($l['link'])/* && $l['link']*/){
    $l1 = db_select_1('*', 'outer_links', '`ID`='.$l['link']);
//    if($l1)
    {
//      $l1['place'] = $l['place'];
      $l['link'] = $l1['link'];
      $l['Title'] = $l1['Title'];
      $l['Comment'] = $l1['Comment'];
    }
  }
  else $l1 = $l;
  // Премахване на target="_blank", когато линкът е към друг раздел с връзки
  if(($l1['ID']!=$l2) && empty($l1['link'])) $tb = '';
  $cl = '';
  if ($l['private']) $cl = ' class="private"';
  $rz .= "<p$cl>".edit_radio($l1, $l2).'<img src="'.$p.'go.gif" alt=""> '."\n".'<a href="';
  if($rewrite_on)
    $rz .= "/$page_id/lid/".$l1['ID']."/";
  else
    $rz .= set_self_query_var('lid',$l1['ID']);
  $rz .= '" title="'.urldecode($l['link']).
        '" '.$tb.'id="lk'.$l1['ID'].'">'.stripslashes($l['Title'])."</a>";
  $rz .= outerlinks_autocomment($l);
  if (in_edit_mode()) $rz .= ' <a href="'.$adm_pth.'duplicate_record.php?t=outer_links&r='.$l['ID'].
                            '" onclick="duDuplicate(this);return false;">2</a>';
  if($spage) $rz .= ' <img class="sid" src="'.$p.'search.png" alt="" onclick="onSearchClick(this);"> ';
  $rz .= "</p>\n";
}

}

// Бисквита, запомняща отворената категория
setcookie('lid',$lid, 0, '/');

// Край на формата за редактиране
if (($what!='all') && ($what!='cat')) $rz .= "\n".end_edit_form($lid);

$rz .= $rzl."\n</div>\n";

return $rz;

} // Край на function outer_links()



// Показване пътя до началната страница
// ------------------------------------
function link_tree($lid){
global $page_data, $page_title;
if (!$lid) return "";
$rz = ""; $lk = ''; $cm = '';
do {
  $l = db_select_1('*','outer_links',"`ID`=$lid");
  $lid = $l['up'];
  if ($rz) $rz = " > \n".$rz;
  if ($lk) $rz = '<a href="'.$lk.'#outer_links">'.$l['Title']."</a>".$rz;
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
if ($lk) $rz = '<a href="'.$lk.'#outer_links">'.translate('outerlinks_home')."</a>".$rz;
$rz = "<p class=\"link_tree\">\n".$rz."\n</p>\n";
if ($cm) $rz .= "<p>$cm</p>\n";
return $rz;
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
    if ($i>2) break; // за да се търси само по първите 3 думи
    if ($q) $q .= ' AND ';
    $q .= "`Title` LIKE '%".addslashes($w)."%'";
  }
  $q1 = '';
  foreach($wa as $i => $w){
    if ($i>2) break; // за да се търси само по първите 3 думи
    if ($q1) $q1 .= ' AND ';
    $q1 .= "`Comment` LIKE '%".addslashes($w)."%'";
  }
  $q = "($q) OR ($q1)";
//  die($q);
  break;
// За търсене в адресите
case 'url': 
  $q = "`link` LIKE '%".addslashes($_POST['search_for'])."%'";
  break;
}
// За пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
// Извличане на данните
$ra = db_select_m('*','outer_links',"$q$qp ORDER BY `place`");
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
  $lk = '<a href="'.$lk.'" title="'.urldecode($t1).'" target="_blank">'.stripslashes($r['Title']).'</a>   '.
        '<a href="'.set_self_query_var('lid',$r['up']).'#outer_links" title="'.urldecode($t2).'">>></a>';
  $lk .= "<br>\n";
  // Добавяне към резултата
  if ($r['link']) $rz2 .= '<img src="'.$p.'go.gif" alt=""> '.$lk;
  else  $rz1 .= '<img src="'.$p.'folder.png" alt=""> '.$lk;
}
return '<p class="link_tree"><a href="'.$pth.'index.php?pid='.$page_id.'">'.translate('outerlinks_home').'</a>   '
.translate('outerlinks_found')." ".count($ra)." (".substr($_POST['search_for'],0,30).")</p>
$rz1$rz2".search_link_form();
}

// Показване формата за търсене
// ----------------------------
function search_link_form(){
global $page_id;
return '<form method="POST" action="'.$_SERVER['SCRIPT_NAME'].'?pid='.$page_id.'#outer_links">
<p class="search">'.translate('outerlinks_searchin').' 
<input type="radio" name="search_by" value="keyword" checked> '.translate('outerlinks_intitles').' 
<input type="radio" name="search_by" value="url"> '.translate('outerlinks_inurls').' 
<input type="text" name="search_for">
<input type="submit" value="'.translate('outerlinks_find').'">
</p></form>'."\n";
}

// Начало на формата за редактиране
// --------------------------------
function start_edit_form(){
global $page_id;
if (!in_edit_mode()) return '';
else return '
<script>
// Създаване на обект за ajax заявки
if (window.XMLHttpRequest) ajaxO = new XMLHttpRequest();
else ajaxO = new ActiveXObject("Microsoft.XMLHTTP");
// Изпълнява се при щракване на бутон "Delete"
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
// Изпълнява се при преместване на фокуса в поле Title:
function enter_title_field(){
// Адреса от поле URL
var u = document.forms.link_edit_form.link.value;
// Не са прави нищо ако адреса е празен
if(!u) return;
// Поле Title
var t = document.forms.link_edit_form.title;
// Ако не е празно се се прави нищо
if(t.value.length>0) return;
// Отваряне на адреса
ajaxO.open("GET", "'.current_pth(__FILE__).'get_site_title.php?url=" + encodeURIComponent(u) + "&a=" + Math.random(), false);
ajaxO.send(null);
// HTML код получен от адреса
var h = ajaxO.responseText;
t.value = h;
}
function onPrivateFocus(e){
var a = e.value;
if(a=="0") a = "1";
else if(a=="1") a = "0";
e.value = a;
}
</script>
<form method="POST" name="link_edit_form">';
}

// Край на формата за редактиране
// --------------------------------
function end_edit_form($i){
global $adm_pth;
if (!in_edit_mode()) return '';
else return '
<input type="hidden" name="action" value="update">
<input type="hidden" name="ID" value="0">
<p class="fef">URL: <input type="text" name="link" size="50"></p>
<p>Title: <input type="text" name="title" size="100" onfocus="enter_title_field();">
<input type="button" value="Aa" onclick="chCaseClick();"></p>
<p>Comment: <textarea name="comment" cols="83" rows="4" style="vertical-align:top"></textarea></p>
<p>Place: <input type="text" name="place" size="5">
Group: <input type="text" name="up" size="5" value="'.$i.'" onfocus="this.select();">
Private: <input type="text" name="private" size="1" onfocus="onPrivateFocus(this)"></p>
<input type="submit" value="Add/Update">
<input type="button" value="Delete" onclick="doDelete_link();">
<input type="reset">
</form>
<script>document.forms["link_edit_form"].link.focus();</script>';
}

// Радио бутони, които се показват в режим на редактиране
// ------------------------------------------------------
function edit_radio($d,$l2,$f=0){
if (!in_edit_mode()) return '';
else return '<input type="radio" name="link_id" value="'.$d['ID'].'" onclick="linkradioclicked('."$l2,$f".');">'."\n".
            '<span onclick="pl_clicked(this,event);" class="sid" title="Place">'.$d['place']."</span> \n";
}

// Обработка на данните, изпратени с $_POST в режим на редактиране
// ----------------------------------------------------
function edit_link($lid){
if ( ! in_edit_mode() ) return;
global $tn_prefix,$db_link;

$id = 0;
if (isset($_POST['link_id']) && is_numeric($_POST['link_id'])) $id = $_POST['link_id'];
else $id = 0;
if(is_numeric($_POST['link'])){
    $idl = db_table_field('`ID`', 'outer_links', "`ID`=".addslashes($_POST['link']));
    if(!($idl>0)) die('ID = '.$_POST['link'].' do not exists');
    $id = intval($_POST['ID']);
}
//die(print_r($_POST,true));

$q0 = " `$tn_prefix"."outer_links` ";
$q2 = '';

if ($_POST['action']=='delete'){
  if (db_table_field('link', 'outer_links', "`ID`=$id")==''){
     // Ако подкатегорията не е празна не се изтрива
     if (db_table_field('COUNT(*)', 'outer_links', "`up`=$id")) die('This is a not empty folder!');
  }
  if(db_table_field('ID', 'bib_title', "`url`=$id")) die(translate('outer_links_inbib'));
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
          $q2 .= "`place`=".(intval(db_table_field('MAX(`place`)', 'outer_links', '1'))+10).", ";
  if (!$q2) return '';
  $q = $q1.substr($q2,0,strlen($q2)-2)." ".$q3;
}
if ( ($q2!="`up`=$lid, ") && (($_POST['action']=='delete') || $_POST['link'] || $_POST['title']) ){
  mysqli_query($db_link,$q);
  db_places10('outer_links');
}
}

//
// Показване на всички връзки в разгърнат вид
// ------------------------------------------
function outerlenks_all($up, $tx, $lv = 1){
$rz = '';
// Добавка за пропускане на private линковете
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
  $t = '<h'.($lv+1).$pr.'><a href="'.set_self_query_var('lid',$d['ID']).'#outer_links">'.$d['Title'].'</a></h'.($lv+1).">\n";
  if ($d['Comment']) $t .= '<p>'.outerlinks_autocomment($d)."</p>\n";
  $rz .= outerlenks_all( $d['ID'], $t, $lv + 1 );
}
$rz = "<div>\n$tx$rz</div>\n";
return $rz;
}

//
// Показване само на всички категории в разгърнат вид
// --------------------------------------------------
function outerlenks_cat($up, $tx, $lv = 1){
$rz = '';
$p = current_pth(__FILE__);
// За пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = 'AND `private`=0';
$da = db_select_m('*', 'outer_links', "`up`=$up AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
foreach($da as $d){
  $n = '';
  if(in_edit_mode()) $n = $d['ID'].' ';
  $t = '<p style="margin-left:'.(20*$lv).'px" class="lv'.$lv.'"><img src="'.$p.'folder.png" alt=""> '.$n.
       '<a href="'.set_self_query_var('lid',$d['ID']).'#outer_links">'.$d['Title'].
       "</a></p>\n";
  $rz .= outerlenks_cat( $d['ID'], $t, $lv + 1 );
}
$rz = "$tx$rz";
return $rz;
}

// Показване на "най-новите"

function outerlenks_new(){
$rz = '<h2>'.translate('outerlinks_newest')."</h2>\n";
// Добавка за пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = '`private`=0';  else $qp = '1';
$da = db_select_m('*', 'outer_links', "$qp ORDER BY `date_time_1` DESC LIMIT 0,20");
return $rz.outerlinks_showlinks($da);
}

// Показване на "най-старите"

function outerlenks_old(){
$rz = '<h2>'.translate('outerlinks_oldest')."</h2>\n";
// Добавка за пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links', "`link`>' '$qp ORDER BY `date_time_1` ASC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

// Показване на "най-кликваните"

function outerlenks_click(){
$rz = '<h2>'.translate('outerlinks_clicked')."</h2>\n";
// Добавка за пропускане на private линковете
$qp = '';
if (!in_edit_mode()) $qp = ' AND `private`=0';
$da = db_select_m('*', 'outer_links',
      "`link`>' ' AND `clicked`>0$qp ORDER BY `clicked` DESC LIMIT 0,10");
return $rz.outerlinks_showlinks($da);
}

function outerlinks_showlinks($da){
$rz = '';
$p = current_pth(__FILE__);
foreach($da as $d){
  if($d['link']) $img = '<img src="'.$p.'go.gif" alt=""> ';
  else $img = '<img src="'.$p.'folder.png" alt=""> ';
  $rz .= '<p>'.$img.'<a href="'.set_self_query_var('lid',$d['ID']).'" title="'.urldecode($d['link']).
         '" target="_blank">'.stripslashes($d['Title']).'</a>';
  if ($d['up']){
     $t2 = db_table_field('Title', 'outer_links', "`ID`=".$d['up']);
  }
  else $t2 = translate('outerlinks_home', false);
  if (show_adm_links()) $rz .= ' &nbsp; '.$d['clicked'];
  $rz .= ' &nbsp; <a href="'.set_self_query_var('lid',$d['up']).'#outer_links" title="'.urldecode($t2).'">'.
         ">></a>";
  $rz .= "</p>\n";
}
return $rz;
}

// Автоматичен коментар
function outerlinks_autocomment($d){
if (!isset($d['Comment'])) return '';
if ($d['Comment']>' ') return ' - '.stripslashes($d['Comment']);
if (substr($d['link'],-4)=='.pdf') return encode(' - pdf файл');
if (substr($d['link'],-4)=='.doc') return encode(' - doc файл');
if (!(strpos($d['link'], 'scholar.google.bg')===false)) return ' - '.translate('outerlinks_sresult').'scholar.google.bg';
if (!(strpos($d['link'], 'google.bg')===false)) return ' - '.translate('outerlinks_sresult').'google.bg';
if (!(strpos($d['link'], 'bg.wikipedia.org')===false)) return ' - '.translate('outerlinks_wiki').'bg.wikipedia.org';
if (!(strpos($d['link'], 'en.wikipedia.org')===false)) return ' - '.translate('outerlinks_wiki').'en.wikipedia.org';
}

// Брой връзки в категория и нейните подкатегории

function uoterlinks_count($c, $qp){
$c1 = db_table_field('COUNT(*)','outer_links', "`up`=".$c['ID']." AND `link`>' ' $qp ORDER BY `place`");
$dt = db_select_m('ID',         'outer_links', "`up`=".$c['ID']." AND (`link`='' OR `link` IS NULL)$qp ORDER BY `place`");
if(count($dt)) foreach($dt as $d) $c1 += uoterlinks_count($d, $qp);
return $c1;
}

?>

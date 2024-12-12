<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2021  Vanyo Georgiev <info@vanyog.com>

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

// Модул за търсене в сайта

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_db_select_m.php');

global $page_header, $main_index;

$page_header .= '<style>
#search_button{ background-image:url('.current_pth(__FILE__).'images/search19x19.png); background-repeat: no-repeat; background-position: center; }
#search_clear { background-image:url('.current_pth(__FILE__).'images/clear19x19.png); background-repeat: no-repeat; background-position: center; }
</style>
<script>
function doSiteSearch(){
var f = document.forms.site_search_form;
var t = f.searchtext.value;
if(Number.isInteger(1*t)){
  if(confirm("'.translate('sitesearch_gpn', false).'"+t+"?")){
    document.location = "'.$main_index.'?pid="+t;
  }
}
else f.submit();
}
if(typeof ajaxO == "undefined"){
if (window.XMLHttpRequest) ajaxO = new XMLHttpRequest();
else ajaxO = new ActiveXObject("Microsoft.XMLHTTP");
}
var sDiv = null;
function searchStringChanged(el,ev){
ev.preventDefault();
if(!sDiv){
  sDiv = document.createElement("div");
  sDiv.setAttribute("id", "sResDiv")
  document.body.appendChild(sDiv);
  sDiv.addEventListener("click", sDivHide);
}
var v = el.value;
if(!v.length){ 
  sDiv.style.display = "none";
  return;
}
else sDivPosition();
var a = "'.current_pth(__FILE__).'ajax_search.php?a=" + Math.floor(Math.random() * 1000) +
        "&text=" + encodeURI(v);
ajaxO.onreadystatechange = onSearchAjaxResponse;
ajaxO.open("GET", a, true);
ajaxO.send();
}
function sDivPosition(){
  if(!sDiv) return "";
  sDiv.style.display = "block";
  var sd = document.getElementById("site_search");
  var r = document.getElementById("site_search").parentElement.getBoundingClientRect();
  var s = sDiv.style;
  s.position = "fixed";//alert(sd);
  s.top = r.bottom + "px";
  if(window.visualViewport.offsetLeft) s.left = window.visualViewport.offsetLeft + "px";
  else s.left = r.left + sd.offsetLeft + "px";
  s.zIndex = "1";
}
window.addEventListener("resize", sDivPosition);
function sDivHide(){
sDiv.style.display = "none";
}
function onSearchAjaxResponse(){
if (ajaxO.readyState == 4 && ajaxO.status == 200){
  sDiv.innerHTML = ajaxO.responseText;
}
}
function searchKeyDown(e){
if ( e.ctrlKey || e.metaKey ){ 
  var fl = document.querySelectorAll("#sResDiv a");
  if (e.key=="1") if(fl[0]){ e.preventDefault(); document.location = fl[0].href; }
  if (e.key=="2") if(fl[1]){ e.preventDefault(); document.location = fl[1].href; }
  if (e.key=="3") if(fl[2]){ e.preventDefault(); document.location = fl[2].href; }
  if (e.key=="4") if(fl[3]){ e.preventDefault(); document.location = fl[3].href; }
  if (e.key=="5") if(fl[4]){ e.preventDefault(); document.location = fl[4].href; }
  if (e.key=="6") if(fl[5]){ e.preventDefault(); document.location = fl[5].href; }
  if (e.key=="7") if(fl[6]){ e.preventDefault(); document.location = fl[6].href; }
  if (e.key=="8") if(fl[7]){ e.preventDefault(); document.location = fl[7].href; }
  if (e.key=="9") if(fl[8]){ e.preventDefault(); document.location = fl[8].href; }
  if (e.key=="0") if(fl[9]){ e.preventDefault(); document.location = fl[9].href; }
}
}
</script>
<style>
#sResDiv span { color:red; }
</style>
';

// 
// Основна функция на модула, която връща html код на форма за търсене,
// но ако вече са изпратени данни за търсене, изпратеният за търсене стринг
// се записва в сесия и се отива на страницата за показване на резултат.
// Ако на функцията е изпратен параметър $r='result' тя връща резултата от търсенето.

function sitesearch2($r=''){

// Връщане на резутата от търсенето
if ($r=='result') return site_search_result();

// Обработване на изпратен стринг за търсене
if (isset($_POST['searchtext'])) do_site_search($_POST['searchtext']);
if (isset($_GET['ssr'])) do_site_search($_GET['ssr']);

//     
$f = new HTMLForm('site_search_form',false);
if (!session_id() && isset($_COOKIE['PHPSESSID'])) session_start();
if (isset($_SESSION['text_to_search'])) $tx = $_SESSION['text_to_search'];
else $tx = '';
$tx = str_replace('"','&quot;',$tx);
$ti = new FormInput(translate('sitesearch_label'),'searchtext','text', $tx);
$ti->js = ' onkeydown="searchKeyDown(event);" onkeyup="searchStringChanged(this,event)"'.
          ' autocomplete="off"';
$ti->id = 'searchtextfield';
$f->add_input($ti);
$b = new FormInput('','','button', translate('sitesearch_submit'));
$p = current_pth(__FILE__);
$b->js = 'onclick="doSiteSearch();"'.
         ' title="'.translate('sitesearch_submit').'"'; 
$f->add_input($b);
if (isset($_SESSION['text_to_search'])){
  $b = new FormInput('','','button',translate('sitesearch_clear'));
  $b->js = 'onclick="document.location=\''.$p.'clear.php\';" '.
           ' id="search_clear"'.
           ' title="'.translate('sitesearch_clear').'"';
  $f->add_input( $b );
}
add_style('site_serarch2');
return '<div id="site_search">'.translate('sitesearch_start')."\n".$f->html()."</div>\n";
}


// Запомняне на търсения стринг $_SESSION
// и пренасочване кам страницата за показване на резултат от търсене

function do_site_search($txs){
  $trt = trim($txs);
  if (!$trt) return;
  if (!session_id()) session_start();
  $_SESSION['text_to_search']=$trt;
  $_SESSION['sitesearch_saved']=0;
  // Страница за показване на резултат
  $l = stored_value('sitesearch_resultpage');
  // Ако не е зададена се пренасочва към result.php
  if (!$l) $l = current_pth(__FILE__).'result.php';
  header("Location: $l");
}

//
// Показване на резултата от търсенето
//
function site_search_result(){
global $language, $pth;
  if (!session_id() && isset($_COOKIE['PHPSESSID'])) session_start();
  // Ако няма текст за търсене, връщане на съобщение за това
  if (!isset($_SESSION['text_to_search'])) return translate('sitesearch_notext');
  $ts = $_SESSION['text_to_search'];
  // Премахват се символите за нов ред
  $ts = str_replace("\n",'',$ts);
  $ts = str_replace("\r",'',$ts);
  // Ако текста за търсене е по-дълъг от 255 - съобщение
  if (strlen($ts)>255){
    unset($_SESSION['text_to_search']);
    unset($_SESSION['sitesearch_saved']);
    return translate('sitesearch_verylong');
  }
  // Сйставяне на масив от неповтарящи се думи
  $wa = array_unique(explode(' ',$ts));
  // Отчитане на статистика за думите в таблица sitesearch_words
  site_search_stat($wa);
  // Добавяне на думите $wa в WHARE частта на SQL зявката за търсене
  $q = where_part($wa,'AND');
  //  
  $msg = '';
  if (count($wa)>1) $msg = translate('sitesearch_allwords');
  $r = db_select_m('name','content',"($q) AND `language`='$language' ORDER BY `date_time_2` DESC", false);
  //         ,      
  if (!count($r)){
    $q = where_part($wa,'OR');
    $r = db_select_m('name','content',"($q) AND `language`='$language' ORDER BY `date_time_2` DESC");
    if (count($wa)>1) $msg = translate('sitesearch_anyword');
  }
  $nf = '<p>'.translate('sitesearch_notfound').'"'.$_SESSION['text_to_search'].'"'.'</p>';
  if (!count($r)) return $nf;
  // Масив, съдържащ номерата на страниците, чието съдържание са записите от масива $r
  $pa = siteserch_pgids($r);
  if (!count($pa)) return $nf;
  $rz  = '<p>'.translate('sitesearch_searchfor').": \"$ts\"</p>\n";
  $rz .= '<p>'.translate('sitesearch_count').': '.count($pa)."</p>\n";
  $rz .= "<p>$msg</p>\n";
  foreach($pa as $p){
    $t = db_table_field('text','content',"`name`='".$p['title']."' AND `language`='$language'");
    if (!$t) $t = "No title";
    $mi = stored_value('sitesearch_indexfile', $pth."index.php");
    $rz .= "<a href=\"$mi"."?pid=".$p['ID']."\">$t</a><br>\n";
  }
  return $rz;
}

//
// Връща масив, съдържащ номерата на страниците, чието съдържание са записите от масива $r

function siteserch_pgids($r){
  $q = '';
  foreach($r as $i)
    if ($q) $q .= " OR `content`='".$i['name']."'"; 
    else $q .= "`content`='".$i['name']."'";
  // Настройката sitesearch_restr съдържа част от SQL заявка, която изключва някои страници от показване в резултата от търсене
  $w = stored_value('sitesearch_restr');
  if ($w && !in_edit_mode() && !show_adm_links()) $q = "( $q )$w";
  $pa = db_select_m('`ID`,`title`','pages',"$q" /* GROUP BY `content`"*/ );
  return $pa;
}

//
//  WHERE   SQL     
// $wa -  масив думи 
// $o  - ,   'AND'  'OR'
//
function where_part($wa,$o){
  $q = '';
  $like = stored_value('sitesearch2_like_not_match') == 'true';
  foreach($wa as $w){
    $w1 = addslashes(trim($w));
    if ($w){
       if ($like) {
          if ($q) $q .= " $o `text` LIKE '%$w1%'";
          else $q .= "`text` LIKE '%$w1%'";
      } else if (strlen($w)>3){
       if ($q) $q .= " $o MATCH (`text`) AGAINST ('$w1'";
       else $q .= "MATCH (`text`) AGAINST ('$w1')";
      }
      else {
       if ($q) $q .= " $o `text` REGEXP '".$w1."'";
       else $q .= "`text` REGEXP '".'[[:<:]]'.$w1.'[[:>:]]'."'";
      }
    }
  }
  return $q;
}

//
// Водене на статистика за търсените думи в таблица sitesearch_words

function site_search_stat($wa){
// За да се води статистика за потърсените думи, трябва да има настройта sitesearch_stat равносилна на логическа стойност true
if (!stored_value('sitesearch_stat')) return;
// В режим на редактиране или администриране, не се отчита статистика
global $can_edit;
if ( show_adm_links() || $can_edit ) return;
// Ако статистиката веднъж е отчетене не се повтаря
if ($_SESSION['sitesearch_saved']) return;
global $db_link,$tn_prefix;
foreach($wa as $w){
 $w1 = addslashes(trim($w));
 //       
 if (strlen($w1)){
   // Номер на дума $w1
   $id = db_table_field('ID', 'sitesearch_words', "`word`='$w1'");
   if ($id){ $q1 = "UPDATE `$tn_prefix"."sitesearch_words` SET "; $q2 = " WHERE `ID`=$id;"; }
   else { $q1 = "INSERT INTO `$tn_prefix"."sitesearch_words` SET `date_time_1`=NOW(), "; $q2 = ';'; }
   $q = $q1."`date_time_2`=NOW(), `word`='$w1', `count`=`count`+1, `IP`='".$_SERVER['REMOTE_ADDR']."'".$q2;
   mysqli_query($db_link,$q);
 }
}
$_SESSION['sitesearch_saved']=1;
}

?>

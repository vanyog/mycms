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

// Модул за търсене в сайта

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_db_select_m.php');

// 
// Основна функция на модула, която връща html код на форма за търсене,
// но ако вече са изпратени данни за търсене, изпратеният за търсене стринг
// се записва в сесия и се отива на страницата за показване на резултат.
// Ако на функцията е изпратен параметър $r='result' тя връща резултата от търсенето.
//

function sitesearch($r=''){

// Показване резултат от търсене
if ($r=='result') return site_search_result();

// Обработване на изпратен стринг за търсене
if (isset($_POST['text'])) do_site_search($_POST['text']);
if (isset($_GET['ssr'])) do_site_search($_GET['ssr']);

// Показване на форма за търсене
$f = new HTMLForm('site_search_form',false);
if (!session_id()) session_start();
if (isset($_SESSION['text_to_search'])) $tx = $_SESSION['text_to_search'];
else $tx = '';
$tx = str_replace('"','&quot;',$tx);
$f->add_input(new FormInput('','text','text',$tx));
$f->add_input(new FormInput('','','submit',translate('sitesearch_submit')));
if (isset($_SESSION['text_to_search'])){
  // Бутон "Последен резултат"
  $p = stored_value('sitesearch_resultpage');
//  $b = new FormInput('','','button',translate('sitesearch_last'));
//  $b->js = 'onclick="document.location=\''.$p.'\';"';
//  $f->add_input( $b );
  // Бетон "Почистване"
  $p = current_pth(__FILE__).'clear.php';
  $b = new FormInput('','','button',translate('sitesearch_clear'));
  $b->js = 'onclick="document.location=\''.$p.'\';"';
  $f->add_input( $b );
}
return $f->html();
}

//
// Тези функция записва изпратеният за търсене стринг в $_SESSION
// и прави пренасочване към страницата за показване на резултат.
//
function do_site_search($txs){
  $trt = trim($txs);
  // Ако изпратения стринг е празен - не се прави нищо
  if (!$trt) return;
  // Запазване стринка за търсене в $_SESSION
  if (!session_id()) session_start();
  $_SESSION['text_to_search']=$trt;
  $_SESSION['sitesearch_saved']=0;
  // Пренасочване към страницата за показване на резултата
  $l = stored_value('sitesearch_resultpage'); // Четене на адреса от настройките
  // Ако няма такава, ризултатът се показва от скрипта result.php на модула
  if (!$l) $l = current_pth(__FILE__).'result.php';
  header("Location: $l");
}

//
// Функция, която връща резултата от търсенето
//
function site_search_result(){
global $language, $pth;
  if (!session_id()) session_start();
  // Съобщение, че няма текст за търсене
  if (!isset($_SESSION['text_to_search'])) return translate('sitesearch_notext');
  $ts = $_SESSION['text_to_search'];
  // Премахване знаци за нов ред
  $ts = str_replace("\n",'',$ts);
  $ts = str_replace("\r",'',$ts);
  // Съобщение, че текстът е прекалено дълъг
  if (strlen($ts)>255){
    unset($_SESSION['text_to_search']);
    return translate('sitesearch_verylong');
  }
  // Разпадане на текста за търсене на думи
  $wa = array_unique(explode(' ',$ts));
  // Запазване на статистика за думите, по които се търси
  site_search_stat($wa);
  // Търсене имената на стрингове, в които се срещат всички думи
  $q = where_part($wa,'AND');
  // Пояснителен надпис
  $msg = '';
  if (count($wa)>1) $msg = translate('sitesearch_allwords');
  $r = db_select_m('name','content',"($q) AND `language`='$language'");
  // Ако не бъдат открити се търсят имената на стрингове, в които се срещат думите поотделно
  if (!count($r)){
    $q = where_part($wa,'OR');
    $r = db_select_m('name','content',"($q) AND `language`='$language'");
    if (count($wa)>1) $msg = translate('sitesearch_anyword');
  }
  $nf = '<p>'.translate('sitesearch_notfound').'"'.$_SESSION['text_to_search'].'"'.'</p>';
  if (!count($r)) return $nf;
  // Четене номерата на страниците, които имат за съдържание, намерените стрингове
  $pa = siteserch_pgids($r);
/*
  $q = '';
  foreach($r as $i)
    if ($q) $q .= " OR `content`='".$i['name']."'"; 
    else $q .= "`content`='".$i['name']."'";
  // Допълнително условие, което ограничава страниците да не се показват в резултата
  $w = stored_value('sitesearch_restr');
  if ($w && !in_edit_mode() && !show_adm_links()) $q = "( $q )$w";
  $pa = db_select_m('`ID`,`title`','pages',"$q GROUP BY `content`");
*/
  if (!count($pa)) return $nf;
  $rz  = '<p>'.translate('sitesearch_searchfor').": \"$ts\"<br>\n";
  $rz .= translate('sitesearch_count').': '.count($pa)."</p>\n";
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
// Четене номерата на страниците, които имат за съдържание стринговете с имена от масива $r

function siteserch_pgids($r){
  $q = '';
  foreach($r as $i)
    if ($q) $q .= " OR `content`='".$i['name']."'"; 
    else $q .= "`content`='".$i['name']."'";
  // Допълнително условие, което ограничава страниците да не се показват в резултата
  $w = stored_value('sitesearch_restr');
  if ($w && !in_edit_mode() && !show_adm_links()) $q = "( $q )$w";
  $pa = db_select_m('`ID`,`title`','pages',"$q GROUP BY `content`");
  return $pa;
}

//
// Съставя WHERE частта на SQL заявката за търсене по думи
// $wa - масив от думи
// $o  - параметър, който е 'AND' или 'OR'
//
function where_part($wa,$o){
  $q = '';
  foreach($wa as $w){
    $w1 = addslashes(trim($w));
    if ($w){
//       if ($q) $q .= " $o `text` LIKE '%$w1%'";
//       else $q .= "`text` LIKE '%$w1%'";
      if (strlen($w)>3){
       if ($q) $q .= " $o MATCH (`text`) AGAINST ('$w1')";
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

// Запазване на статистика за думите, по които се търси
// Осъществява се ако има опция sitesearch_stat със стойност 1

function site_search_stat($wa){
// Ако няма опция sitesearch_stat - край
if (!stored_value('sitesearch_stat')) return;
// В режим на администриране или редактиране - край
global $can_edit;
if ( show_adm_links() || $can_edit ) return;
// Ако думите вече са запазени - край
if ($_SESSION['sitesearch_saved']) return;
global $db_link,$tn_prefix;
foreach($wa as $w){
 $w1 = addslashes(trim($w));
 // За да не се запазва презен стринг
 if (strlen($w1)){
   // Номер на думата, ако вече е запазена
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
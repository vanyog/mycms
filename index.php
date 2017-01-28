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

// Основен скрипт за генериране на страници
 
// Страница се идентифицира с номер, който се предава с $_GET['pid']
// Всяка страница се описва с шаблон, заглавие, съдържание и др., които са зададени
// в запис от таблица $tn_prefix.`pages`.


if(!ob_start("ob_gzhandler")) ob_start();

$exe_time = microtime(true);

error_reporting(E_ALL); ini_set('display_errors',1);

if (phpversion()>'5.0') date_default_timezone_set("Europe/Sofia");

// Път до директорията на системата
$idir = dirname(__FILE__).'/';

// Път до файл conf_database.php с данни за достъп до базата данни. 
// Може да е различен от този в $idir, ако е необходимо.
$ddir = $idir;

if (
  !file_exists($idir.'conf_database.php')
  || !file_exists($idir.'conf_paths.php')
) 
die('The system is not propperly installed. See <a href="http://vanyog.com/_new/index.php?pid=91" target="_blank">USAGE.txt</a> file.');

$page_header = ''; // Добавки към хедъра на страницата

include_once($idir.'lib/f_stored_value.php');
load_options(array(
  'main_index_pageid',
  'cache_time',
  'error_404_template',
  'today',
  'sitesearch_nocoleron',
  'acceptable_params'
));
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_parse_template.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_page_cache.php');
include_once($idir.'lib/f_db_table_status.php');

header("Content-Type: text/html; charset=$site_encoding");

// Адрес на индексния файл
$ind_fl = $_SERVER['PHP_SELF'];

$body_adds   = ''; // Добавки към body тага

$can_edit = false;     // Право на потребителя да редактира надписите по страницата
$can_create = false;   // Право на потребителя да съдава/изтрива страници в дадения раздел(подменю) на сайта
$can_manage = array(); // Права за администриране на модули
$can_visit = true;     // Право на влязъл потребител да вижда съдържанието на страницата.

// Номер на страницата
$page_id = stored_value('main_index_pageid',1);
if (isset($_GET['pid'])) $page_id = 1*$_GET['pid'];

// Заглавие на страницата
$page_title = '';

// Чете се описанието на страницата от таблица $tn_prefix.'pages'
$page_data = db_select_1('*','pages',"ID=$page_id");
if (!$page_data) $page_data = page404();

// Пренасочване към http, ако не е необходим https протокол
include_once($idir.'lib/f_stop_https.php');
stop_https($page_data['content']);

// Заглавие на страницата
$page_title = translate($page_data['title']);

// Масив с опции
$page_options = '';
if ($page_data['options']) { $page_options = explode(' ',$page_data['options']); }

// Четене на html кода на страницата от кеша
$cnt = page_cache();

// Ако страницата не е извлечена от кеша се генерира
if (!$cnt){

 // Попълване със съдържание на елементите в шаблона
 $cnt = parse_template($page_data);
 // Записване в кеша
 $t = stored_value('cache_time');
 if ($t) save_cache($cnt);
}

// Ако страницата не е достъпна за потребителя се генерира Access denied
if (!$can_visit) {
  if (session_id()) session_destroy();
  header("Status: 403");
  die("Access denied by index.php.");
}

// Броят се показванията на страницата
count_visits($page_data);

// Оцветяване на търсени думи
$cnt = colorize($cnt);

$exe_time = number_format(microtime(true) - $exe_time, 3);

// Показване броя на MYSQL заявките, ако е предвидено да се показват
$cnt = str_replace('<!--DB_REQ_COUNT-->',"$db_req_count $exe_time ", $cnt);

// Изпращане на страницата
echo $cnt;

// --------------------------------

// Връща страница, която показва надпис, че няма страница с такъв номер
function page404(){
$rz = Array (
'ID' => 0,
'menu_group' => 1,
'title' => 'error_404_title',
'content' => 'error_404_content',
'template_id' => stored_value('error_404_template',1),
'hidden' => '0',
'options' => '',
'tcount'=>0,
'dcount'=>0,
'donotcache'=>1
);
//print_r($rz); die;
return $rz;
}

// Брои посещенията
function count_visits($p){
global $tn_prefix, $db_link, $idir, $can_edit;
include_once($idir."lib/f_adm_links.php");
// Ако се показват линкове за администриране, или меню за редактиране на страниците, не се брои нищо
if ( ($p['ID']==0) || show_adm_links() || $can_edit ) return '';
new_day();
$q = "UPDATE `$tn_prefix"."pages` SET dcount = dcount+1 WHERE `ID`=".$p['ID'].";";
mysqli_query($db_link,$q);
}

// Ако започва нов ден се записват данните за изминалото денонощие в таблица $tn_prefix.'visit_history'
function new_day(){
global $apth, $tn_prefix, $db_link, $idir;
// чете се последната дата от таблица $tn_prefix.'options'
include_once($idir.'lib/f_stored_value.php');
$td = stored_value('today');
$d = getdate();
// ако не се е сменила датата не се прави нищо
if ($d['mday']==$td) return;
// записва се последната датата в таблица $tn_prefix.'options'
store_value('today',$d['mday']);
$dd = $d['year'].'-'.$d['mon'].'-'.$d['mday'];
// четат се записите на посетените през деня страници от таблица $tn_prefix.'pages'
$dt = db_select_m('ID,dcount','pages','`dcount`>0');
// записва се броя посещения на всяка страница в таблица $tn_prefix.'visit_history'
$q = "INSERT INTO `$tn_prefix"."visit_history` (`page_id`, `date`, `count`) VALUES\n";
foreach($dt as $r){
  $q .= "(".$r['ID'].", '$dd', ".$r['dcount']."),\n";
}
$q = substr($q, 0, strlen($q)-2).";";
mysqli_query($db_link,$q);
// нулира се броя на посещенията в таблица $tn_prefix.'pages'
$q = "UPDATE `$tn_prefix"."pages` SET tcount = tcount + dcount, dcount = 0;";
mysqli_query($db_link,$q);
// Записване обема на данните от таблица content към днешната дата
$q = "INSERT INTO `$tn_prefix"."content_history` (`date`, `size`) VALUES ".
     "('$dd', ".db_table_status('content', 'Data_length').");";
mysqli_query($db_link,$q);
}

// Оцветяване на търсени думи

// Част от регулярния израз за намиране на търсените думи
$word_pattern = '';

function colorize($cnt){
if (isset($_SESSION['text_to_search'])){
  // Страници, на които не се прави оцветяване
  $a = stored_value('sitesearch_nocoleron', '$nocolor = array();');
  if ($a) eval($a);
  global $page_id, $word_pattern;
  // Дали е възникнала грешка в preg_replace
  $GLOBALS['preg_error']=false;
  if (!in_array($page_id, $nocolor)){
    $ca = explode('<body',$cnt);
    $wa = array_unique(explode(' ',$_SESSION['text_to_search']));
    foreach($wa as $w){
      $word_pattern = to_regex($w);
      $ca[1] = preg_replace_callback('/>([^<]*?)</is', 'colorize1', $ca[1]);
    }
    $cnt = implode('<body',$ca);
  }
}
return $cnt;
}

function to_regex($w){
$w1 = mb_strtoupper($w);
$w2 = mb_strtolower($w);
$rz = '';
for($i=0;$i<strlen($w1);$i++){ 
  if (in_array($w[$i],array('/','.','^')))
    $rz .= '\\'.$w[$i];
  else
    $rz .= '['.$w1[$i].$w2[$i].']';
}
return $rz;
}

function colorize1($a){
$a1 = trim(str_replace('&nbsp;', ' ', $a[1]));
if (!$a1) return $a[0];
global $word_pattern;
$pt = '/([^a-zA-Zа-яА-Я])('.$word_pattern.')([^a-zA-Zа-яА-Я])/is';
$rp = '\1<span class="searched">\2</span>\3';
// Ако вече е възникнала грешка с preg_replace - не се вика.
// Целта е да се избегне многократното показване на съобщение за грешка.
if ($GLOBALS['preg_error']) return $a[0];
$rz = preg_replace($pt, $rp, $a[0]);
if (!$rz){
  $GLOBALS['preg_error'] = true;
  return $a[0];
}
else return $rz;
}

?>


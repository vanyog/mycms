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

if(!isset($idir)) $idir = dirname(dirname(__FILE__)).'/';
if(!isset($ddir)) $ddir = $idir;

include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_db_delete_where.php');

// В този файл се дефинират две функции, свързани с кеширането на страници

// page_cache() чете html кода на страницата от таблица $tn_prefix.'page_cache'
// вместо страницата да се генерира наново.
// Връща празен стринг, ако: страницата не подлежи на кеширане; времето за опресняване на кеша в минути, зададено с настройката
// cache_time е изтекло; или не е зададено.
// Стойност на cache_time -1 означава записът в кеша да се опреснява, само след променяне на страницата.

// save_cache($cnt) записва html кода на страницата в таблица $tn_prefix.'page_cache'
// Настройката от таблица 'options' с име 'acceptable_params' съдържа имената на допустимите за сайта
// $_GET параметри. Стрингът с параметрите започва и завършва със знак =, а имената се отделят също с =.

// При самостоятелно извикване, този файл предизвиква почистване на кеша на страница $_GET['purge']
// При $_GET['purge'] == 0     се почиства кеша на главната страница.
// При $_GET['purge'] == 'all' се изтриват кешовете на всички страници.

if(isset($_GET['purge'])){
  global $db_link;
  if(!$_GET['purge']) $_GET['purge'] = stored_value('main_index_pageid',1);
  if($_GET['purge']=='all') $q = '1';
  else $q = "`page_ID`=".(1*$_GET['purge']);
  db_delete_where('page_cache',$q);
  $i = mysqli_affected_rows($db_link);
  die('Page cache '.$_GET['purge']." purged. $i - records deleted.".
      '<p><a href="/?pid='.$_GET['purge'].'">Go to page</a>');
}

function page_cache(){
// При случаи, в които не се използва кеш - празен стринг
if (do_not_cache()) return '';
global $language, $page_data;
$t = stored_value('cache_time');
// Не е зададено време за кеширане, или то е 0
if (!$t && isset($page_data['donotcache']) && ($page_data['donotcache']!=-1)) return '';
// Ако е зададено време за кеширане на отделната страница в поле `donotcache` се вземе предвид това време
if (isset($page_data['donotcache']) && ($page_data['donotcache']>1) ) $t = $page_data['donotcache'];
// Приемлив заявен адрес
$htp = acceptable($_SERVER['REQUEST_URI'],false);
// Четене на данните от кеш таблицата
$d = db_select_1('*', 'page_cache', 
     '`page_ID`='.$page_data['ID']." AND `name`='".addslashes($htp)."' AND `language`='$language'");
if (!$d) return '';
else{
  if(isset($page_data['donotcache']) && ($page_data['donotcache']==-1) ) return $d['text'];
  $td = time() - strtotime($d['date_time_1']);
//  die("$td ".($t*60));
  if ( !($t<0) && ($td > ($t*60)) ) return '';
  return $d['text'];
}
}

//
// Записване html кода на страницата в таблица $tn_prefix.'page_cache'

function save_cache($cnt){
// Случаи, в които не се запазва кеш
if (do_not_cache()) return;
global $language, $page_data, $tn_prefix, $db_link;
// Уеднаквена форма на адреса
$htp = acceptable($_SERVER['REQUEST_URI'],true);
// Ако адресът не е приемлив, не се запазва кеш
if (!$htp) return;
$id = db_table_field('ID','page_cache',
      "`page_ID`=".$page_data['ID'].
      " AND `name`='".addslashes($htp).
      "' AND `language`='$language'");
if (!$id) $q = "INSERT INTO `$tn_prefix"."page_cache` SET ";
else      $q = "UPDATE `$tn_prefix"."page_cache` SET ";
if (isset($_SERVER['HTTP_REFERER'])) $r = ", `referer`='".addslashes($_SERVER['HTTP_REFERER'])."'";
else $r = '';
$q .= "`page_ID`=".$page_data['ID'].
      ", `name`='".addslashes($htp).
      "', `language`='$language', `date_time_1`=NOW(), `text`='".addslashes($cnt)."'".
      $r;
if ($id) $q .= " WHERE `ID`=$id;";
else $q .';';
mysqli_query($db_link,$q);
}

//
// Връща истина във всички случаи, в който не следва да се прави кеширане

function do_not_cache(){
global $page_data, $debug_mode;
$other = false;
$code = stored_value("page_cache_IPs");
if($code) eval($code);
$rz = $other
  || ($page_data['ID']==0)
  || (isset($page_data['donotcache']) && ($page_data['donotcache']==1))
  || in_edit_mode()
  || count($_POST)
  || isset($_COOKIE['PHPSESSID'])
  || (!is_local() && show_adm_links())
  || !empty($debug_mode)
  ;
return $rz;
}

//
// Изчиства кеша за адрес $a

function purge_page_cache($a){
$b = parse_url($a);
$c = array(); 
if (isset($b['query'])) parse_str($b['query'],$c);
if (is_numeric($c['pid'])) db_delete_where('page_cache',"`page_ID`=".(1*$c['pid']));
}

//
// Връща уеднаквена форма на заявения адрес
// При $y=true - връща празен стринг за недопустим параметър
// При $y=false - само премахва недопустимите параметри

function acceptable($u,$y){
global $edit_name;
$a = parse_url($u);
$b = array();
if (isset($a['query'])) parse_str($a['query'],$b);
$ka = array_keys($b);
$o = stored_value('acceptable_params');
foreach($ka as $k){
  if ( ($k=='lang') || ($k==$edit_name) ) {
     unset($b[$k]);
     continue;
  }
  if (strpos($o,"=$k=")===false)
     if ($y) return '';
     else unset($b[$k]);
}
ksort($b);
$a['query'] = http_build_query($b);
$rz = isset($a['path']) ? $a['path'] : '';
if ($a['query']) $rz .= '?'.$a['query'];
return $rz;
}

?>
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

include_once($idir.'lib/f_db_delete_where.php');

// В този файл се дефинират две функции, свързани с кеширането на страници

// page_cache() чете html кода на страницата от таблица $tn_prefix.'page_cache'
// вместо страницата да се генерира наново.
// Ако страницата не подлежи на кеширане, времето за опресняване на кеша в минути, зададено с настройката
// cache_time е изтекло, или не е зададено, връща празен стринг.
// Стойност cache_time=-1 означава записът в кеша да се опреснява, само при променяне на страницата.

// save_cache($cnt) записва html кова на страницата в таблица $tn_prefix.'page_cache'

function page_cache(){
// Случаи, в които не се използва кеш:
if (do_not_cache()) return '';
global $language, $page_data;
$t = stored_value('cache_time');
// Не е зададено време за кеширане, или то е 0
if (!$t) return '';
// Четене на данните от кеш таблицата
$d = db_select_1('*', 'page_cache', 
     '`page_ID`='.$page_data['ID']." AND `name`='".addslashes($_SERVER['REQUEST_URI'])."' AND `language`='$language'");
if (!$d) return '';
else{
  $td = time() - strtotime($d['date_time_1']);
  if ( !($t<0) && ($td > ($t*60)) ) return '';
  else return $d['text'];
}
}

//
// Записване html кода на страницата в таблица $tn_prefix.'page_cache'

function save_cache($cnt){
// Случаи, в които не се запазва кеш
if (do_not_cache()) return;
global $language, $page_data, $tn_prefix, $db_link;
$id = db_table_field('ID','page_cache',
      "`page_ID`=".$page_data['ID'].
      " AND `name`='".addslashes($_SERVER['REQUEST_URI']).
      "' AND `language`='$language'");
if (!$id) $q = "INSERT INTO `$tn_prefix"."page_cache` SET ";
else      $q = "UPDATE `$tn_prefix"."page_cache` SET ";
$q .= "`page_ID`=".$page_data['ID'].
      ", `name`='".addslashes($_SERVER['REQUEST_URI']).
      "', `language`='$language', `date_time_1`=NOW(), `text`='".addslashes($cnt)."'";
if ($id) $q .= " WHERE `ID`=$id;";
else $q .';';
mysqli_query($db_link,$q);
}

//
// Връща истина във всички случаи, в който не следва да се прави кеширане

function do_not_cache(){
global $page_data;
if (!session_id()) session_start();
return
  (isset($page_data['donotcache']) && ($page_data['donotcache']==1)) ||
  in_edit_mode() || 
  count($_POST) || 
  (isset($_SESSION) && count($_SESSION)) || 
  (!is_local() && show_adm_links());
}

//
// Изчиства кеша за адрес $a

function purge_page_cache($a){
$b = parse_url($a);
$c = array(); 
$d = $b['path'];
if (isset($b['query'])) parse_str($b['query'],$c);
if (isset($c['pid'])) $d .= '?pid='.$c['pid'];
if ($d>'/') db_delete_where('page_cache',"`name` LIKE '$d%'");
}


?>

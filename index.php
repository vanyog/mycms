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

error_reporting(E_ALL); ini_set('display_errors',1);

header("Content-Type: text/html; charset=windows-1251");

if (phpversion()>'5.0') date_default_timezone_set("Europe/Sofia");

// Номер на страницата
$page_id = 1;
if (isset($_GET['pid'])) $page_id = 1*$_GET['pid'];

$idir = dirname(__FILE__).'/';

if (!file_exists($idir.'conf_database.php')
  ||!file_exists($idir.'conf_languages.php')
  ||!file_exists($idir.'conf_host.php')
  ||!file_exists($idir.'conf_paths.php')
) 
   die('Системата все още не е правилно инсталирана и конфигурирана. Вижте файл USAGE.txt.');
   
include('lib/f_db_select_1.php');
include('lib/f_db_select_m.php');
include('lib/f_parse_template.php');
include_once('lib/translation.php');

$page_header = ''; // Добавки към хедъра на страницата
$body_adds   = ''; // Добавки към body тага

// Чете се описанието на страницата от таблица $tn_prefix.'pages'
$page_data = db_select_1('*','pages',"ID=$page_id");
if (!$page_data) 
   if (is_local()) die('<a href="'.$adm_pth.'new_record.php?t=pages&ID='.$page_id.'">Click here</a> to create a page.');
   else $page_data = page404();

// Броят се показванията на страницата
count_visits($page_data);

// Масив с опции
$page_options = '';
if ($page_data['options']) { $page_options = explode(' ',$page_data['options']); }

// Попълване със съдържание на елементите в шаблона
$cnt = parse_template($page_data);

// Изпращане на страницата
echo $cnt;

// --------------------------------

// Връща страница, която показва надпис, че няма страница с такъв номер
function page404(){
return Array (
'ID' => 0,
'menu_group' => 1,
'title' => 'error_404_title',
'content' => 'error_404_content',
'template_id' => 2,
'options' => ''
);
}

// Брои посещенията
function count_visits($p){
global $tn_prefix, $db_link, $idir;
include_once($idir."lib/f_adm_links.php");
if (!$p['ID'] || show_adm_links()) return '';
new_day();
$q = "UPDATE `$tn_prefix"."pages` SET dcount = dcount+1 WHERE `ID`=".$p['ID'].";";
mysql_query($q,$db_link);
}

// Ако започва нов ден се записват данните за изминалото денонощие в таблица $tn_prefix.'visit_history'
function new_day(){
global $apth, $tn_prefix, $db_link;
$tdf = $apth.'today.txt';
$td = file($tdf);
$d=getdate();
if ($d['mday']==trim($td[0])) return;
$dd = $d['year'].'-'.$d['mon'].'-'.$d['mday'];
$dt = db_select_m('ID,dcount','pages','`dcount`>0');
foreach($dt as $r){
  $q = "INSERT INTO `$tn_prefix"."visit_history` SET `page_id`=".$r['ID'].", `date`='$dd', `count`=".$r['dcount'].";";
//  echo "$q<br>";
  mysql_query($q,$db_link);
}
$f = fopen($tdf,'w+');
fwrite($f,$d['mday']);
fclose($f);
$q = "UPDATE `$tn_prefix"."pages` SET tcount = tcount + dcount, dcount = 0;";
//echo "$q<br>";
mysql_query($q,$db_link);
}

?>


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

// Път до директорията на системата
$idir = dirname(__FILE__).'/';

// Път до файл conf_database.php с данни за достъп до базата данни. 
// Може да е различен от този в $idir, ако е необходимо.
$ddir = $idir;

if (
  !file_exists($idir.'conf_database.php')
  || !file_exists($idir.'conf_paths.php')
) 
die('Системата все още не е правилно инсталирана и конфигурирана. Вижте файл <a href="http://vanyog.com/_new/index.php?pid=91">USAGE.txt</a>.');

include($idir.'lib/f_db_select_1.php');
include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_parse_template.php');
include_once($idir.'lib/translation.php');

// Адрес на индексния файл
$ind_fl = $_SERVER['PHP_SELF'];

$page_header = ''; // Добавки към хедъра на страницата
$body_adds   = ''; // Добавки към body тага

$can_edit = false;     // Право на потребителя да редактира надписите по страницата
$can_create = false;   // Право на потребителя да съдава/изтрива страници в дадения раздел(подменю) на сайта
$can_manage = array(); // Права за администриране на модули

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
'template_id' => 1,
'options' => '',
'tcount'=>0,
'dcount'=>0
);
}

// Брои посещенията
function count_visits($p){
global $tn_prefix, $db_link, $idir;
include_once($idir."lib/f_adm_links.php");
// Ако се показват линкове за администриране не се брои нищо
if (!$p['ID'] || show_adm_links()) return '';
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
$dd = $d['year'].'-'.$d['mon'].'-'.$d['mday'];
// четат се записите на посетените през деня страници от таблица $tn_prefix.'pages'
$dt = db_select_m('ID,dcount','pages','`dcount`>0');
// записват се броя посещения на всяка страница в таблица $tn_prefix.'visit_history'
foreach($dt as $r){
  $q = "INSERT INTO `$tn_prefix"."visit_history` SET `page_id`=".$r['ID'].", `date`='$dd', `count`=".$r['dcount'].";";
  mysqli_query($db_link,$q);
}
// записва се последната датата в таблица $tn_prefix.'options'
store_value('today',$d['mday']);
// нулира се броя на посещенията в таблица $tn_prefix.'pages'
$q = "UPDATE `$tn_prefix"."pages` SET tcount = tcount + dcount, dcount = 0;";
mysqli_query($db_link,$q);
}

?>


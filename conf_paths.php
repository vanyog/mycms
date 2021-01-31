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

// Скрипт за указване на пътя до някои директории
// и четене на настройки от таблица $tn_prefix.'options'

if(!isset($idir)) $idir = dirname(__FILE__).'/';
if(!isset($ddir)) $ddir = $idir;

if( isset($_GET['debug']) && $_GET['debug'] ) $GLOBALS['debug_mode'] = true;

include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_is_local.php');

load_options(array(
  'acceptable_params',
  'admin_path',
  'adm_name',
  'adm_value',
  'cache_time',
  'ckeditor_file',
  'default_language',
  'document_root',
  'edit_name',
  'edit_value',
  'host_local',
  'host_web',
  'languages',
  'main_index_file',
  'main_index_pageid',
  'mod_path',
  'phpmyadmin_local',
  'phpmyadmin_web',
  'prefere_www',
  'RewriteEngine',
  'stop_https',
  'SEO_names'
));

// Задаване друга стойност на $_SERVER['DOCUMENT_ROOT'], ако връщаната от сървъра не е правилна
$document_root = stored_value('document_root');
if (file_exists($document_root)) $_SERVER['DOCUMENT_ROOT'] = $document_root;

// Директория на входната страница.
// Задава се ако сайтът се изгражда като раздел в друг сайт и се намира в директория на главния сайт.
// Трябва да завършва с /.
$pth = current_pth();

// Главен index.php файл на сайта
$main_index = stored_value('main_index_file', $pth.'index.php');

// Абсолютна директория на входната страница във файловата система на сървъра
$apth = $_SERVER['DOCUMENT_ROOT'].$pth;

// Директория за администриране
$adm_pth = stored_value('admin_path','manage').'/';
if(is_local()) $adm_pth = 'manage/';
if ($adm_pth[0]!='/') $adm_pth = $pth.$adm_pth;

// Абсолютна директория на директорията за администриране във файловата система на сървъра
$adm_apth = $_SERVER['DOCUMENT_ROOT'].$adm_pth;

// Адрес на phpMyAdmin за отдалечения сървър 
$phpmyadmin = $adm_pth.'db/index.php';

// Път до ckeditor
$ckpth = dirname(stored_value('ckeditor_file',$adm_pth.'ckeditor/ckeditor.js')).'/';

// Директория с модули
$mod_pth = stored_value('mod_path',$pth.'mod').'/';
if ($mod_pth[0]!='/') $mod_pth = $pth.$mod_pth;

// Абсолютна директория до директорията с модули
$mod_apth = $_SERVER['DOCUMENT_ROOT'].$mod_pth;

// Кодировка на сайта
$site_encoding = 'windows-1251';
switch ($colation){
case 'cp1251': $site_encoding = 'windows-1251'; break;
case 'utf8': $site_encoding = 'UTF-8'; break;
default: die("Unknown colation $colation in conf_paths.php");
}

// Кодировка на php файловете на сървъра
$file_encoding = 'windows-1251';

// Дали да се използват SEO имена, вместо номера на страниците
$seo_names = stored_value('SEO_names') == 'on';

// Дали се използва RewriteEngine
$rewrite_on = stored_value('RewriteEngine') == 'on';

// Списък, разделени със запетая имане на модули, които не трябва да зареждат _style.css файла от директорията си
$no_style = ',';

// Тайни стойности, на които се базира сигурността на административния достъп до сайта:

// Име на променлива, която се изпраща с GET за да се покаже менюто за администриране 
$adm_name = stored_value('adm_name','admin');
// Стойност на променлива, която се изпраща с GET за да се покаже менюто за администриране 
$adm_value = stored_value('adm_value','on');

// Име на променлива, която се изпраща с GET за да се мине в режим на редактиране
$edit_name = stored_value('edit_name','edit');
// Стойност на променлива, която се изпраща с GET за да се мине в режим на редактиране
$edit_value = stored_value('edit_value','on');

// Връща истина, ако се изпълнява скрипт от директорията за администриране
function in_admin_path(){
global $adm_pth;
return ( substr($_SERVER['SCRIPT_NAME'],0,strlen($adm_pth))==$adm_pth );
}

// Връща текущата директория
function current_pth($f = __FILE__){
$p1 = $_SERVER['DOCUMENT_ROOT'];         $n1 = strlen($p1);
if ($p1[$n1-1]=='/') $n1--;
$p2 = str_replace('\\','/',dirname($f)); $n2 = strlen($p2);
if(substr($p2, 0, $n1)!=$p1){
  $or = stored_value('uploadfile_otherroot');
  if($or){
    $p1 = $or;
    $n1 = strlen($p1);
  }
}
$r = substr($p2,$n1,$n2-$n1).'/';
return $r;
}

// Проверява дали стринг $a започва със стринг $b
function starts_with($a, $b){
$l = strlen($b);
return substr($a, 0, $l) == $b;
}


?>
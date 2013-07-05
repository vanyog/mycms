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

// Функцията translate($n) връща текст с име $n на езика, определен с глобалната променлива $language.

// Ако в таблица `content` на базата данни няма такъв текст,
// на локален сървър или в режим на редактиране 
// на мястото на текста се показва хипервръзка към страница за вмъкване на текст,
// но на онлайн сървър в работен режим, функцията връща текст на подразбиращия се език.

include_once($idir."conf_paths.php");
include_once($idir."lib/f_is_local.php");
include_once($idir."lib/f_db_select_1.php");
include_once($idir."lib/f_parse_content.php");

$content_date_time    = '';// Променлива, която съдържа датата и часа на последната редакция на върнатия текст 
$content_create_time = ''; // Променлива, която съдържа датата и часа на първото въвеждане на върнатия текст 

function translate($n){
global $language, $adm_pth, $default_language, $content_date_time, $content_create_time;

$content_date_time = '';
$content_create_time = '';

$el = ''; // Линк за редактиране. Показва се ако сайтът е в режим на редактиране.
if (in_edit_mode()){
  $id = db_select_1('ID','content',"name='$n' AND language='$language'");
  $el = '<a href="'.$adm_pth.'edit_record.php?t=content&r='.$id['ID'].'">*</a>';
}

// Връщан резултат
$rz = ''; 

// Четене на записа за надпис с име $n на език $language
$r = db_select_1('*','content',"name='$n' AND language='$language'");
if ($r){ // Ако има такъв запис
  $content_create_time = $r['date_time_1']; 
  $content_date_time = $r['date_time_2'];
  $t = stripslashes($r['text']);
  $rz = parse_content(apply_filters($n,$t));
  if (!isset($r['nolink']) || !$r['nolink']) $rz .= $el;
}
else if (is_local() || in_edit_mode())
         // На локелен сървър или в режим на редактиране се показва името на стринга като линк,
         // който отваря форма за въвеждане на липсващия надпис
         return "<a href=\"$adm_pth"."new_content.php?n=$n&l=$language\">$n</a>";
       else { // На отдалечен сървър в работен режим
         // Четене на записа на езика по подразбиране
         $r = db_select_1('*','content',"`name`='$n' AND `language`='$default_language'");
         // Ако няма запис се показва името на текста
         if ( !$r ) $r['text'] = $n; 
         else {
           $content_create_time = $r['date_time_1'];
           $content_date_time = $r['date_time_2'];
         }
         $t = stripslashes($r['text']);
         // Заместват се със съдържание евентуални <!--$$_XXX_$$--> елементи
         $rz = parse_content(apply_filters($n,$t));
       }

// Връщане на резултата
return $rz;

} // Край на функцията translate($n)

// Функцията apply_filters($n, $t) прилага върху текста $t, определените за текста с име $n филтри

function apply_filters($n, $t){
global $idir, $adm_pth;
$rz = $t; // Връщан резултат
// Четене на списъка от имена на филтри, които се прилагат върху текста
$fl = db_select_1('filters', 'filters', "`name`='$n'");
// Масив от имена на филтри
$fla = array();
if ($fl) $fla = explode(',', $fl['filters']);
// Прилагане на списъка от филтри      
foreach($fla as $fln){
  $flp = "filter/$fln/$fln.php"; // Път до файла на филтъра от директорията на сайта
  $afp = "$idir$flp"; // Абсолютен път до файла на филтъра
//  print_r($afp); die;
  if (file_exists($afp)){ // Ако има такъв филтър
    include_once($afp);
	$rz = $fln($rz);
  }
  else if (show_adm_links()) $rz .= '<p><br>Unknown fliter <a href="'.$adm_pth.'new_filter.php?f='.$fln.'">'.$fln.'</a><p>';
}
return $rz;
} 

?>

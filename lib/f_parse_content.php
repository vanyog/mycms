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

// Функцията parse_content($cnt) замества елементите <!--$$_XXX_$$--> в стринга $cnt
// със съдържание, генерирано от php скриптове, които се съхраняват в таблица $tn_prefix.'scripts'
// или модули от директория mod.
// При грешка в изпълнението на функция eval(), адреса на страницата се запазва в таблица
// `options` под име 'eval_error_uri'.

include_once($idir.'lib/f_translate.php');
include_once($idir.'lib/f_adm_links.php');
include_once($idir.'lib/f_mod_path.php');
include_once($idir.'mod/rawfile/f_rawfile.php');

function parse_content($cnt){
global $page_options, $page_data, $page_title, $body_adds, $page_header, $content_date_time,
       $idir, $pth, $adm_pth, $apth, $mod_pth, $mod_apth,
       $can_visit, $can_manage, $site_encoding, $debug_mode;

$l = strlen($cnt);
$str1 = '<!--$$_'; // Означение за начало на замествания елемент
$str2 = '_$$-->';  // Означение за край на замествания елемент

// Цикъл за заместване на елементи
// $p0 - позиция на първата замествана буква
while ( !(($p0 = strrpos($cnt,$str1))===false) ){

$p1 = $p0 + strlen($str1); // Позиция на първия символ от името на елемента
$p2 = strrpos($cnt,$str2); // Позиция на първия символ от означението за край на заместван елемент
// echo "$l $p1 $p2 ".substr($cnt,$p1,$p2-$p1)."<br>";
// Ако не е намерено означение за край, означението за начало се променя да стане видимо,
// а след него се вмъква съобщение == Not closed ! ==
if ($p2<$p1){
  $cnt = substr_replace($cnt,'&lt;&nbsp;!--$$_== Not closed ! ==',$p0,strlen($str1));
  continue;
} 
$p3 = $p2 + strlen($str2); // Позиция на последния заместван символ

// Отделяне на името от параметъра
$tg = explode('_',substr($cnt,$p1,$p2-$p1),2);

$tx = ''; // Html код, който ще замести елемента

// Четене на скрипта с име $tg[0] от таблица $tn_prefix.'scripts'
//$sc = db_select_1('*','scripts',"`name`='".$tg[0]."'");
$sc = script($tg[0]);

if (!$sc){ // Ако няма такъв скрипт се търси модул с това име
  if( !empty($debug_mode) ) echo "Module ".$tg[0]."(".( isset($tg[1]) ? $tg[1] : '' ).") ";
  $f = strtolower($tg[0]);
  $fn = mod_path($f);
  if ($fn){
    // Зареждане на файл _style.css, ако в директорията на модула има такъв
    $sf = dirname($fn).'/_style.css';
    if(file_exists($sf) && !function_exists($f)){
      $sfn = substr($sf,strlen($apth));
      $page_header .= "<style>\n".rawfile($sfn)."</style>\n";
    }
    // Зареждане на модула и изпълняване на главната му функция
    $c = "include_once('$fn');\n";
    if (isset($tg[1])) $c .= '$tx = '."$f('".addslashes($tg[1])."');";
    else $c .= '$tx = '."$f();";
    if (eval($c)===false){
      // При грешка се записва адреса на страницата и програмния код, предизвикал грешката
      store_value("eval_error_uri", $_SERVER['REQUEST_URI']);
      store_value("eval_error_code", $c);
    }
  }
  else { // Ако няма модул се показва линк за автоматично създаване на модул
    if (show_adm_links()) $tx = ' (Can\'t parse content <a href="'.$adm_pth.'new_mod.php?n='.$tg[0].'">'.$tg[0].'</a>) ';
    else $tx = '<p>Can\'t parse content '.$tg[0].'</p>';
  }
}
else {
  if( !empty($debug_mode) ) echo "Script ".$tg[0]."(".( isset($tg[1]) ? $tg[1] : '' ).") ";
  if (eval(stripslashes($sc['script']))===false){ // Изпълнява се модула
     store_value("eval_error_uri", $_SERVER['REQUEST_URI']);
  }
}

// Заместване на елемента с генерирания html код, който е присвоен на $tx
$cnt = substr_replace($cnt,$tx,$p0,$p3-$p0);

if( !empty($debug_mode) ) echo db_req_count()."<br>\n";

} // Край на цикъла за попълване на елементите

return $cnt;

} // Край на функцията parce_content()

function script($nm){
static $script = array();
if(!count($script)) {
  $sn = db_select_m('name', 'scripts', 1);
  foreach($sn as $n) $script[$n['name']] = '';
}
if(!isset($script[$nm])) return false;
if(empty($script[$nm])) $script[$nm] = db_select_1('*','scripts',"`name`='$nm'");
return $script[$nm];
}

?>
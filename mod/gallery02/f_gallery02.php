<?php
/*
VanyoG CMS - a simple Content Management System
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

// Функцията gallery02($p) връща html код за показване на галерия от снимки.
// Файловете със снимки трябва да се намират в директория $p относно главната директория на сайта.
// Умаленият размер снимки трябва да се намират във файлове със същите имена, в поддиректория "$p/th".

// В директорията със снимките може да има файл titles.php, който дефинира асоциативен масив
// $img_title с ключове - имената на файловете, и стойности - заглавия, на снимките.

// Параметър $_GET['a']=='list' предизвиква показване кода за файл titles.php, който трябва да бъде поставен
// в директория th на галерията

// Параметър $_GET['o']=='fn' предизвиква подреждане по азбучен ред на файловете

if(!isset($idir)) $idir = dirname(dirname(dirname(__FILE__))).'/';

include_once($idir.'/lib/f_is_local.php');

function gallery02($p){

global $page_header;

// Индекс на активната снимка
$id = 0;

if (isset($_GET['imid'])) $id = 1*$_GET['imid'];

// Четене имената на файловете и заглавията от директория $p
$tl = $_SERVER['DOCUMENT_ROOT'].$p.'/th/titles.php';
if (file_exists($tl)){
 include($tl);
 $fl = array_keys($img_title);
}
else $fl = file_list02($_SERVER['DOCUMENT_ROOT'].$p.'/th','jpg');
if(isset($_GET['o']) && ($_GET['o']=='fn')){ sort($fl); }

if(isset($_GET['a']) && ($_GET['a']=='list')) die(gallery02_tf($fl));

if ($id>count($fl)-1) $id = count($fl)-1;

$rz = '';

// Добавяне на JavaScript
$jscr = '<script><
performance.mark("gallery02_Start");
var g02_images = [';
foreach($fl as $i => $f){
  $jscr .= "\"$f\"";
  if ($i<count($fl)-1) $jscr .= ",";
}
$jscr .= '];
var g02_current = 0;
function g02_next(){
if (g02_current >= g02_images.length-1) return;
var m = document.getElementById("main_image");
g02_current = g02_current+1;
m.src = "'.$p.'/"+g02_images[g02_current];
document.location = "#g02_top";
}
function g02_prev(){
if (g02_current <= 0) return;
var m = document.getElementById("main_image");
g02_current = g02_current-1;
m.src = "'.$p.'/"+g02_images[g02_current];
document.location = "#g02_top";
}
function g02_set_current(i){
var m = document.getElementById("main_image");
m.src = "'.$p.'/"+g02_images[i];
g02_current = i;
}
performance.mark("gallery02_End");
</script>';
if(!isset($page_header)) $rz .= $jscr;
else $page_header .= $jscr;

$p1 = current_pth(__FILE__);

// Връщан резултат - започва с първата от снимките
$rz .= '<a id="g02_top"></a>
<p style="text-align:center">
<img src="'.$p1.'arrow_prev.gif" alt="Previouse" style="cursor:pointer;" onclick="g02_prev();">
<img src="'.$p1.'arrow_next.gif" alt="Next" style="cursor:pointer;" onclick="g02_next();">
</p>
<p style="text-align:center">
<img id="main_image" src="'.$p.'/'.$fl[$id].'" alt="" width="650px">
<p>
<p style="text-align:center">'."\n";

// Показване на умалените снимки
$w = 130; // Ширина на умалените снимки
foreach($fl as $i => $f){
  $rz .= '<a href="#g02_top" onclick="g02_set_current('.$i.');"';
  if(in_edit_mode()) $rz.= " title=\"$f\"";
  $rz .= '>
<img id="'.$i.'" src="'."$p/th/$f".'" alt="">
</a>'."\n";
}

// Завършване и връщене на резултата
$rz .= '</p>';
return $rz;
}

// Функцията file_list($p,$e) връща масив с имената на файловете от директория $p
// които имат разширение $e

function file_list02($p,$e){ //die("$p<br>$e");
$r = array(); // Връщания резултат
if ($d = opendir($p)){
  while(($n = readdir($d))!==false){
    if (strtolower(pathinfo($n,PATHINFO_EXTENSION))==$e) $r[]=$n;
  }
} else die("Can't open directory: $p");
sort($r,SORT_NUMERIC);
return $r;
}

// Генерира и съдържанието на файл titles.php

function gallery02_tf($fl){
$rz = "<pre>&lt;?php\n\n\$img_title = array(\n";
foreach($fl as $v){
  $rz .= "'$v'=>'";
  if(isset($img_title[$v])) $rz .= $img_title[$v];
  $rz .= "',\n";
}
$rz = substr($rz,0,-2)."\n";
$rz .= ");\n\n?></pre>";
return $rz;
}

?>

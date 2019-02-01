<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията gallery01($a) връща html код за показване на галерия от снимки.

// Файловете със снимки трябва да се намират в директория $a относно главната директория на сайта.
// Умаленият размер снимки трябва да се намират във файлове със същите имена, в поддиректория "$a/th".

// Заглавията на снимките се дефинират във файл "$a/titles.php в асоциативен масив с име $title, който има за ключове
// имената на файловете и стойности - заглавията на снимките.

// За да се показват заглавията под умалените снимки във файл titles.php трябва да е дефинирана променлива
// $write_title = true; В противен случай заглавията се показват само като изскачащи надписи.

// В случай, че умалените снимки не трябва да бъдат линкове, във файл titles.php
// се дефинира променлива $no_links = true;

include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_set_self_query_var.php');

function gallery01($a){
$i = 0; // Номер на снимката, която да се покаже
if (isset($_GET['iid'])) $i = 1*$_GET['iid'];
// Абсолютен път до директорията със снимките
$p = $_SERVER['DOCUMENT_ROOT']."/$a";
// Зареждане заглавията на снимките
$tf = $p."/titles.php";
if (file_exists($tf)){
  include_once($tf);
  // Списък на *.jpg файловете
  $fl = array_keys($title);
}
else $fl = file_g_list($p,'jpg');
if( !count($fl) ) return encode('Не са намерени файлове в директория ').$p;
if (!isset($write_title)) $write_title = false;
if (!isset($no_links)) $no_links = false;
// Генериране на html кода
$rz = '<div id="gallery01">'."\n";
if ( !$i || $no_links ) // Ако не е изпратен номер на снимка, или е зададено да няма линкове
                        // се показват всички снимки в умален размер
foreach($fl as $j => $f){
  $tn = "$a/th/$f";
  if (!file_exists($_SERVER['DOCUMENT_ROOT']."/$tn")) $tn = "$a/$f";
  if (isset($title[$f])) $tt = encode($title[$f]); else $tt = '';
  if (!$no_links){ $a1 = '<a href="'.set_self_query_var('iid',$j+1).'#big">'; $a2 = '</a>';}
  else { $a1 = ''; $a2 = ''; }
  $rz .= '<div><div>'."\n".
         "$a1<img alt=\"".strip_tags($tt)."\" title=\"".strip_tags($tt)."\" src=\"/$tn\">$a2\n";
  if ($write_title) $rz .= "<br>$tt\n";
  $rz .= "</div></div>\n";
}
else { // Ако е изпратен номер на снимка се показва само една снимка
  if (isset($fl[$i-1])) $f = $fl[$i-1]; else $f = '';
  if (isset($title[$f])) $tt = encode($title[$f]); else $tt = '';
  $k = strlen($_SERVER['DOCUMENT_ROOT']);
  $mp = dirname(__FILE__);
  $mp = substr($mp,$k,strlen($mp)-$k).'/';
  $mp = str_replace('\\','/',$mp);
  // Бутони за навигация
  $nv = "<p id=\"big\">\n";
  if ($i>1) $nv .= '<a href="'.set_self_query_var('iid',$i-1).'#big"><img alt="prev" src="'.$mp.
                   'arrow_prev.gif"></a>'."\n";
  $nv .= '<a href="'.set_self_query_var('iid',0).'#big"><img alt="up" src="'.$mp.'arrow_up.gif"></a>'."\n";
  if ($i<count($fl)) $nv .= '<a href="'.set_self_query_var('iid',$i+1).'#big"><img alt="next" src="'.$mp.
                            'arrow_next.gif"></a>'."\n";
  $nv .= "<p>\n";
  $rz .= "$nv<p><img alt='$tt' src=\"/$a/$f\" class=\"big\"></p>\n";
  $rz .= "<p>$tt</p>\n$nv";
}
$rz .= '</div>
<p style="clear:both;">&nbsp;</p>';
return $rz;
}

// Връща масив с имената на файловете от директория $p
// които имат разширение $e
function file_g_list($p,$e){
$r = array(); // Връщания резултат
if( !file_exists($p) ) return $r;
if ($d = opendir($p)){
  while(($n = readdir($d))!==false){
    if (strtolower(pathinfo($n,PATHINFO_EXTENSION))==$e) $r[]=$n;
  }
}
sort($r,SORT_NUMERIC);
return $r;
}

?>

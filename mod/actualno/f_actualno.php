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

// Модул за обслужване на рубрики "Новини" и "Съития и покани"

// При $a='show' - показване на новина номер $_GET['nid'], а ако $_GET['nid']=='all' - показване на всички новини.
// В случай, че $_GET['nid'] не е дефиниран или задава несъществуващ номер,
// се показва най-новата новина на съответния език.

// При $a='showevent' - се показват събития.

// При $a='abstracts' - в рамката за новини се показват не само линкове, но и резюмета на новините.

include_once($idir.'lib/f_db2user_date_time.php');
include_once($idir.'lib/f_stored_value.php');

global $actualno_news_page, $actualno_events_page;

$actualno_news_page   = stored_value('actualno_news_page',    99);
$actualno_events_page = stored_value('actualno_events_page',106);

//
// Показване на рубриката на главната страница

function actualno($a = ''){
if (($a=='show')||($a=='showevent')) return actualno_show($a); // за страници, показващи всички или отделни новини
if  ($a=='events')                   return actualno_events(); // каре "Събития и покани" на нач. страница
// Каре "Новини" на началната страница
global $language, $main_index, $actualno_news_page;
$d = date("Y-m-d H:i:s", time()-30*24*3600);
$al = db_select_m('*','actualno',
                  "`type`='News' AND `Active`='Yes' AND `lang`='".$language."' order by `Date` DESC LIMIT 6");
$rz = '<div class="actualno_'.$language.'">
';
if (!count($al)) $rz .= translate('actualno_nonews').actualno_edit_links(0);
$ms = '';
$c = 0; //  Брояч
foreach($al as $row){
 if ($a=='abstracts'){
    if (($row['Date']<$d)&&($c>5)) break;
    $rz .= actualno_edit_links($row['ID']).actualno_abstract($row);
 }
 else $rz .= '<a href="'.$main_index.'?pid='.$actualno_news_page.'&amp;nid='.$row['ID'].'">'.$row['Link']."</a><br><br>\n";
  $c++;
}
return $rz.'
</div>';
}

function actualno_events(){
global $language, $actualno_events_page;
// Времето на настоящия момент в MySQL формат
$d = date("Y-m-d H:i:s");
// Ако е зададена стартова дата:
$w = " AND (`StartDate`<='0000-01-01 00:00:00' OR `StartDate`<'$d')";
if(in_edit_mode()) $w = '';
// Четене на предстоящите събития
$al = db_select_m('*','actualno',
      "`type`='Event' AND `Active`='Yes' AND `lang`='".$language."' AND `Date`>'$d' $w ORDER BY `Date` ASC");
$rz = '<div class="actualno_'.$language.'">
';
$c = 0; //  Брой показани съобщения
foreach($al as $row){
  $rz .= actualno_edit_links($row['ID'], 'Event').actualno_abstract($row,true,$actualno_events_page);
  $c++;
}
$c1 = 5-$c;
// Ако са показани по-малко от 4 се показват и стари съобщения
if ( $c1>0 ){
  $al = db_select_m('*','actualno',
      "`type`='Event' AND `Active`='Yes' AND `lang`='".$language."' AND `Date`<='$d' $w ORDER BY `Date` DESC LIMIT 0,$c1");
  if(count($al)) $rz .= "<h2>".translate('actualno_oldevents')."</h2>\n";
  foreach($al as $row){
    $rz .= '<div class="old">'.actualno_edit_links($row['ID'], 'Event').actualno_abstract($row,true,$actualno_events_page)."</div>\n";
    $c++;
  }
}
if (!$c) $rz .= translate('actualno_nonews').actualno_edit_links(0);
return $rz.'
</div>';
}

//
// Съставяне на "Резюме", което се показва на главната страница
// в случай, че няма съставено наготово такова.
// $y трябва да е false, за да не се използва поле `Abstract` дори и да има такова.
// Последното се използва за създаване на бутона "Автоматично резюме" на страницата за редактиране.

function actualno_abstract($d, $y = true, $np = '99'){
global $page_id, $main_index, $actualno_news_page;
if($np=='99') $np = $actualno_news_page;
if(!isset($page_id)) $page_id = $np;
// Ако има поле `Abstract` се използва това поле
if ($d['Abstract'] && $y) return $d['Abstract'];
$pc = parse_content($d['Content']);
// Дата
$rz = '<p class="date">'.db2user_date_time($d['Date'],false)."</p>\n".
       "<div>\n";
// Изображение, ако има
$rz .= actualno_image($pc);
$t = '';
if(isset($_GET['template'])){
  $t = 1*$_GET['template'];
  $at = stored_value('allowed_templates');
  $p = strpos($at, ",$t,");
  if(!$p===false) $t = "&amp;template=$t";
  else $t = '';
}
$tg = '';
$h = parse_url($d['url'],  PHP_URL_HOST);
if($h && ($h!=$_SERVER['HTTP_HOST'])) $tg = ' target="_blank"';
// Заглавие
if ($d['url']) $rz .= '<h2><a href="'.$d['url']."\"$tg>".actualno_title($d)."</a></h2>\n";
else $rz .= '<h2><a href="'.$main_index.'?pid='.$np.'&amp;nid='.$d['ID'].$t.'">'.actualno_title($d)."</a></h2>\n";
// Кратък текст
//if ($d['url']) ; //$rz .= '<p class="url"><a href="'.$d['url'].'">'.translate('actualno_link').'</a></p>';
//else 
	$rz .= actualno_short_text($d['Content']);
return "$rz\n</div>\n";
}

function actualno_image($pc){
// Търсене на изображение
$r = array();
$i = preg_match_all('/<img.*src="(.*?)".*(alt=".*?").*>/', $pc, $r);
$j = 0;
if (!$i) $j = preg_match_all('/<img.*(alt=".*?").*src="(.*?)".*>/', $pc ,$r);
// Изображение, ако има такова
if ($i)      return '<img src="'.actualno_small_image($r[1][0]).'" '.$r[2][0].' style="max-width:100px;max-height:100px;float:left;margin-right:10px;">'."\n";
else if ($j) return '<img src="'.actualno_small_image($r[2][0]).'" '.$r[1][0].' style="max-width:100px;max-height:100px;float:left;margin-right:10px;">'."\n";
return '';
}

//
// Автоматично създава малко изображение в директория /small_images

function actualno_small_image($ip){ //echo($ip."<br>\n");
$ex = strtolower( pathinfo($ip, PATHINFO_EXTENSION) );
//if($ex=='svg')
    return $ip;
global $no_small_image;
if(isset($no_small_image) && $no_small_image) return $ip;
static $error = false;
if($error) return $ip;
$sc = parse_url($ip, PHP_URL_SCHEME);
if($sc) $fip = $ip;
else{
  $fip = $_SERVER['DOCUMENT_ROOT'].rawurldecode($ip);// echo("$fip<p>\n");
  if(!file_exists($fip)) return $ip;
}
$rz = '/small_images/'.basename(rawurldecode($ip));
$nip = $_SERVER['DOCUMENT_ROOT'].$rz; // echo("$nip<p>");
if( !file_exists($nip) || !(filesize($nip)) ){
  $fo = ini_get("allow_url_fopen");
  ini_set("allow_url_fopen", 1);
  $d = dirname($nip);
  if(!file_exists($d)){ if(!$error) echo("Module ACTUALNO: directory '$d' do not exist."); $error = true;    return $ip; }
  if(!is_writable($d)){ if(!$error) echo("Module ACTUALNO: directory '$d' is not writable."); $error = true; return $ip; }
  list($w1, $h1) = getimagesize($fip);
  if(!$w1 || !$h1){ echo("<p>Module ACTUALNO: Zero size of '$fip' image.</p>"); return $ip; }
  if($w1>$h1){
    $w2 = 100;
    $h2 = $h1 * 100 / $w1;
  }
  else {
    $h2 = 100;
    $w2 = $w1 * 100 / $h1;
  }
  switch($ex){
  case 'jpg': case 'jpeg': $i1 = imagecreatefromjpeg($fip); break;
  case 'png': $i1 = imagecreatefrompng( $fip); break;
  case 'gif': $i1 = imagecreatefrompng( $fip); break;
  default: return $ip;
  }
  $i2 = imagecreatetruecolor($w2, $h2);
  imagecopyresampled($i2, $i1, 0, 0, 0, 0, $w2, $h2, $w1, $h1);
 // if(!is_writable($nip)){ if(!$error) echo("Module ACTUALNO: file '$nip' is not writable."); $error = true; return $ip; }
  $f = fopen($nip, 'w');
  switch($ex){
  case 'jpg': case 'jpeg': imagejpeg($i2, $nip); break;
  case 'png': imagepng( $i2, $nip); break;
  case 'gif': imagegif( $i2, $nip); break;
  }
  ini_set("allow_url_fopen", $fo);
}
if(!filesize($nip)) return $ip;
//echo("$rz<p>\n");
return str_replace(' ', '%20', $rz);
}

//
// "Изрязване" от текста на първите му $l букви

function actualno_short_text($a, $l=200){
global $language;
$a = strip_tags($a);
$a = str_replace('&nbsp;',' ',$a);
$a = str_replace("\n",' ',$a);
$a = str_replace("\r",'',$a);
$a = trim($a);
$fl = strlen($a)-1;
$letters = array(
'en'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
'bg'=>'абвгдежзийклмнопрстуфхцчшщъьюяАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯ'
);
while ( ($l<$fl) && !in_array($a[$l], array(' ', ',', '.', ':', '-', '&') ) ) $l++;
$rz = substr($a,0,$l);
if (strlen($a)>strlen($rz)) $rz .= '...';
return $rz;
};

//
// Показване на една новина на страницата за новини

function actualno_show($a){
global $language, $og_image, $og_title, $og_description, $og_pid, $page_id;
// Номер на новината
$i = 0;
if (isset($_GET['nid'])){
  if ($_GET['nid']=='all') return '<div id="news_frame">'.actualno_all($a).'</div>';
  $i = 1*$_GET['nid'];
}
// Четене на новината
$t = 'News';
if ($a=='showevent') $t = 'Event';
if ($i==0){
  $d = db_select_1('*', 'actualno', "`type`='$t' AND `lang`='$language' ORDER BY `Date` DESC");
  if (!$d) return translate('actualno_nonews');
}
else $d = db_select_1('*', 'actualno', "`ID`=$i");
if (!$d) return "Incorrect news id";
$dt = date("Y-m-d H:i:s");
$vis = in_edit_mode() || ($d['StartDate']<='0000-01-01 00:00:00') || ($d['StartDate']<$dt);
// Номер на по-нова новина
$i1 = db_table_field('ID', 'actualno',
      "`type`='$t' AND `Date`>'".$d['Date']."' AND `lang`='$language' ORDER BY `Date` ASC LIMIT 0,1");
$nv1 = '';
if ($i1) $nv1 = '<a href="'.set_self_query_var('nid',$i1).'">'.translate('actualno_newer'.$t).'</a>';
// Номер на по-стара новина
$i2 = db_table_field('ID', 'actualno',
      "`type`='$t' AND `Date`<'".$d['Date']."' AND `lang`='$language' ORDER BY `Date` DESC LIMIT 0,1");
$nv2 = '';
if ($i2) $nv2 = '<a href="'.set_self_query_var('nid',$i2).'">'.translate('actualno_older'.$t).'</a>';
// Заглавие
$tt = actualno_title($d);
$rz = '';
$nv = '<p class="navigation">'.$nv1.
      ' &nbsp; <a href="'.set_self_query_var('nid','all').'">'.translate('actualno_allnews'.$t).'</a> &nbsp; '.
      $nv2."</p>\n";
if (in_edit_mode()) $rz .= '<p>'.actualno_edit_links($i, $t).'</p>';
$rz .= '<div id="news_frame">
'.$nv;
if($vis) $rz .= "<h1>$tt</h1>\n".'<p class="date">'.db2user_date_time($d['Date'],false)."</p>";
$pc = parse_content($d['Content']);
// За фейсбук
$r = array();
$j = preg_match_all('/<img.*src="(.*?)".*?>/', $pc, $r);
if($j){
  $f = 0;
  $u = stored_value('uploadfile_dir', '');
  while(($f<$j) && (substr($r[1][$f], 0, strlen($u))!=$u)){ $f++; } //die(print_r($r,true).$u);
  if($f<$j){ $og_image = 'http://www.vsu.bg'.$r[1][$f]; }
}
$og_pid = "$page_id&nid=$i";
$og_description = $tt;
if(!$vis) $rz .= "<p>Nothing to display</p>\n";
else $rz .= $d['Content'];
if ($d['url']) $rz .= '<p class="url"><a href="'.$d['url'].'">'.translate('actualno_link').'</a></p>';
$rz .= $nv.'</div>';
return $rz;
}

//
// Линкове Edit и New в режим на редактиране

function actualno_edit_links($i, $t = "News"){
$rz = '<p style="margin:20px 0 0 0;">';
if (in_edit_mode()){
  $pp = current_pth(__FILE__);
  if($i>0) $rz .= ' <a href="'.$pp.'edit_news.php?id='.$i.'&type='.$t.'">Edit</a>';
  $rz .= ' <a href="'.$pp.'edit_news.php?id=0&type='.$t.'">New</a> ';
}
return $rz."</p>\n";
}

//
// Заглавие на новината

function actualno_title($d){
$tt = $d['Title'];
if (!$tt) $tt = $d['Link'];
return $tt;
}

//
// Показване на всички новини на страницата за новини

function actualno_all($a){
global $language, $actualno_news_page, $actualno_events_page;
$perpage = 10; // Брой новини на страница
// От кой номер да се показват
$p1 = 0;
if (isset($_GET['pg'])) $p1 = 1*$_GET['pg']*$perpage;
if ($p1<0) $p1=0;
// До кой номер да се показват
$p2 = $p1 + $perpage;
// Четене на всички новини на текущия език
$t = 'News'; $np = $actualno_news_page;
$w = '';
if ($a=='showevent'){
  $t = 'Event';
  $np = $actualno_events_page;
  $d = date("Y-m-d H:i:s");
  $w = " AND (`StartDate`<='0000-00-00 00:00:00' OR `StartDate`<'$d')";
}
if(in_edit_mode()) $w = '';
$da = db_select_m('*', 'actualno', "`type`='$t' AND `lang`='$language' $w ORDER BY `Date` DESC ");
if (!count($da)) return translate('actualno_nonews');
if ($p2>count($da)){ $p2=count($da); $p1=$p2-$perpage; }
if ($p1<0) $p1=0;
$rz = '';
$nv = ''; // Лента за навигация
$n2 = '';
$pg = 0;  // Номер на страница с новини
foreach($da as $i=>$d){
  // Генериране на навигацията
  if ($i % $perpage == 0){
     if (($i>=$p1)&&($i<$p2)){
        // Стрелка за назад
        if ($pg>0) $nv = '<a href="'.set_self_query_var('pg',$pg-1).'"><</a> '.$nv;
        // Номер на текуща страница
        $nv .= ' <span>'.($pg+1)."</span>";
        // Стрелка за напред
        if ($pg<floor(count($da)/$perpage)) $n2 = ' <a href="'.set_self_query_var('pg',$pg+1).'">></a>';
     }
     // Линк към друга страница
     else $nv .= ' <a href="'.set_self_query_var('pg',$pg).'">'.($pg+1).'</a>';
     $pg++;
  }
  // Показване на новини
  if (($i>=$p1)&&($i<$p2)){
     $rz .= actualno_abstract($d,true,$np);
  }
}
$rz .= "<p>&nbsp;</p>\n";
$nv .= $n2; 
return "$rz\n<p class=\"pages\">$nv</p>";
}
?>

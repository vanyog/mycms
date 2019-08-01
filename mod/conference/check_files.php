<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Проверява дали файловете, качени в директорията на конференцията съответстват на базата данни

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_is_local.php');
include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_file_list.php');
include_once($idir.'lib/f_encode.php');

// Тип на потребителя
$ut = stored_value('conference_usertype', 'vsu2014');

// Директория с качени файлове за конференцията
$p = stored_value('conference_files', '/conference/2014/files/');
$dir = $_SERVER['DOCUMENT_ROOT'].$p;

// Четене имената на DOC файловете
$af = db_select_m('fulltextfile', 'proceedings', "`utype`='$ut' AND `fulltextfile`>''");
$ap = array();
header("Content-Type: text/html; charset=$site_encoding");
echo encode("<h2>Липсващи DOC файлове</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile'];
  $ap[] = $f['fulltextfile'];
  if (!file_exists($fn)) echo $fn."<br>";
}

// Четене имената на PDF файловете
$af = db_select_m('fulltextfile2', 'proceedings', "`utype`='$ut' AND `fulltextfile2`>''");
echo encode("<h2>Липсващи PDF файлове</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile2'];
  $ap[] = $f['fulltextfile2'];
  if (!file_exists($fn)) echo $fn."<br>";
}

$fl = file_list($dir);

echo encode("<h2>Излишни файлове</h2>");
foreach($fl as $f)
 if (!in_array($f,$ap)){
    if (!is_local()) $fl = rawurlencode($f); else $fl = $f;
    echo "<a href=\"$p$fl\">$f</a><br>\n";
 }

?>

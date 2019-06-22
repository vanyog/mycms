<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// ��������� ���� ���������, ������ � ������������ �� ������������� ������������ �� ������ �����

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_is_local.php');
include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_file_list.php');
include_once($idir.'lib/f_encode.php');

// ��� �� �����������
$ut = stored_value('conference_usertype', 'vsu2014');

// ���������� � ������ ������� �� �������������
$p = stored_value('conference_files', '/conference/2014/files/');
$dir = $_SERVER['DOCUMENT_ROOT'].$p;

// ������ ������� �� DOC ���������
$af = db_select_m('fulltextfile', 'proceedings', "`utype`='$ut' AND `fulltextfile`>''");
$ap = array();
header("Content-Type: text/html; charset=$site_encoding");
echo encode("<h2>�������� DOC �������</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile'];
  $ap[] = $f['fulltextfile'];
  if (!file_exists($fn)) echo $fn."<br>";
}

// ������ ������� �� PDF ���������
$af = db_select_m('fulltextfile2', 'proceedings', "`utype`='$ut' AND `fulltextfile2`>''");
echo encode("<h2>�������� PDF �������</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile2'];
  $ap[] = $f['fulltextfile2'];
  if (!file_exists($fn)) echo $fn."<br>";
}

$fl = file_list($dir);

echo encode("<h2>������� �������</h2>");
foreach($fl as $f)
 if (!in_array($f,$ap)){
    if (!is_local()) $fl = rawurlencode($f); else $fl = $f;
    echo "<a href=\"$p$fl\">$f</a><br>\n";
 }

?>

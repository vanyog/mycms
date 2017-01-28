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

// ������� ������ �� ���������� �� ��������
 
// �������� �� ������������ � �����, ����� �� ������� � $_GET['pid']
// ����� �������� �� ������ � ������, ��������, ���������� � ��., ����� �� ��������
// � ����� �� ������� $tn_prefix.`pages`.


if(!ob_start("ob_gzhandler")) ob_start();

$exe_time = microtime(true);

error_reporting(E_ALL); ini_set('display_errors',1);

if (phpversion()>'5.0') date_default_timezone_set("Europe/Sofia");

// ��� �� ������������ �� ���������
$idir = dirname(__FILE__).'/';

// ��� �� ���� conf_database.php � ����� �� ������ �� ������ �����. 
// ���� �� � �������� �� ���� � $idir, ��� � ����������.
$ddir = $idir;

if (
  !file_exists($idir.'conf_database.php')
  || !file_exists($idir.'conf_paths.php')
) 
die('The system is not propperly installed. See <a href="http://vanyog.com/_new/index.php?pid=91" target="_blank">USAGE.txt</a> file.');

$page_header = ''; // ������� ��� ������ �� ����������

include_once($idir.'lib/f_stored_value.php');
load_options(array(
  'main_index_pageid',
  'cache_time',
  'error_404_template',
  'today',
  'sitesearch_nocoleron',
  'acceptable_params'
));
include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_parse_template.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_page_cache.php');
include_once($idir.'lib/f_db_table_status.php');

header("Content-Type: text/html; charset=$site_encoding");

// ����� �� ��������� ����
$ind_fl = $_SERVER['PHP_SELF'];

$body_adds   = ''; // ������� ��� body ����

$can_edit = false;     // ����� �� ����������� �� ��������� ��������� �� ����������
$can_create = false;   // ����� �� ����������� �� ������/������� �������� � ������� ������(�������) �� �����
$can_manage = array(); // ����� �� �������������� �� ������
$can_visit = true;     // ����� �� ������ ���������� �� ����� ������������ �� ����������.

// ����� �� ����������
$page_id = stored_value('main_index_pageid',1);
if (isset($_GET['pid'])) $page_id = 1*$_GET['pid'];

// �������� �� ����������
$page_title = '';

// ���� �� ���������� �� ���������� �� ������� $tn_prefix.'pages'
$page_data = db_select_1('*','pages',"ID=$page_id");
if (!$page_data) $page_data = page404();

// ������������ ��� http, ��� �� � ��������� https ��������
include_once($idir.'lib/f_stop_https.php');
stop_https($page_data['content']);

// �������� �� ����������
$page_title = translate($page_data['title']);

// ����� � �����
$page_options = '';
if ($page_data['options']) { $page_options = explode(' ',$page_data['options']); }

// ������ �� html ���� �� ���������� �� ����
$cnt = page_cache();

// ��� ���������� �� � ��������� �� ���� �� ��������
if (!$cnt){

 // ��������� ��� ���������� �� ���������� � �������
 $cnt = parse_template($page_data);
 // ��������� � ����
 $t = stored_value('cache_time');
 if ($t) save_cache($cnt);
}

// ��� ���������� �� � �������� �� ����������� �� �������� Access denied
if (!$can_visit) {
  if (session_id()) session_destroy();
  header("Status: 403");
  die("Access denied by index.php.");
}

// ����� �� ������������ �� ����������
count_visits($page_data);

// ���������� �� ������� ����
$cnt = colorize($cnt);

$exe_time = number_format(microtime(true) - $exe_time, 3);

// ��������� ���� �� MYSQL ��������, ��� � ���������� �� �� ��������
$cnt = str_replace('<!--DB_REQ_COUNT-->',"$db_req_count $exe_time ", $cnt);

// ��������� �� ����������
echo $cnt;

// --------------------------------

// ����� ��������, ����� ������� ������, �� ���� �������� � ����� �����
function page404(){
$rz = Array (
'ID' => 0,
'menu_group' => 1,
'title' => 'error_404_title',
'content' => 'error_404_content',
'template_id' => stored_value('error_404_template',1),
'hidden' => '0',
'options' => '',
'tcount'=>0,
'dcount'=>0,
'donotcache'=>1
);
//print_r($rz); die;
return $rz;
}

// ���� �����������
function count_visits($p){
global $tn_prefix, $db_link, $idir, $can_edit;
include_once($idir."lib/f_adm_links.php");
// ��� �� �������� ������� �� ��������������, ��� ���� �� ����������� �� ����������, �� �� ���� ����
if ( ($p['ID']==0) || show_adm_links() || $can_edit ) return '';
new_day();
$q = "UPDATE `$tn_prefix"."pages` SET dcount = dcount+1 WHERE `ID`=".$p['ID'].";";
mysqli_query($db_link,$q);
}

// ��� ������� ��� ��� �� �������� ������� �� ���������� ��������� � ������� $tn_prefix.'visit_history'
function new_day(){
global $apth, $tn_prefix, $db_link, $idir;
// ���� �� ���������� ���� �� ������� $tn_prefix.'options'
include_once($idir.'lib/f_stored_value.php');
$td = stored_value('today');
$d = getdate();
// ��� �� �� � ������� ������ �� �� ����� ����
if ($d['mday']==$td) return;
// ������� �� ���������� ������ � ������� $tn_prefix.'options'
store_value('today',$d['mday']);
$dd = $d['year'].'-'.$d['mon'].'-'.$d['mday'];
// ����� �� �������� �� ���������� ���� ���� �������� �� ������� $tn_prefix.'pages'
$dt = db_select_m('ID,dcount','pages','`dcount`>0');
// ������� �� ���� ��������� �� ����� �������� � ������� $tn_prefix.'visit_history'
$q = "INSERT INTO `$tn_prefix"."visit_history` (`page_id`, `date`, `count`) VALUES\n";
foreach($dt as $r){
  $q .= "(".$r['ID'].", '$dd', ".$r['dcount']."),\n";
}
$q = substr($q, 0, strlen($q)-2).";";
mysqli_query($db_link,$q);
// ������ �� ���� �� ����������� � ������� $tn_prefix.'pages'
$q = "UPDATE `$tn_prefix"."pages` SET tcount = tcount + dcount, dcount = 0;";
mysqli_query($db_link,$q);
// ��������� ����� �� ������� �� ������� content ��� �������� ����
$q = "INSERT INTO `$tn_prefix"."content_history` (`date`, `size`) VALUES ".
     "('$dd', ".db_table_status('content', 'Data_length').");";
mysqli_query($db_link,$q);
}

// ���������� �� ������� ����

// ���� �� ���������� ����� �� �������� �� ��������� ����
$word_pattern = '';

function colorize($cnt){
if (isset($_SESSION['text_to_search'])){
  // ��������, �� ����� �� �� ����� ����������
  $a = stored_value('sitesearch_nocoleron', '$nocolor = array();');
  if ($a) eval($a);
  global $page_id, $word_pattern;
  // ���� � ���������� ������ � preg_replace
  $GLOBALS['preg_error']=false;
  if (!in_array($page_id, $nocolor)){
    $ca = explode('<body',$cnt);
    $wa = array_unique(explode(' ',$_SESSION['text_to_search']));
    foreach($wa as $w){
      $word_pattern = to_regex($w);
      $ca[1] = preg_replace_callback('/>([^<]*?)</is', 'colorize1', $ca[1]);
    }
    $cnt = implode('<body',$ca);
  }
}
return $cnt;
}

function to_regex($w){
$w1 = mb_strtoupper($w);
$w2 = mb_strtolower($w);
$rz = '';
for($i=0;$i<strlen($w1);$i++){ 
  if (in_array($w[$i],array('/','.','^')))
    $rz .= '\\'.$w[$i];
  else
    $rz .= '['.$w1[$i].$w2[$i].']';
}
return $rz;
}

function colorize1($a){
$a1 = trim(str_replace('&nbsp;', ' ', $a[1]));
if (!$a1) return $a[0];
global $word_pattern;
$pt = '/([^a-zA-Z�-��-�])('.$word_pattern.')([^a-zA-Z�-��-�])/is';
$rp = '\1<span class="searched">\2</span>\3';
// ��� ���� � ���������� ������ � preg_replace - �� �� ����.
// ����� � �� �� ������� ������������� ��������� �� ��������� �� ������.
if ($GLOBALS['preg_error']) return $a[0];
$rz = preg_replace($pt, $rp, $a[0]);
if (!$rz){
  $GLOBALS['preg_error'] = true;
  return $a[0];
}
else return $rz;
}

?>


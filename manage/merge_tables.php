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

// ������� �� ��� ������� �� ������ �����.
// ���� ������� �� ������� $_GET['t1'] � �� ������� � � ������� $_GET['t2'].
// ����������� t1 � t2 �� ������� ����� �� ��������� � ����������.

// ��� ���������� �� �������� �� ����� ����.
// ��� �������� ����� �������� ����� � ������� ���������.

include('conf_manage.php');
include($idir.'lib/f_db_select_1.php');
include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_insert_1.php');
include($idir.'lib/f_db_update_record.php');
include($idir.'lib/o_form.php');

if (!isset($_GET['t1'])) die('������ ��������� t1=�����������������');
if (!isset($_GET['t2'])) die('������ ��������� t2=�����������������');

$t1 = $_GET['t1'];
$t2 = $_GET['t2'];

set_prefix('');

session_start();

process_post();

// ������ �� ������ ����� �������
$d = db_select_m('*',$t1,'1');

// ���� ��������� ������
$c = count($d);

$page_content = "<p>��������� �� ������� $t1 $c ������.</p>\n";

// $fn - ������� ��� �� ����, �������� �� 'ID'.
$fn = '';
if ($c) $fn = fnoidfield($d[0]);

if (!$fn) $page_content .= "<p class=\"warning\">���� ��� �� ����, �������� �� 'ID'.</p>";

// ���� ����������
$sk = 0;

// �� ����� �������� �����
if ($c) foreach($d as $r){
//  echo $r['ID'].':'.$r[$fn]."<br>";
  // ��� � ������� ����������
  if (!(strpos($_SESSION['records_to_skip'], ",".$r['ID']."," )===false)){ $sk++; continue; }
  // �������� �� ������ ������ �� ������� �������, �� ����� ���� $fn ������� ��� ������ ���� �� �������� ����� �� ������� �������
  $d2 = db_select_m('*',$t2,"`$fn`='".$r[$fn]."'");
  // ��� ���� ������ ������, ������ �� ������� ������� �� ������ ��� �������.
  if (!count($d2)) {
    $r1 = $r;
    unset($r1['ID']);
    $i = db_insert_1($r1,$t2);// echo "$i <br>";
  }
  // ����� �������� ��������
  else{
   // ��� ������ ���������� ���������� ������������
   foreach($d2 as $r2) if ($s = same_records($r,$r2)) break;
   // ��� � ������� ������� ����� � ���� ����� �� �� �������
   if (!$s){
     $page_content .= what_to_do($r,$d2);
     break;
   }
  }
}

$page_content .= "<p>����������: $sk.</p>";

include('build_page.php');

//
// ������� ��� �� ���� �������� �� ID 
//
function fnoidfield($a){
if (!is_array($a)) return '';
$ks = array_keys($a);
foreach($ks as $k) if ($k!='ID') return $k;
return '';
}

//
// �������� �������� $r1 � $r2.  ����� ������ ��� �� ���������� ���� �� 'ID'.
//
function same_records($r1,$r2){
foreach($r1 as $f => $v){
  // ��� ��� ������ ����� ���� ���� ��� ������ ��� - ������� � ����������� �� ���������
  if (!isset($r2[$f])){
    print_r($r1); echo "<br>"; 
    print_r($r2); echo "<br>";
    die("����� ������� ����� ������� ���������");
  }
//  echo $r1[$f].' -- '.$r2[$f].'<br>';
  if ( ($f!='ID') && ($r1[$f]!=$r2[$f]) ) return false;
}
return true;
}

//
// ��� ��������� �� ������� � �������� �� ������� ����� �� ����� ����� �� �� �������
//
function what_to_do($r,$d2){
$rz = "<p>\n".print_r($r,true)."<br>\n";
foreach($d2 as $d) $rz .= print_r($d,true)."<br>\n";
$rz .= "</p>\n";
$c = count($d2);
$f = new HTMLForm('whattodo');
$f->add_input( new FormInput('','id1','hidden',$r['ID']));
if ($c==1) $f->add_input( new FormInput('','id2','hidden',$d2[0]['ID']));
$f->add_input( new FormInput('��������:','what','radio','insert','�� �� ������'));
$f->add_input( new FormInput('','what','radio','skip','�� �� ��������'));
if ($c==1) $f->add_input( new FormInput('','what','radio','update','�� �������'));
$f->add_input( new FormInput('','what','radio','terminate','���� �� ���������'));
$f->add_input( new FormInput('','','submit','������������')); 
return $rz.$f->html();
}

//
// ��������� �� ����������� � $_POST �����
//
function process_post(){
if (!isset($_SESSION['records_to_skip'])) $_SESSION['records_to_skip'] = '';
if (!count($_POST)) return '';
global $t1,$t2;
$id1 = 1*$_POST['id1'];
// ������ �� ������ �� ������� $t1
$r1 = db_select_1('*',$t1,"`ID`=$id1");
switch ($_POST['what']){
case 'insert':
  unset($r1['ID']);
  db_insert_1($r1,$t2);
  break;
case 'skip':
  if (!isset($_SESSION['records_to_skip'])) $_SESSION['records_to_skip'] = ',';
  $_SESSION['records_to_skip'] .= "$id1,";
//  print_r($_SESSION); die;
  break;
case 'terminate':
  unset($_SESSION['records_to_skip']);
  die("��������� ���� ����������.");
  break;
case 'update':
  $r1['ID']=1*$_POST['id2'];
  db_update_record($r1,$t2);
  break;
default: die("��������� �������� ".$_POST['what']);
}
return '';
}

?>

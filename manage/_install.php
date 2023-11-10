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

// ������ �� ��������� �� ������� � ������ ����� �� ������� �� ��������� ��� �� ��������.
// SQL �������� �� ��������� � �������� �� ����� � ��������� �� ��� ���� tables.sql -
// �� ��������� - � ���������� manage
// �� �������� - � ������������ �� ���������� ����� mod/����������.
// $_GET['m'] � ����� �� ������.
// $_GET['c'] � ����� ��� �� ���������� � ���� conf_database.php. �������� �� ������ ���� � ����
// ������� �� �������� � � ������������ �� ����.
// ��� �� � ������� $_GET ���������, �� ������� ���� conf_database.php

error_reporting(E_ALL); ini_set('display_errors',1);

include('conf_manage.php');

if (isset($_GET['c'])){
   $ddir = $_GET['c'];
}
else {
   // ��� ���� conf_database.php ����, ��������� �� ����� �� ����������� ��
   if (!file_exists($idir.'conf_database.php')) create_conf_database();
}

//include($idir.'conf_paths.php');

$p = 'tables.sql';

// ��� � ��������� ��� �� �����, $p � .sql ���� �� ������������� �� 
if (isset($_GET['m'])) $p = $_SERVER['DOCUMENT_ROOT'].$mod_pth.$_GET['m']."/$p";
// ��� �� � ��������� ��� �� �����, �� ��������� ������ �������
else create_conf_database();

include($idir.'conf_database.php');

$site_encoding = $site_encoding = 'UTF-8';

header("Content-Type: text/html; charset=$site_encoding");

// ��� .sql ���� �� � � ���������� $mod_pth �� ��������� � ���������� 'mod/'.$_GET['m']
if (!file_exists($p)){
  $p = $_SERVER['DOCUMENT_ROOT'].$pth.'mod/'.$_GET['m']."/tables.sql";
  if (!file_exists($p)) die("$p file not found");
}

$fc = file_get_contents($p);

/*$fc = iconv('windows-1251', $site_encoding, file_get_contents($p));

$fc = str_replace('CREATE TABLE IF NOT EXISTS `', "CREATE TABLE IF NOT EXISTS `$tn_prefix", $fc);*/

$fc = str_replace('CREATE TABLE `', "CREATE TABLE `$tn_prefix", $fc);

$fc = str_replace('ALTER TABLE `', "ALTER TABLE `$tn_prefix", $fc);

$fc = str_replace('INSERT INTO `', "INSERT INTO `$tn_prefix", $fc);

/*$fa = explode('-- --------------------------------------------------------',$fc);

foreach($fa as $q){
  echo ("<p><pre>$q</pre></p\n>");
  mysqli_query($db_link,$q);
  echo mysqli_error($db_link);
}*/

mysqli_multi_query($db_link, $fc);

echo '<p>Success</p>

<p><a href="'.dirname($_SERVER['PHP_SELF']).'">Go next</a></p>';

die($fc);

// 
// �������, ��������� ����� �� ��������� �� �������, ����� ������
// �� �� ������� ��� ���� conf_database.php.
//
function create_conf_database(){
global $idir, $ddir;
include_once($idir.'lib/o_form.php');
// ��� ���� conf_database.php ���� ����������
if (file_exists($ddir.'conf_database.php')){
  // ��� ���� � ���������� �� �� ��������
  if (isset($_GET['continue'])&&($_GET['continue']=='yes')) { return; }
  // ������� �� ����� �� ������������
/*  $f = new HTMLForm('pform'); $f->astable = false;
  $i = new FormInput('','continue','hidden','yes'); $f->add_input($i);
  $i = new FormInput('Click the button to ','','submit','continue'); $f->add_input($i);*/
  echo '<p>File '.$ddir.'<strong>conf_database.php</strong>'.' exists.</p>
<p><a href="'.$_SERVER['PHP_SELF'].'?continue=yes">Click here</a> to continue the instalation.</p>
<p>Or remove it to start a new instalation.</p>';
  die;
}
$f = new HTMLForm('pform');
$i = new FormInput('Database','database','text'); $f->add_input($i);
$i = new FormInput('User','user','text'); $f->add_input($i);
$i = new FormInput('Password','password','text'); $f->add_input($i);
$i = new FormInput('Table mane prefix','prefix','text'); $f->add_input($i);
$i = new FormInput('','','button','Save'); 
$i -> set_event('onclick','ifNotEmpty_pform();');
$f->add_input($i);
if (count($_POST)) process_data();
else { echo '<h1>Create the tables in the database</h1>'.$f->html(); die; }
}

//
// ������� �� ��������� �� ����������� � $_POST �����,
// ����� ������� conf_database.php �����.
//
function process_data(){
global $idir;
// ��������� �� ������ �����, ��� �� ����������
try{
   $db_link = mysqli_connect("localhost",$_POST['user'],$_POST['password']);
} catch (Exception $e){ die('Invalid credentials. Try again.');}
if (!$db_link) die("Failed to connect to MySQL: " . mysqli_connect_error());
$q = "CREATE DATABASE IF NOT EXISTS `".$_POST['database']."` COLLATE=utf8_unicode_ci;";
if (!mysqli_query($db_link,$q)) die("Error creating database: " . mysqli_error($db_link));
// ���������� �� conf_database.php �����
$s = '<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

This file is generated by _install.php script
*/

$database ="'.$_POST['database'].'";
$user     ="'.$_POST['user'].'";
$password ="'.$_POST['password'].'";
$tn_prefix = "'.$_POST['prefix'].'";
$colation = "utf8";

?>
';
// ��� ������������ � ��������� �� ����� - ���������
if (!is_writable($idir)) {
  echo "<p>Can't write to file ".$idir.'<strong>conf_database.php</strong>'.'</p>
<p>Please, create it manually with the following content:</p>
';
  echo '<textarea rows="20" cols="100">'.htmlentities($s).'</textarea>';
  die;
}
// ��������� �� �����
$f = fopen($idir.'conf_database.php','w');
if ($f){
  fwrite($f,$s);
  fclose($f);
}
}

?>

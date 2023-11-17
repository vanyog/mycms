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
  if (isset($_GET['m'])) $p = __DIR__.'mod/'.$_GET['m']."/tables.sql";
  if (!file_exists($p)) die("$p file not found");
}

$fc = file_get_contents($p);

$fc = str_replace('DROP TABLE IF EXISTS `', "DROP TABLE IF EXISTS `$tn_prefix", $fc);
$fc = str_replace('CREATE TABLE `', "CREATE TABLE `$tn_prefix", $fc);
$fc = str_replace('ALTER TABLE `', "ALTER TABLE `$tn_prefix", $fc);
$fc = str_replace('INSERT INTO `', "INSERT INTO `$tn_prefix", $fc);

$fc = preg_replace('/\'host_local\', \'.*\'/', '\'host_local\', \'localhost\'', $fc);
$fc = preg_replace('/\'host_web\', \'.*\'/', '\'host_web\', \'mysite.org\'', $fc);

mysqli_multi_query($db_link, $fc);

echo '<p>All done.</p>

<p><a href="'.dirname($_SERVER['PHP_SELF']).
'/">Go to manage</a> folder or go to <a href="'.dirname(dirname($_SERVER['PHP_SELF'])).
'/index.php">Home page</a>.</p>';

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
  echo '<h1>Creation of tables in the database</h1>
<p>File '.$ddir.'<strong>conf_database.php</strong>'.' exists.</p>
<p><a href="'.$_SERVER['PHP_SELF'].
'?continue=yes">Click here</a> to continue with creation of data tables. 
If there is data in the tables, it will be deleted and replaced with new data.</p>
<p>Or remove conf_database.php file to start a new instalation.</p>';
  die;
}
$f = new HTMLForm('pform');
$i = new FormInput('Host','host','text','localhost');     $f->add_input($i);
$i = new FormInput('Database','database','text','VanyoG CMS'); $f->add_input($i);
$i = new FormInput('User','user','text');                 $f->add_input($i);
$i = new FormInput('Password','password','text');         $f->add_input($i);
$i = new FormInput('Table mane prefix','prefix','text');
     $i -> nocheck = true; 
     $f->add_input($i);
$i = new FormInput('','','button','Save'); 
     $i -> set_event('onclick','ifNotEmpty_pform();');
     $f->add_input($i);
if (count($_POST)) process_data();
else { echo "<h1>Creation of conf_database.php file</h1>\n".$f->html(); die; }
}

//
// ������� �� ��������� �� ����������� � $_POST �����,
// ����� ������� conf_database.php �����.
//
function process_data(){
global $idir;
// ��������� �� ������ �����, ��� �� ����������
try{ //print_r($_POST); die;
   $db_link = mysqli_connect($_POST['host'],$_POST['user'],$_POST['password']);
} catch (Exception $e){ echo('<p>Can\'t connect to database.</p>'); }
if (empty($db_link)) echo("<p>Failed to connect to MySQL: " . mysqli_connect_error().'</p>');
else {
  $q = "CREATE DATABASE IF NOT EXISTS `".$_POST['database']."` COLLATE=utf8_unicode_ci;";
  if (!mysqli_query($db_link,$q)) 
    echo("<p>Error creating database: " .mysqli_error($db_link).'</p>');
}
// ���������� �� conf_database.php �����
$s = '<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

This file is generated by _install.php script
*/

$database ="'.$_POST['database'].'";
$host     ="'.$_POST['host'].'";
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

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

// Страница за качване на файл на сайта

if (!isset($_GET['fid'])) die("No upload id");
if (!isset($_GET['fn' ])) die("No upload name");

$idir = dirname(dirname(dirname(__FILE__))).'/';

include($idir.'lib/translation.php');
include($idir.'lib/o_form.php');
include_once($mod_apth.'user/f_user.php');

// Проверка дали има влязъл потребител
user('new');

// Номер на интернет страницата.
$pid = 1*$_GET['pid'];

// Номер на записа на файла в таблица $tn_prefix.'files'.
// Ако е 0 ще бъде създаден нов запис.
$fid = 1*$_GET['fid'];

// Име на файла от интернет страницата.
$fn = addslashes($_GET['fn']);

// Надпис върху хипервръзката на файла
$ftx = '';

// Четене на данните за файла от таблица $tn_prefix.'files'.
$fd = db_select_1('*','files',"`pid`='$pid' AND `name`='$fn'");

if ($fd) $ftx = $fd['text'];

// Ако са изпратени данни се обработват.
if (count($_POST) && !isset($_POST['password'])) process_data();
// Ако не са изпратени данни се показва форма за редактиране.
else { 
  show_form(); 
  include($idir.'lib/build_page.php');
}

// ------ Функции -------

//
// Показване форма за качване на файл.
// 
function show_form(){
global $ftx, $page_content;

$page_content = '<h1>'.translate('uploadfile_upladpagetitle')."</h1>\n";

$uf = new HTMLForm('uploadform');
$tx = new FormInput(translate('uploadfile_linktext'), 'text', 'text', $ftx);  $tx->size = 80;  $uf->add_input( $tx );
$fl = new FormInput(translate('uploadfile_file'), 'file', 'file');            $fl->size = 70;  $uf->add_input($fl);
$uf->add_input(new FormInput('', '', 'submit', translate('uploadfile_submit')) );

$page_content .= $uf->html();
}

//
// Обработка на изпратени данни.
//
function process_data(){
global $pid, $fid, $fd, $tn_prefix, $fn, $db_link, $pth;
// Път до директория за качване на файлове. Ако не е зададена друга, съвпада с директорията на модула.
$fld = current_pth(__FILE__);
$fld = $_SERVER['DOCUMENT_ROOT'].stored_value('uploadfile_dir',$fld); //echo "$fld<br>"; die;
// Път до качения файл
$fln = $fld.$_FILES['file']['name'];
// Четене на данни от запис за файл със същото име.
$dt = db_select_1('*','files',"`filename`='$fln'");
// Ако има такъв запис - прекратяване със съобщение за грешка 
if ($dt && ($dt['ID']!=$fid)){
  header("Content-Type: text/html; charset=windows-1251");
  die(translate('uploadfile_fileinuse'));
}
// Ако има друг файл на сървъра за този запис, файлът се изтрива.
if ($fd && file_exists($fd['filename'])) unlink($fd['filename']);
// Проверка дали има файл на сървъра със същото име, който не се отнася за същия запис.
if (file_exists($fln) && (!$dt || ($dt['ID']!=$fid)) ){
  header("Content-Type: text/html; charset=windows-1251");
  die(translate('uploadfile_fileexists'));
}
// Преместване на качения файл в директория за качване на файлове
if (!move_uploaded_file($_FILES['file']['tmp_name'], $fln)) die('Do not uploaded');
// Записване на денни в базата
$w = '';
if ($fd) { $q = "UPDATE `"; $w = "WHERE `ID`=$fid"; }
else $q = "INSERT INTO `";
$q .= $tn_prefix."files` SET `pid`='$pid', `name`='$fn', `filename`='$fln', `text`='".addslashes($_POST['text'])."' $w;";
mysql_query($q,$db_link);
//print_r($q); die;
header("Location: $pth"."index.php?pid=$pid");
}

?>
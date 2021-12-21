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

// Страница за качване на файл в сайта

if (!isset($_GET['fid'])) die("No upload id");
if (!isset($_GET['fn' ])) die("No upload name");

include("conf_uploadfile.php"); 
include($idir.'lib/translation.php');
include($idir.'lib/o_form.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir."lib/f_page_cache.php");
include_once('lib.php');

// Проверка дали има влязъл потребител
if (!in_edit_mode()) user('new');

// Номер на интернет страницата.
$pid = 1*$_GET['pid'];

// Номер на записа на файла в таблица $tn_prefix.'files'.
// Ако е 0 ще бъде създаден нов запис.
$fid = 1*$_GET['fid'];

// Име на файла от интернет страницата.
$fn = addslashes($_GET['fn']);

// Надпис върху хипервръзката на файла
$ftx = '';
// Дата на показване
$tshow = '0000-01-01 00:00:00';
// Дата на скриване
$thide = '0000-01-01 00:00:00';

// Четене на данните за файла от таблица $tn_prefix.'files'.
$fd = db_select_1('*','files',"`pid`='$pid' AND `name`='$fn'");

if ($fd){
  $ftx = htmlspecialchars(stripslashes($fd['text']), ENT_COMPAT, 'cp1251');
  $tshow = $fd['date_time_3'];
  $thide = $fd['date_time_4'];
}

// Ако са изпратени данни, те се обработват и се извършва връщане към предишната страница.
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
global $ftx, $tshow, $thide, $page_content, $fd;

$f = uploadfile_href($fd['filename']);

$page_content = '<h1>'.translate('uploadfile_upladpagetitle')."</h1>\n".
'<p>'.$fd['name'].' <a href="'.$f.'" target="_blank">'.basename($fd['filename'])."</a></p>\n";

$uf = new HTMLForm('uploadform');

$uf->add_input( new FormInput('', 'referer', 'hidden', $_SERVER['HTTP_REFERER']) );
$uf->add_input( new FormInput(translate('uploadfile_timeshow'), 'timeshow', 'text', $tshow) );
$uf->add_input( new FormInput(translate('uploadfile_timehide'), 'timehide', 'text', $thide) );

$tx = new FormInput(translate('uploadfile_linktext'), 'text', 'text', $ftx);
$tx->size = 80;
$uf->add_input( $tx );

$fl = new FormInput(translate('uploadfile_file'), 'file', 'file');
$fl->size = 70;
$uf->add_input($fl);

$uf->add_input(new FormInput('', '', 'submit', translate('uploadfile_submit')) );

$page_content .= $uf->html();
}

//
// Обработка на изпратени данни.
//
function process_data(){
global $pid, $fid, $fd, $tn_prefix, $fn, $db_link, $pth, $site_encoding;

// Път до директория за качване на файлове. Ако не е зададена друга, съвпада с директорията на модула.
$fld = current_pth(__FILE__);
$fld = $_SERVER['DOCUMENT_ROOT'].stored_value('uploadfile_dir',$fld);
if(substr($fld,-1)!='/') $fld .= '/';

if(!file_exists($fld)) die("Directory '$fld' do not exist.");
if (!is_writable($fld)) die("Directory '$fld' is not writable.");

// Път до качения файл
$fln = $fld.$_FILES['file']['name'];

// Четене на данни от запис за файл със същото име.
$dt = db_select_1('*','files',"`filename`='$fln'");

// Ако има такъв запис - прекратяване със съобщение за грешка 
if ($dt && ($dt['ID']!=$fid)){
  header("Content-Type: text/html; charset=$site_encoding");
  die(translate('uploadfile_fileinuse'));
}
// Ако има друг файл на сървъра за този запис, файлът се изтрива.
if ($fd && // има данни в таблица 'files'
    $_FILES['file']['tmp_name'] // има успешно качен файл
){
   // document_root деректорията на друг сървър
   $or = stored_value('uploadfile_otherroot');
   // Път до файла на този сървър
   $thfn = $fd['filename'];
   if($or && (substr($thfn,0,strlen($or))==$or)) $thfn = $_SERVER['DOCUMENT_ROOT'].substr($thfn,strlen($or));
   if( file_exists($thfn) ) unlink($thfn);
}

// Проверка дали има файл на сървъра със същото име, който вероятно не се отнася за същия запис.
if (($fln!=$fld) && file_exists($fln) && (!$dt || ($dt['ID']!=$fid)) ){
  header("Content-Type: text/html; charset=$site_encoding");
  die(translate('uploadfile_fileexists'));
}

// Преместване на качения файл в директория за качване на файлове
if ($_FILES['file']['tmp_name'] && !move_uploaded_file($_FILES['file']['tmp_name'], $fln)) die('Do not uploaded');

// Записване на денни в базата
$w = '';
if ($fd) {
  $q = "UPDATE `$tn_prefix"."files` SET `date_time_2`=NOW(), ";
  $w = " WHERE `ID`=$fid";
}
else $q = "INSERT INTO `$tn_prefix"."files` SET `date_time_1`=NOW(), `date_time_2`=NOW(), ";
$q .= "`date_time_3`='".$_POST['timeshow'].
      "', `date_time_4`='".$_POST['timehide'].
      "', `pid`='$pid', `name`='$fn', ";
if ($_FILES['file']['tmp_name']) $q .= "`filename`='$fln', ";
$q .= "`text`='".addslashes($_POST['text'])."'$w;";
mysqli_query($db_link,$q);
//print_r($q); die;
purge_page_cache($_POST['referer']);
header("Location: ".$_POST['referer']);
}

?>

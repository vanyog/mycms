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

// ��������� �� ����� �� ����� ����.

if (!isset($_GET['fid'])) die("No upload id");

include("conf_uploadfile.php");
include($idir.'lib/translation.php');
include($idir.'mod/user/f_user.php');

// ����� ��������! ������ �� �� �������� ������� �� �����������!
if (!in_edit_mode()) user();

// ����� �� ������ �� �����.
$fid = 1*$_GET['fid'];

// ������ �� ������� �� ����� �� ������� $tn_prefix.'files'.
$fd = db_select_1('*','files',"`ID`='$fid'");

if (!$fd) die(translate('uploadfile_idnotexists'));

$afn = $fd['filename'];

// ��� ����� � ��������� �� ���� ������
$n = stored_value('uploadfile_otherroot');
if ($n){
  $l1 = strlen($n);
  $l2 = strlen($afn);
  if (substr($afn, 0, $l1)==$n) $afn = $_SERVER['DOCUMENT_ROOT'].substr($afn, $l1, $l2-$l1);
}

if (!file_exists($afn)){ // ��������� �� ������, ��� ������ �� ����������.
  header("Content-Type: text/html; charset=windows-1251");
  die(translate('uploadfile_filenotexists'));
}

// ��������� �� ����� �� �������
if (unlink($afn)){

  // ��������� ��� ��������� �� ������ �� ������ �����
  if (stored_value('uploadfile_deletefileonly')=='true')
    $q = "UPDATE `$tn_prefix"."files` SET `filename`='' WHERE `ID`=$fid;";
  else
    $q = "DELETE FROM `$tn_prefix"."files` WHERE `ID`=$fid;";
  mysqli_query($db_link,$q);
  
  // ���������� ��� ���������� � ���� ��� �����
  header("Location: ".$_SERVER['HTTP_REFERER']);
}
else{ // ��������� �� ������, ��� ������ �� � ������ �������.
  header("Content-Type: text/html; charset=windows-1251");
  die(translate('uploadfile_deleteerror'));
}



?>
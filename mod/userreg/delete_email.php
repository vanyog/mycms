<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2020  Vanyo Georgiev <info@vanyog.com>

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


// ��������� �� ���������� ���� ��������� �� ���, ����� �� SHA2(`email`,224)

if(!isset($_GET['code'])) die('Nothing to do.');

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_1.php');
include_once($idir.'lib/f_db_delete_from.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_set_self_query_var.php');

// ������ �� ������ �� ����������� � ��������� �����
$d = db_select_1('*', 'users', "SHA2(`email`,224)='".addslashes($_GET['code'])."'");

// ��� �� �� ������ ����� - �����
if(!$d) die('Incorrect code.');

// ���� �� �����������. �� ���� ���� �� ������� ����������
$language = array_search($d['language'], $languages);

// �������� �� ����������
$page_title = translate('userreg_delEmailTitle');

$page_content = "<h1>$page_title</h1>
<p>".$d['email']."</p>\n";

// ��� ������������ ��� ��� � ������
if($d['username'] && $d['password']){
  $lp = stored_value('userreg_login_'.$d['type']);
  // �� ������� ������, �� ������ �� ����� � ������� ��
  $page_content .= '<p>'.translate('userreg_delEmailLogin')." <a href=\"$lp&lang=$language\">$lp</a></p>\n";
}
else{ // �����:
  // ��� ������������ � ������� �����, �� ����������� �����������
  if(isset($_GET['confirm']) && ($_GET['confirm']=='yes')) {
    // ���������
    db_delete_from('users', $d['ID']);
    // ��������� �� ������, �� �������� � ������
    $page_content .= '<p><b>'.translate('userreg_delEmailDone')."</b></p>\n";
    // � ���� "������������" ��� ��������� �������� �� �����
    $page_content .= '<p><a href="/">'.translate('userreg_delEmailContinue')."</a></p>\n";
  }
  else {
    // ��������� �� ���� �� ������������� �� �����������
    $page_content .= '<p><a href="'.set_self_query_var('confirm','yes').'">'.translate('userreg_delEmailYes')."</a></p>\n";
    // � ���� "�����", ����� �������� ��� ��������� �������� �� �����.
    $page_content .= '<p><a href="/">'.translate('userreg_delEmailNo')."</a></p>\n";
  }
}

include_once($idir.'lib/build_page.php');

?>

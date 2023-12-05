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

// ��������� �� ������� �� ������

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'mod/usermenu/f_usermenu.php');
include_once($idir.'lib/f_set_self_query_var.php');

// �������� �� ������� �� ������� ����������
usermenu(true);

// ��� ���� ����� �� ����� conference - ����
if(empty($can_manage['conference'])) die("Not permitted for current user");

// ��� �� �������������
$utype = stored_value('conference_usertype', 'basa2019');

$page_header = '<style>
body { padding:1em; }
</style>
';

$page_title = encode('������� �� ������');

$page_content = "<h1>$page_title</h1>
<p><a href=\"".set_self_query_var('list', 'alloftype').'">'.encode('������ ����������� �� ���')." $utype</a></p>
<p><a href=\"".set_self_query_var('list', 'allwithabstracts').'">'.encode('������ � ���������� ��������')."</a></p>".
'<textarea style="width:500px;">'.rezult().'</textarea>';

include_once($idir.'lib/build_page.php');

// ��������� �� ��������� �����

function rezult(){
if(!isset($_GET['list'])) return encode('�������� ������');
if(!function_exists($_GET['list'])) return encode('�������� �� ����������');
else return $_GET['list']();
}

function alloftype(){
return encode("��������� �� � ������������");
}

function allwithabstracts(){
global $utype;
// ������ �� ������������� - ��������� � ���������� ��������
$a = db_select_m('user_id', 'proceedings', "`utype`='$utype' AND `approved_a`=1 GROUP BY `user_id`");
// SQL ������
$q = '';
foreach($a as $e){
  if($q) $q .= ' OR ';
  $q .= '`ID`='.$e['user_id'];
}
// ������ �� �������������
$es = db_select_m('email', 'users', "$q ORDER BY `email` ASC");
$rz = '';
foreach($es as $e) $rz .= $e['email']."\n";
return $rz;
}

?>
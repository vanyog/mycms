<?php
/*
VanyoG CMS - a simple Content Management System
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

// ����������� �� ������ �� ������� 'actualno'

if (!isset($_GET['id'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

//$no_small_image = true;

include('f_actualno.php');
include_once($idir."lib/f_encode.php");
include($idir."mod/usermenu/f_usermenu.php");
//include_once($idir."lib/translation.php");
//include_once($idir."lib/f_edit_record_form.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_page_cache.php");
include_once($idir."/lib/f_mod_picker.php");

// ����� �� ����������, �� ����� �� �������� ��������
// $page_id = 87;

// ����� �� ����������
// $page_data = db_select_1('*', 'pages', "`ID`=$page_id");

// �������� �� ������� �� �����������
usermenu(true);

// ���� ��� ���� ����� �� ���������
if (!$can_edit && !show_adm_links()) die('You have no permission to edit news');

// ����� �� ������ �� ������� actualno
$i = 1*$_GET['id'];

$d = db_select_1('*', 'actualno', "`ID`=$i");

if($_GET['type']=='News') $np = '99';
else $np = '106';

$page_header = '<script>
function set_abstract(){
var f = document.forms["editrecord_form"].Abstract;
f.value = "'.str_replace("\n", '\n"+'."\n\"", addslashes(actualno_abstract($d, false, $np))).'";
}
</script>'."\n";

$cp = array(
 'ID'=>$i,
 'type'=>encode('������/�������:'),
 'lang'=>encode('E���:'),
 'Link'=>encode('����:'),
 'url'=>'URL:',
 'Title'=>encode('��������:'),
 'Abstract'=>encode('������:'),
 'Content'=>encode('����������:'),
 'Active'=>encode('���������:'),
 'Date'=>encode('���� � ���:'),
 'StartDate'=>encode('�� �� �� ������� �����:')
);

if ($i==0) $page_content = encode("<h1>��������� �� ������</h1>\n");
else $page_content = encode('<h1>����������� �� ������</h1>
<input type="button" value="����������� ������" onclick="set_abstract();">
');
$page_content .= mod_picker();

// ����������� �� ��������� �����
if (count($_POST)){
  if ($i==0) db_insert_1($_POST, 'actualno');
  else process_record($_POST, 'actualno');
  purge_page_cache($_SESSION['http_referer']);
  header('Location: '.$_SESSION['http_referer']);
}
else if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];

// ����� �� ����������� �� ������
$page_content .= edit_record_form($cp, 'actualno');

$pt = $_SESSION['http_referer'];

$page_content .= '<p><a href="'.$pt.'">'.translate('usermenu_back').'</a></p>';

include($idir."lib/build_page.php");

?>

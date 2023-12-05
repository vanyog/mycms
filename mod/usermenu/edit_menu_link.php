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

// ����������� �� ���� �� ����, ���� �������� ����� ����� * ��� ���� ���� � ����� �� �����������

error_reporting(E_ALL); ini_set('display_errors',1);

if (!isset($_GET['pid']) || !isset($_GET['id'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir."conf_paths.php");
include("f_usermenu.php");
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/f_db_update_where.php");
//include_once($idir."lib/translation.php");
//include_once($idir."lib/f_edit_record_form.php");
include_once($idir."lib/f_db_insert_or_1.php");
//include_once($idir."lib/f_page_cache.php");
//include_once($idir."/lib/f_mod_picker.php");

// ����� �� ����������, �� ����� �� �������� �������������.
$page_id = 1*$_GET['pid'];

// �������� �� ������� �� �����������
usermenu(true);

// ���� ��� ���� ����� �� ���������
if (!$can_edit && !show_adm_links()) die('You have no permission to edit this text');

// ����� �� ����� � ������� $tn_prefix.'menu_items'.
$id = 1*$_GET['id'];

$rz = '';
// ����������� �� ����������� � $_POST �����
if (count($_POST)) $rz = process_data();
else {
  if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['http_referer'] = $_SERVER['HTTP_REFERER'];
  else $_SESSION['http_referer'] = $main_index.'?pid='.$page_id;
}

// ����� �� �����
$m = db_select_1('*', 'menu_items', "`ID`=$id");

// ����� ����� �����
$tx = db_table_field('text', 'content', "`name`='".$m['name']."' AND `language`='$language'" );

// ����� �� �����������
$f = new HTMLform('mform');
$f -> add_input( new FormInput('','ID','hidden',$m['ID']) );
$f -> add_input( new FormInput(translate('usermenu_menugroup'),'group','text',$m['group']) );
$f -> add_input( new FormInput(translate('usermenu_menupos'),'place','text',$m['place']) );
$f -> add_input( new FormInput(translate('usermenu_menutext'),'text','text',str_replace('"','&quot;', $tx)) );
$f -> add_input( new FormInput(translate('usermenu_menulinkdb'),'link','text',$m['link']) );
if(isset($m['attr'])) 
   $f -> add_input( new FormInput(translate('usermenu_menuattr'),'attr','text',str_replace('"','&quot;', $m['attr'])) );
$f -> add_input( new FormInput('','','submit',translate('saveData')) );

$page_title = translate('usermenu_editmenu');
$page_content = '<h1>'.$page_title.'</h1>
<p>'.$m['name'].'</p>
'.$rz.'
'.$f->html().'
<p><a href="'.$_SESSION['http_referer'].'">'.translate('usermenu_back').'</a></p>';

include($idir."lib/build_page.php");

function process_data(){
global $language;
$d['text'] = addslashes($_POST['text']);
$d['date_time_2'] = 'NOW()';
$d['language'] = $language;
$i = db_table_field('name', 'menu_items', "`ID`=".(1*$_POST['ID']) );
$d['name'] = $i;
db_insert_or_1($d, 'content', "`name`='$i' AND `language`='$language'", 'b', false);
unset($_POST['text']);
db_update_record($_POST, 'menu_items');
if(!session_id()) session_start();
header('Location: '.(isset($_SESSION['http_referer']) ? $_SESSION['http_referer'] : '') );
die;
}

?>

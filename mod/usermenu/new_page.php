<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// ��������� �� ���� �������� �� �������� $_GET['p']

if (!isset($_GET['p'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."conf_paths.php");
include_once($idir."lib/translation.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_insert_m.php");
include_once($idir."lib/o_form.php");

// ����� �� ����������, �� ����� � ��������� ������ �� ���� ��������
$page_id = 1*$_GET['p'];

// ����� �� ����������, �� ����� � ��������� ������ �� ���� ��������
$page_data = db_select_1('*','pages',"`ID`=$page_id");

// ����������� ������� �� �����������
$tx = usermenu(true);

// ��� ������������ ���� ����� �� ������� ���� �������� - ����.
if (!$can_create) echo die("Your have no permission to create new page here.");

// ��������� �� ��������� �����
if (count($_POST)) process_data();

// �������  �� ������ �������� � ������ - �� ������������ ���-������.
$pz = db_table_field('MAX(`place`)', 'menu_items', "1")+10;

// ��������� �� ����� �� ��������� �� ����� �� ���� �������� 
$pf = new HTMLForm('new_page_fotm');

$pf->add_input( new FormInput(translate('usermenu_newmenu'), 'newmenu', 'checkbox'));

$ti = new FormSelect(translate('usermenu_language'), 'lang', $languages, $language);
$ti->values = 'k';
$pf->add_input( $ti );

$pf->add_input(new FormInput(translate('usermenu_menupos'), 'place', 'text', $pz) );

$ti = new FormInput(translate('usermenu_linktext'), 'linktext', 'text');
$ti->size = '50';
$pf->add_input( $ti );

$ti = new FormInput(translate('usermenu_addtomenu'), 'addtomenu', 'checkbox');
$ti->checked = "checked";
$pf->add_input( $ti );

$ti = new FormInput(translate('usermenu_newpagetitle'), 'title', 'text');
$ti->size = '100';
$pf->add_input( $ti );

$pf->add_input(new FormTextArea(translate('usermenu_newpagecontent'), 'content', 100, 30) );

$pf->add_input( new FormInput('','','submit',translate('usermenu_newpagesubmit')) );

$page_content = '<h1>'.translate('usermenu_createnewpage').'</h1>'.$pf->html();
$page_header .= '<style><!--
th { text-align: right; vertical-align:top; }
--></style>';

include($idir."lib/build_page.php");

//
// ��������� �� ��������� �����
//
function process_data(){
global $pth, $page_data;//  print_r($_POST); die;

// ���������� �� m: �� ������� �������
$_POST['content'] = str_replace('<m:', '<', $_POST['content']);
$_POST['content'] = str_replace('</m:', '</', $_POST['content']);
$_POST['content'] = str_replace(' xmlns:m="http://www.w3.org/1998/Math/MathML"', '', $_POST['content']);

// ���� �� ������� ��� ������
$newmenu = isset($_POST['newmenu']) && ($_POST['newmenu']=='on');

// ���� �� �� ������� ���� � ������
$addtomenu = isset($_POST['addtomenu']) && ($_POST['addtomenu']=='on');

// ������������ ����� �� ������ ��������
$pi = db_table_field('MAX(`ID`)', 'pages', '1')+1;

// ����� �� ������ �� ������ ��������
$mg1 = $page_data['menu_group']; // �� ������� ����
$mg2 = $mg1; // �� ������ ����, ��� �� ������� ��� ������
if ($newmenu){ // ������� �� ������ ���� - � 1 ��-����� �� ���-�������, ������� ����� �� ����
// ����� � ������� 'pages' ���� � � ������� 'menu_items'
  $mg2 = db_table_field('MAX(`menu_group`)', 'pages', '1')+1;
  $mg3 = db_table_field('MAX(`group`)', 'menu_items', '1')+1;
  if ($mg3>$mg2) $mg2 = $mg3;
}

// ������������ ����� �� ����� ����
$mi = db_table_field('MAX(`ID`)', 'menu_items', '1')+1;

// ����� �� ������� 'pages'
$d1 = array(
  'menu_group'=>$mg2,
  'title'=>"p$pi"."_title",
  'content'=>"p$pi"."_content",
  'template_id'=>$page_data['template_id'],
);
// ��������� � ���������
$pi = db_insert_1($d1,'pages');

// ����� �� ������ � ������� 'menu_items'
$d2 = array (
  'place'=>1*$_POST['place'], 
  'group'=>$mg2, 
  'name'=>"p$mi"."_link",
  'link'=>$pi
);
// ��������� � ���������
if ($addtomenu || $newmenu) $pp = db_insert_1($d2,'menu_items');

// ��� �� ������� ��� ������ �� ������� ��������� ��� ���������� � � ������� ����
if ($newmenu){
  // ����� �� ������ � ������� 'menu_items'
  $d2 = array (
    'place'=>1*$_POST['place'] - 5, 
    'group'=>$mg1, 
    'name'=>"m$mg2"."_link",
    'link'=>$pi
  );
  // ��������� � ���������, ��� � ������� �� �� ������ ���� � ������ �� �������� ��������
  if ($addtomenu) $pp = db_insert_1($d2,'menu_items');
  // ����� �� ������� 'menu_tree'
  $dt = array(
    'group'=>$mg2,
    'parent'=>$mg1,
    'index_page'=>$pi
  );
 $pn = db_insert_1($dt,'menu_tree');
}

// ����� �� �������� � ������� 'content'
$d3 = array (
// �������� �� ����������
array('name'=>$d1['title'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['title'])
      ),
// ���������� �� ����������
array('name'=>$d1['content'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['content'])
      )
);
// ������� ����� ������� � ������
if (trim($_POST['linktext']))
  // � ������ �� ������ �������� 
  $d3[] = array('name'=>"p$mi"."_link",
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['linktext'])
  );
  // � ������ �� �������� ��������, ��� � ������� �� �� �����
  if ($addtomenu) $d3[] = array('name'=>"m$mg2"."_link",
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['linktext'])
  );
// ��������� � ���������
db_insert_m($d3,'content');

$l = 'Location: '.$pth.'index.php?pid='.$pi;
header($l); 
}

?>

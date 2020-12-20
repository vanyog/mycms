<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2019 Vanyo Georgiev <info@vanyog.com>

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
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/o_form.php");

// ����� �� ����������, �� ����� � ��������� ������ �� ���� ��������
$page_id = 1*$_GET['p'];

// ����� �� ����������, �� ����� � ��������� ������ �� ���� ��������
$page_data = db_select_1('*','pages',"`ID`=$page_id");

// ����������� ������� �� �����������
$tx = usermenu(true);

// ��� ������������ ���� ����� �� ������� ���� �������� - ����.
if (!$can_create) die("Your have no permission to create new page here.");

// ���������� �� ����������, �� ����� � ��������� ��������
$cr = db_select_1('ID,text', 'content',"`name`='".$page_data['content']."' AND `language`='$language'",'');
$pc = $cr['text'];
//db_table_field('text','content',"`name`='".$page_data['content']."' AND `language`='$language'",'');
if(empty($pc)) die('No page content was read.');

// �������� �� ������� ��������
$m1 = array();
$r1 = preg_match('/<(h.).*?>(.*?)<\/\1(>)/s', $pc, $m1, PREG_OFFSET_CAPTURE);

// �������� � ����� ����� ����� �� ����������
$pt = ''; if(isset($m1[2][0])) $pt = trim( str_replace("\n", " ", $m1[2][0]) );

// �������� �� �������� �������� �� ����� ������� � �������� �� ������� ���������� ����
$tx = '';
$m2 = array();
if(isset($m1[3][1])){
   $r2 = preg_match('/<'.$m1[1][0].'.*?>/s', $pc, $m2, PREG_OFFSET_CAPTURE, $m1[3][1]);
   if(isset($m2[0][1])) $tx = substr($pc, $m1[3][1] + 1, $m2[0][1] - $m1[3][1] - 1);
   else $tx = substr($pc, $m1[3][1] + 1);
}

// ��������� �� ��������� �����
if (count($_POST)) process_data();

// �������  �� ������ �������� � ������ - �� ������������ ���-������.
$pz = INTVAL(db_table_field('MAX(`place`)', 'menu_items', '`group`='.$page_data['menu_group']))
      + 10;

// ��������� �� ����� �� ��������� �� ����� �� ���� �������� 
$pf = new HTMLForm('new_page_fotm');

$pf->add_input( new FormInput(translate('usermenu_newmenu'), 'newmenu', 'checkbox'));

$ti = new FormSelect(translate('usermenu_language'), 'lang', $languages, $language);
$ti->values = 'k';
$pf->add_input( $ti );

$pf->add_input(new FormInput(translate('usermenu_menupos'), 'place', 'text', $pz) );

$ti = new FormInput(translate('usermenu_linktext'), 'linktext', 'text', $pt);
$ti->size = '50';
$pf->add_input( $ti );

$ti = new FormInput(translate('usermenu_addtomenu'), 'addtomenu', 'checkbox');
$ti->checked = "checked";
$pf->add_input( $ti );

$ti = new FormInput(translate('usermenu_newpagetitle'), 'title', 'text', $pt);
$ti->size = '100';
$pf->add_input( $ti );

$pf->add_input(new FormTextArea(translate('usermenu_newpagecontent'), 'content', 100, 30, $tx) );

$pf->add_input( new FormInput('','','submit',translate('usermenu_newpagesubmit')) );

$page_content = '<h1>'.translate('usermenu_newpagefrh').'</h1>'.$pf->html();
$page_header .= '<style><!--
th { text-align: right; vertical-align:top; }
--></style>';

include($idir."lib/build_page.php");

//
// ��������� �� ��������� �����
//
function process_data(){
global $pth, $pc, $m1, $m2, $cr, $page_id;
// ��������� �� ������ ��������
$pi = create_new_page($_POST);
// ��������� �� ������������ �� ���������� ��������
if(isset($m1[3][1])){
   if(isset($m2[0][1])) $tx = substr_replace($pc, '', $m1[0][1], $m2[0][1] - $m1[0][1] - 1);
   else $tx = substr_replace($pc, '', $m1[0][1]);
}
$cr['date_time_2'] = 'NOW()';
$cr['text']=$tx;
db_update_record($cr, 'content', false);
//die; //(print_r($cr,true));
// ������� ��� ���������� ��������
$l = 'Location: '.$pth.'index.php?pid='.$page_id;
header($l);
}

//
// ������� �� ��������� �� ���� �������� �� ����� �� ����� $data
//
function create_new_page($data){
global $pth, $page_data;

// ���������� �� m: �� ������� �������
$data['content'] = str_replace('<m:', '<', $data['content']);
$data['content'] = str_replace('</m:', '</', $data['content']);
$data['content'] = str_replace(' xmlns:m="http://www.w3.org/1998/Math/MathML"', '', $data['content']);

// ���� �� ������� ��� ������
$newmenu = isset($data['newmenu']) && ($data['newmenu']=='on');

// ���� �� �� ������� ���� � ������
$addtomenu = isset($data['addtomenu']) && ($data['addtomenu']=='on');

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
  'hidden'=>$page_data['hidden']
);
// ��������� � ���������
$pi = db_insert_1($d1,'pages');

// ����� �� ������ � ������� 'menu_items'
$d2 = array (
  'place'=>1*$data['place'],
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
    'place'=>1*$data['place'] - 5,
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
      'language'=>addslashes($data['lang']),
      'text'=>addslashes($data['title'])
      ),
// ���������� �� ����������
array('name'=>$d1['content'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($data['lang']),
      'text'=>addslashes($data['content'])
      )
);
// ������� ����� ������� � ������
if (trim($data['linktext']))
  // � ������ �� ������ �������� 
  $d3[] = array('name'=>"p$mi"."_link",
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($data['lang']),
      'text'=>addslashes($data['linktext'])
  );
  // � ������ �� �������� ��������, ��� � ������� �� �� �����
  if ($addtomenu) $d3[] = array('name'=>"m$mg2"."_link",
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($data['lang']),
      'text'=>addslashes($data['linktext'])
  );
// ��������� � ���������
db_insert_m($d3,'content');

return $pi;
}

?>

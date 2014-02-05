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

// Създаване на нова страница в раздел/меню $_GET['m']

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."conf_paths.php");
include_once($idir."lib/translation.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_insert_m.php");
include_once($idir."lib/o_form.php");

// Номер на страницата, от която е изпратена заявка за нова страница
$page_id = 1*$_GET['p'];

// Данни на страницата, от която е изпратена заявка за нова страница
$page_data = db_select_1('*','pages',"`ID`=$page_id");

// Проверяване правата на потребителя
$tx = usermenu(true);

// Ако потребителят няма право да създава нова страница - край.
if (!$can_create) echo die("Your have no permission to create new page here.");

// Обработка на изпратени данни
if (count($_POST)) process_data();

// Позиция  на новата страница в менюто - по подразбиране най-отдолу.
$pz = db_table_field('MAX(`place`)', 'menu_items', "1")+10;

// Създаване на форма за попълване на данни за нова страница 
$pf = new HTMLForm('new_page_fotm');

$pf->add_input( new FormInput(translate('usermenu_newmenu'), 'newmenu', 'checkbox'));

$ti = new FormSelect(translate('usermenu_language'), 'lang', $languages, $language);
$ti->values = 'k';
$pf->add_input( $ti );

$pf->add_input(new FormInput(translate('usermenu_menupos'), 'place', 'text', $pz) );

$ti = new FormInput(translate('usermenu_linktext'), 'linktext', 'text');
$ti->size = '50';
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
// Обработка на изпратени данни
//
function process_data(){
global $pth, $page_data;//  print_r($_POST); die;

// Дали се създава нов раздел
$newmenu = isset($_POST['newmenu'])&&($_POST['newmenu']=='on');

// Предполагаем номер на новата страница
$pi = db_table_field('MAX(`ID`)', 'pages', '1')+1;

// Номер на менюто на новата страница
$mg1 = $page_data['menu_group']; // На старото меню
$mg2 = $mg1; // На новото меню, ако се създава нов раздел
if ($newmenu){ // Номерът на новото меню - с 1 по-голям от най-големия, зададен номер на меню
// както в таблица 'pages' така и в таблича 'menu_items'
  $mg2 = db_table_field('MAX(`menu_group`)', 'pages', '1')+1;
  $mg3 = db_table_field('MAX(`group`)', 'menu_items', '1')+1;
  if ($mg3>$mg2) $mg2 = $mg3;
}

// Данни за таблица 'pages'
$d1 = array(
  'menu_group'=>$mg2,
  'title'=>"p$pi"."_title",
  'content'=>"p$pi"."_content",
  'template_id'=>$page_data['template_id'],
);
// Записване в таблицата
$pi = db_insert_1($d1,'pages');

// Данни за записа в таблица 'menu_items'
$d2 = array (
  'place'=>1*$_POST['place'], 
  'group'=>$mg2, 
  'name'=>"p$pi"."_link",
  'link'=>$pi
);
// Записване в таблицата
$pp = db_insert_1($d2,'menu_items');

// Ако се създава нов раздел се създава препратка към страницата и в старото меню
if ($newmenu){
  // Данни за записа в таблица 'menu_items'
  $d2 = array (
    'place'=>1*$_POST['place'] - 5, 
    'group'=>$mg1, 
    'name'=>"p$pi"."_link",
    'link'=>$pi
  );
  // Записване в таблицата
 $pp = db_insert_1($d2,'menu_items');
  // Данни за таблица 'menu_tree'
  $dt = array(
    'group'=>$mg2,
    'parent'=>$mg1,
    'index_page'=>$pi
  );
 $pn = db_insert_1($dt,'menu_tree');
}

// Данни за записите в таблица 'content'
$d3 = array (
// Надписа върху линка в менюто
array('name'=>$d2['name'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['linktext'])
      ),
// Заглавие на страницата
array('name'=>$d1['title'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['title'])
      ),
// Съдържание на страницата
array('name'=>$d1['content'],
      'date_time_1'=>'NOW()',
      'date_time_2'=>'NOW()',
      'language'=>addslashes($_POST['lang']),
      'text'=>addslashes($_POST['content'])
      )
);
// Записване в таблицата
db_insert_m($d3,'content');

$l = 'Location: '.$pth.'index.php?pid='.$pi;
header($l); 
}

?>

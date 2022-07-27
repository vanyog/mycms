<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// Линк към следващата, според картата на сайта страница.
// Ако параметърат $dr == 'back' връща линк към предишната страница.
// Ако има настройка 'nextpage_contentID'=1 към адресите на страниците се добавя #стойнотНаНастройката.

include_once($idir.'lib/f_db_select_join_1.php');

function nextpage($dr = ''){
global $page_data, $page_id, $main_index;
$id = $page_id;
if($dr=='back') $dr = '<'; else $dr = '>';
while (true){
  // Данни за следващата страница
  $pd = nextpage_data($page_data['menu_group'], $id, $dr);
  if(!$pd) return '';
  $id = $pd['ID'];
  if( in_edit_mode() || !$pd['hidden'] ) break;
}
$anch = stored_value('nextpage_contentID');
if($anch) $anch = '#'.$anch;
// Заглавие на страницата
$t = '<span>'.translate('nextpage_next'.$dr).'</span><a href="'.$main_index.'?pid='.$pd['ID'].$anch.'">'.strip_tags(translate($pd['title'], false)).'</a>';
return $t;
}

// Данните на следващата/предишната страница

function nextpage_data($gr, $page_id, $dr){
global $tn_prefix;
// Данни за линка към текущата страница в менюто й.
$ld = db_select_1('*', 'menu_items', "`group`='$gr' AND `link`='$page_id'  OR `link` LIKE '%pid=$page_id%'");
if (!$ld) return '';
if($dr=='<') $ord = 'DESC'; else $ord = 'ASC';
// Да се търсят само нескрити страници
$h = "`hidden`=0 AND ";
// Но не и в режим на редактиране
$h = '';
// Данни за следващия линк в менюто
$nl = db_select_select_join_1('a.*,b.hidden', 
        'menu_items', 'pages',  'a.link=b.ID', 
        "$h `group`='$gr' AND `place`$dr".$ld['place']." ORDER BY `place` $ord", false);
// Ако няма такъв се търси следващ линк в родителските менюта
if (!$nl) $nl = nextpage_from_parent($ld, $dr, $ord);
if (!$nl) return '';
// Номер на групата на намерената страница
$gn = db_table_field('menu_group', 'pages', "`ID`='".$nl['link']."'");
//if (($dr=='<')&&($gr!=$gn)) $nl = nextpage_last_child($nl);
// Извличане номера на страницата от поле `link`
$id = $nl['link'];
if(!is_numeric($nl['link'])){
  $a = array();
  $i = preg_match('/pid=(\d+)/', $nl['link'], $a);
  if($i) $id = $a[1];
//  die("$id");
}
// Данни за следващата страница
return db_select_1('*', 'pages', "`ID`='$id'", false );
}

// Следващ линк от родителското меню

function nextpage_from_parent($ld, $dr, $ord){//die(print_r($ld,true));
do {
  // Родителско меню
  $p = db_select_1('*', 'menu_tree', "`group`=".$ld['group']);
  if (!$p) return false;
  // Данни за линка към главната страница на раздела в родителското меню.
  $ld = db_select_1('*', 'menu_items', "`group`='".$p['parent']."' AND `link`=".$p['index_page']);
  if (!$ld) return false;
  // Данни за следващия линк в родителското меню
  $nl = db_select_1('*', 'menu_items', "`group`='".$ld['group']."' AND `place`$dr".$ld['place']." ORDER BY `place` $ord");
} while (!$nl); // die;
return $nl;
}

function nextpage_last_child($nl){
$tr = db_select_1('*', 'menu_tree', "`index_page`='".$nl['link']."'", false);
if($tr){
  $nl = db_select_1('*', 'menu_items', "`group`='".$tr['group']."' ORDER BY `place` DESC");
  if($nl) $nl = nextpage_last_child($nl);
} 
return $nl;
}

?>
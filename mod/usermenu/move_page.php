<?php
/* 
VanyoG CMS - a simple Content Management System
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

// Страница с номер $_GET['p'] се премества в група $_GET['g'].
// Групата, в която се премества трябва да съществува и да има главна страница.

if (!isset($_GET['p'])||!isset($_GET['g'])) die("Insufficient parameters");

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."lib/f_db_table_field.php");
//include_once($idir."conf_paths.php");
//include_once($idir."lib/f_db_insert_or_1.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_update_record.php");
include_once($idir."lib/f_page_cache.php");

// Номер на страницата, която се премества
$p = 1*$_GET['p'];
$page_id = $p;

// Проверяване правата на потребителя да премести страницата
$tx = usermenu(true);
// Ако потребителят няма право да премести страницата - край.
if (!$can_create) echo die("Your have no permission to move this page.");

// Данни за текущата страницата от таблица 'pages'
$pd = db_select_1('`ID`,`menu_group`', 'pages', "`ID`=$p");

// Номер на групата, в която се премества
$g = 1*$_GET['g'];

// Номер на главната страница на групата, в която се премества
$page_id = db_table_field('index_page', 'menu_tree', "`group`=$g");

$newg = false; // Дали е създадена нова група

// Ако не се открие главна страница, групата не съществува и
// настоящата страница става главна страница на нова група, която се създава.
if (!$page_id) {
  $page_id = $p;
  db_insert_1(array('group'=>$g, 'parent'=>$pd['menu_group'], 'index_page'=>$p), 'menu_tree');
  $newg = true;
}

// Проверяване правата на потребителя да създава страници в новата група
$tx = usermenu(true);
// Ако потребителят няма право да създава страницата в новата група - край.
if (!$can_create) echo die("Your have no permission to move pages in group $g.");

// Данни за групата на текущата страница от таблица 'menu_tree'
$td = db_select_1('`ID`,`parent`,`index_page`', 'menu_tree', "`group`=".$pd['menu_group']);

// Ако страницата е главна на групата, се премества цялата група
if ($td['index_page']==$p){

  // Данни за линка към страницата в родителското меню
  $ld = db_select_1('`ID`,`group`', 'menu_items', "`group`=".$td['parent']." AND `link`=$p");

  // Ако има данни се премества линка
  if ($ld){
    $ld['group']=$g;
    db_update_record($ld, 'menu_items');
  }

  // Променя се родителя на групата на страницата с новата група
  unset($td['index_page']);
  $td['parent'] = $g;
  db_update_record($td, 'menu_tree');

}
else { // Ако страницата не е главна на групата, се премества само страницата

  // Данни за линка към страницата в текущото меню
  $ld = db_select_1('`ID`,`group`', 'menu_items', "`group`=".$pd['menu_group']." AND `link`=$p");

  // Задава се нов номер на група в записа на страницата
  $pd['menu_group'] = $g;
  db_update_record($pd, 'pages');

  // Ако другата група не е нова в нея се премества и линка
  if(!$newg){
     $ld['group'] = $g;
     db_update_record($ld, 'menu_items');
  }

}

// Връщане на страницата, която се премества
$p = $main_index.'?pid='.$p;
$q = 'http://'.$_SERVER['HTTP_HOST'].$p;
purge_page_cache($q);
header("Location: $p");

?>

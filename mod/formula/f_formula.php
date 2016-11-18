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

// Модул за автоматично номериране на формули.
// Параметър $a е `ID` на формулата от таблица 'formula'
// Ако непосредствено пред цифрите на номера стои буква 'c' вместо формулата се показва цитиране към нея.
// Ако в таблица 'formula' няма формула със задавеното `ID`, се показва форма за въвеждане на формулата.
// Ако $a е `ID` на формула, показана на друга страница, се показва съобщение, какво ID да се използва за нова формула.

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_db_insert_1.php');
include_once($idir.'lib/f_db_update_record.php');

function formula($a){
// Връщане на цитиране към формулата
if ( strlen($a) && (strtolower($a[0])=='c') )  return formula_ref($a);
global $adm_pth;
// Асоциативен масив с формулите от текущата страница
static $fs = false;
// Ако оща не е съставен се съставя
if (!$fs) $fs = formula_for_page();
static $c = 0; // Брой вече показани на текущата страница формули
$n = count($fs) - $c; // Номер на поредната формула в страницата
// Ако не съществува формула с `ID`=$a за текущата страница
if (!isset($fs[$a])){
  // Ако съществува формула с `ID`=$a, показана на друга страница
  if (db_table_field('ID', 'formula', "`ID`=$a"))
     // Се показва съобщение какво стойност на $a е допустима за текущата страница
     return '<p>'.translate('formula_incorrectID').db_table_field('MAX(`ID`)', 'formula', 1).'</p>';
  else 
     // Ако не съществува формула с `ID`=$a, се показва форма за въвеждане
     return formula_edit_form();
}
// Запазване в БД нов номер на формулата, ако е необходимо
if ($n!=$fs[$a]['number']) db_update_record(array('ID'=>$fs[$a]['ID'], 'number'=>$n), 'formula');
// Линк * за редактиране на формулата
$elk = '';
if (in_edit_mode()) $elk = " <a href=\"$adm_pth/edit_record.php?t=formula&amp;r=$a\">$a*</a>";
// Показване на формулата
$vn = $fs[$a]['page_id'].'.'.$n; // Видим номер на формулата
$rz = '<a name="f'.$vn.'" id="f'.$vn.'"></a>
<div class="math">
<div class="l">('.$vn.')</div>
<div class="r">'.$fs[$a]['markup']."\n".$elk."</div>\n</div>
<div style=\"clear:both\"></div>\n";
$c++;
return $rz;
}

function formula_for_page(){
if (count($_POST)) formula_insert();
global $page_id;
$da = db_select_m('*', 'formula', "`page_id`=$page_id");
$rz = array();
foreach($da as $d) $rz[$d['ID']] = $d;
return $rz;
}

function formula_edit_form(){
$f = new HTMLForm('formula_edit');
$fi = new FormTextArea('', 'markup', 50, 10);
$fi -> ckbutton = '';
$f -> add_input( $fi ); 
$f -> add_input( new FormInput('', '', 'submit', encode('Запазване на формулата')) );
return $f -> html(); 
}

function formula_insert(){
global $page_id;
$_POST['page_id'] = $page_id;
db_insert_1($_POST, 'formula');
}

function formula_ref($a){
global $main_index;
$id = substr($a, 1);
$d = db_select_1('*', 'formula', "`ID`=$id");
$vn = $d['page_id'].'.'.$d['number']; // Видим номер на формулата
$rz = '(<a href="'.$main_index.'?pid='.$d['page_id'].'#f'.$vn.'">'.$d['page_id'].'.'.$d['number'].'</a>)';
return $rz;
}

?>

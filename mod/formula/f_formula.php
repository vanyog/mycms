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

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_db_insert_1.php');

function formula($a){
global $adm_pth;
static $fs = false;
static $c = -1;
if (!$fs) $fs = formula_for_page();
if (!isset($fs[$a])) return formula_edit_form();
$elk = '';
if (in_edit_mode()) $elk = " <a href=\"$adm_pth/edit_record.php?t=formula&amp;r=$a\">*</a>";
$c++;
return '<div class="math">
<div class="l">('.$fs[$a]['page_id'].'.'.(count($fs)-$c).')</div>
<div class="r">'.$fs[$a]['markup']."\n".$elk."</div>\n</div>
<div style=\"clear:both\"></div>\n";
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

?>
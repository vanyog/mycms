<?php
/*
VanyoG CMS - a simple Content Management System
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

// ����� �� ����������� ����������, ��������� � �������� �� �������.
// ��������� $a � `ID` �� ��������� �� ������� 'formula'
// ��� �������������� ���� ������� �� ������ ���� ����� 'c' ������ ��������� �� ������� �������� ��� ���.
// ��� � ������� 'formula' ���� ������� ��� ���������� `ID`, �� ������� ����� �� ��������� �� ���������.
// ��� $a � `ID` �� �������, �������� �� ����� ��������, �� ������� ���������, ����� ID �� �� �������� �� ���� �������.

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_db_insert_1.php');
include_once($idir.'lib/f_db_update_record.php');

global $page_header;
global $hidden_formulas;

$page_header .= '<script>
function formulaOver(ev,i){
var w = document.getElementById("fViewer");
if(!w){
  w = document.createElement("div");
  w.id = "fViewer";
  document.body.appendChild(w);
  w.style.position = "fixed";
  w.style.zIndex = 100;
}
w.style.top = ev.clientY+2+"px";
w.style.left = ev.clientX+2+"px";
w.style.visibility = "visible";
var f = document.getElementById("f"+i);
w.innerHTML = f.innerHTML;
}
function formulaLeave(){
var w = document.getElementById("fViewer");
w.style.visibility = "hidden";
}
</script>
<style>
div.h { display:none; }
</style>'."\n";

function formula($a){
// ������� �� �������� ��� ���������
if ( strlen($a) && (strtolower($a[0])=='c') )  return formula_ref($a);
global $page_id, $adm_pth;
// ����������� ����� � ��������� �� �������� ��������
static $fs = false;
// ��� ��� �� � �������� �� �������
if (!$fs) $fs = formula_for_page($a);
static $c = 0; // ���� ���� �������� �� �������� �������� �������
$n = count($fs) - $c; // ����� �� ��������� ������� � ����������
// ��� �� ���������� ������� � `ID`=$a �� �������� ��������
if (!isset($fs[$a])){
  $fd = db_select_1('*', 'formula', "`ID`=$a");
  // ��� ���������� ������� � `ID`=$a
  if ($fd)
     // �� ������ � ������ �������
     $fs[$fd['ID']] = $fd;
//     return translate('formula_incorrectID')." ".(db_table_field('MAX(`ID`)', 'formula', 1)+1);
  else 
     // ��� �� ���������� ������� � `ID`=$a, �� ������� ����� �� ���������
     return formula_edit_form();
}
// ��������� � �� ��� ����� �� ���������, ��� � ����������
if ( ($page_id==$fs[$a]['page_id']) && ($n!=$fs[$a]['number']) ) db_update_record(array('ID'=>$fs[$a]['ID'], 'number'=>$n), 'formula');
// ���� * �� ����������� �� ���������
$elk = '';
if (in_edit_mode()) $elk = " <a href=\"$adm_pth/edit_record.php?t=formula&amp;r=$a\">$a*</a>";
// ��������� �� ���������
$vn = $fs[$a]['page_id'].'.'.$n; // ����� ����� �� ���������
$rz = '<a id="f'.$vn.'"></a>
<div class="math">
<div class="l">('.$vn.")</div>\n";
$rz .= formula_1div('r',$fs[$a],$elk); //'<div class="r" id="f'.$fs[$a]['ID'].'">'.$fs[$a]['markup']."\n".$elk."</div>\n</div>\n";
$rz .= '<div style="clear:both"></div>'."\n";
$c++;
return $rz;
}

function formula_1div($c,$f,$elk){
return '<div class="'.$c.'" id="f'.$f['ID'].'">'.$f['markup']."\n".$elk."</div>\n";
}

function formula_for_page($a){
if (count($_POST)) formula_insert();
global $page_id;
// ������ �� �������� �� ������� �� �������� ��������
$da = db_select_m('*', 'formula', "`page_id`=$page_id");
// ��� ���� ������ ������ �� ����� �������� �� ���������� �� ������� ����� $a
if(!count($da)){
  $id = db_table_field('page_id', 'formula', "ID=$a");
  $da = db_select_m('*', 'formula', "`page_id`=$id");
}
$rz = array();
foreach($da as $d) $rz[$d['ID']] = $d;
return $rz;
}

function formula_edit_form(){
$f = new HTMLForm('formula_edit');
$fi = new FormTextArea('', 'markup', 50, 10);
$fi -> ckbutton = '';
$f -> add_input( $fi ); 
$f -> add_input( new FormInput('', '', 'submit', encode('��������� �� ���������')) );
return $f -> html(); 
}

function formula_insert(){
global $page_id;
$_POST['page_id'] = $page_id;
db_insert_1($_POST, 'formula');
}

function formula_ref($a){
global $main_index, $page_id, $hidden_formulas;
$id = substr($a, 1);
$d = db_select_1('*', 'formula', "`ID`=$id");
$vn = $d['page_id'].'.'.$d['number']; // ����� ����� �� ���������
$rz = '(<a href="'.$main_index.'?pid='.$d['page_id'].'#f'.$vn.'" onmouseover="formulaOver(event,'.$id.');" onmouseleave="formulaLeave();">'.$d['page_id'].'.'.$d['number'].'</a>)';
// ��� ��������� �� � �� �������� ��������, �� ������ � ���������� $hidden_formulas
if($d['page_id']!=$page_id) $hidden_formulas .= formula_1div('h',$d,'');
return $rz;
}

?>

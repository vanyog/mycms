<?php
/*
MyCMS - a simple Content Management System
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

error_reporting(E_ALL); ini_set('display_errors',1);

include_once("conf_manage.php");
include_once($idir."lib/translation.php");
include_once($idir."lib/f_db_select_m.php");
include_once($idir."lib/o_form.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_update_where.php");

// ���� ������ �� �������� �� ����������� ������� �� ���������� �� ������������.
// ������ ������� ���������� ��� ���-����� �������� �� ����� �� ������������ ������ 
// � ������� ����� �� ������������ ��.

// � ��� �������� � ���������� �� ���������� ��������� ���� �� �� �������
// ��������� $_GET['p'], ����� �������� � ����, ����� ������ �� �� ����� � ������� �� 
// ����������� �� ����������.


// ���������� �� ����������� �����, ��� ��� ������
if (count($_POST)) process_trans();

// ������� � WHERE ������ �� sql �������� �� �������� �� ���������
$w = '';

// ��� ��� ��������� 
if (isset($_GET['p'])) $w = " AND `name` LIKE '%".$_GET['p']."%'"; 

// ����� �� ������� �� ������ ��������� �� ����� �� ������������.
$na = db_select_m('name','content',"`language`='$default_language'$w ORDER BY `date_time_2` DESC"); //print_r($na); die;

// �������� �� ������ �������� ����� ��� ����� �� ������������
$la = $languages;
unset($la[$default_language]);
$la = array_keys($la);

$page_content = '';

if (!count($la)) $page_content = '<p>���� ���� � ���� �� '.$languages[$default_language].'</p>';
else {

// �� ����� ��� �� ������
foreach($na as $n){
  $n1 = $n['name']; //echo "$n1<br>";
  // ��� � ������� ������� ������, �� ������� ����� �� ���������� � ������� �� ����������
  $page_content .= untraslated_string($n1);
  if ($page_content) break;
}

} // ���� �� if (���� ���� ����) ...

// JavaScript, ����� ������� � ������ �� ������, �� �� ���� ����� �� �� ������ � �������
// � ������� �� ���������� � ����� �� �� ������� �� ������ ����� �������

$page_content .= '<script>
var t = document.getElementById("text");
t.select();
t.setSelectionRange(0, 99999);
// JavaScript, ����� ��������� ���������� �� ����� Save � Ctrl+Enter
var lastKey;
function do_save(e){
if((lastKey=="Meta") && (e.key=="Enter")){
  var f = document.forms.new_tralslation;
  f.submit();
}
lastKey = e.key;
}
function translateByGoogle(){
var haveKey = ';
$gak = stored_value('GoogleTranslateAPIkey','');
if(empty($gak)) $page_content .= 'false';
else $page_content .= 'true';
$page_content .= ';
if(!haveKey) { alert("\'GoogleTranslateAPIkey\' not specified."); return; }
var f = document.forms.new_tralslation;
alert(f.action);
}
</script>
';

// JavaScript, ����� ��������� ���������� �� ����� Save � Ctrl+Enter


// ��������� �� ����������
include_once("build_page.php");

//----------- ������� --------------

//
// �������, ����� ������� ����� �� ����������
// $n1 - ��� �� ������ �� ����������
// $l  - ����, �� ����� ������ �� �� �������

function new_translation($n1,$l){
global $languages, $default_language;
  $d = db_select_1('*','content',"`name`='$n1' AND `language`='$default_language'");
  $v = $d['text'];
  $t = db_select_1('*','content',"`name`='$n1' AND `language`='$l'");
  if(isset($t['text'])) $v = $t['text'];
  else{
    $n2 = db_select_m('name','content',"`text`='$v' AND `language`='$default_language'");
    foreach($n2 as $n){
      $v = db_table_field('text','content',"`name`='".$n['name']."' AND `language`='$l'",'',false);
      if($v) break;
    }
    if(!$v) $v = $d['text'];
  }
  $f = new HTMLForm('new_tralslation');
  $f->add_input(new FormInput('','name','hidden',$n1));
  $f->add_input(new FormSelect('Not editable','nolink',array('0','1'),$d['nolink']));
  $f->add_input(new FormInput('','language','hidden',$l));
  $i = new FormTextArea('Text in '.$languages[$l],'text',100,15,
                         str_replace('&','&amp;',stripslashes($v)));
  $i->js = ' onkeydown="do_save(event);"';
  $f->add_input( $i );
  $f->add_input(new FormInput('','','submit','Save (cmd+Enter)'));
  $gb = new FormInput('','','button','By Google');
  $gb->js = ' onclick="translateByGoogle();"';
  $f->add_input( $gb );
  return "<p>String name: '".$d['name']."'<br>\nIn ".$languages[$default_language].
         ":</p>\n".'<textarea id="deflang" cols="100" rows="10"  disabled="disabled">'.
         str_replace('&','&amp;',stripslashes($d['text'])).
         "</textarea>\n".$f->html();
}

//
// �� ����� ����, �������� �� ������������� ��, �� ��������� ���� ��� ������ ��
// ������� � ��� $n1. ��� ���� �� ����� ����� �� ����������� ��, � ��� ��� �������
// �� ������ ����� �� ����� ������ ������.
//
function untraslated_string($n1){
global $la, $default_language;
  // �� ����� ����, �������� �� ������������� ��
  foreach($la as $l){
    $r = db_select_1('*','content',"`name`='$n1' AND `language`='$l'");
    if ( !empty(db_table_field('text', 'content', "`name`='$n1' AND `language`='$default_language'",''))
         && (!$r
             || ($r['date_time_2'] < db_table_field('date_time_2', 'content', "`name`='$n1' AND `language`='$default_language'"))
            )
       )
       return new_translation($n1,$l);
  }
  return '';
}

//
// ��������� ��������� ����������� �����
//
function process_trans(){
  unset($_POST['action']);
  $_POST['date_time_2']='NOW()';
  $id = db_table_field('ID', 'content', "`name`='".$_POST['name']."' AND `language`='".$_POST['language']."'");
  if($id) {
    db_update_where($_POST, 'content', "`ID`=$id");
  }
  else {
    $_POST['date_time_1']='NOW()';
    db_insert_1($_POST,'content');
  }
}

?>

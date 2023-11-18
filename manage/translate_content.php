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
  $n1 = $n['name'];
  // ��� � ������� ������� ������, �� ������� ����� �� ���������� � ������� �� ����������
  $page_content .= untraslated_string($n1);
  if ($page_content) break;
}
$page_content .= '<p>No more strings for translation</p>';

} // ���� �� if (���� ���� ����) ...

$page_content = "<h1>Translate content</h1>\n".$page_content;

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
if(typeof ajaxO == "undefined"){
if (window.XMLHttpRequest) ajaxO = new XMLHttpRequest();
else ajaxO = new ActiveXObject("Microsoft.XMLHTTP");
}
function translateByGoogle(){
var haveKey = ';
$gak = stored_value('GoogleTranslateAPIkey','');
if(empty($gak)) $page_content .= 'false';
else $page_content .= 'true';
$page_content .= ';
if(!haveKey) { alert("\'GoogleTranslateAPIkey\' not specified."); return; }
var f = document.forms.new_tralslation;
var te = f.text;
var tx = te.value.substring(te.selectionStart,te.selectionEnd);
if(!tx) { alert("Select some text and try again."); return; }
tx = encodeURI(tx);
var fa = f.action;
var na = fa.replace("translate_content.php","translate_byGoogle.php") + 
         "?a=" + Math.floor(Math.random() * 1000) +
         "&lang=" + "'.$GLOBALS['currentLanguage'].'" + 
         "&text=" + tx;
ajaxO.onreadystatechange = onAjaxResponse;
ajaxO.open("GET", na, true);
ajaxO.send();
}
function onAjaxResponse(){
if (ajaxO.readyState == 4 && ajaxO.status == 200){
var f = document.forms.new_tralslation;
var te = f.text;
te.value = te.value.substring(0,te.selectionStart) + 
           ajaxO.responseText + 
           te.value.substr(te.selectionEnd);
}
}
function copyAgain(){
if(!confirm("Do you really want to copy the default text over the translation?")) return;
var t = document.getElementById("deflang").value;
var v = document.forms.new_tralslation.text;
v.value = t;
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
global $languages, $default_language, $adm_pth;
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
         "</textarea>\n".
         '<input type="button" value="Copy" onclick="copyAgain()"> &nbsp; '.
         '<a href="'.$adm_pth.'edit_record.php?t=content&r='.$d['ID'].'" target="editTub">Edit</a>'.$f->html();
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
    $GLOBALS['currentLanguage'] = $l;
    $r = db_select_1('*','content',"`name`='$n1' AND `language`='$l'");
    if ( !empty(db_table_field('text', 'content', "`name`='$n1' AND `language`='$default_language'",''))
         && (!$r
             || ($r['date_time_2'] < db_table_field('date_time_2', 'content', "`name`='$n1' AND `language`='$default_language'"))
            )
       )
       return new_translation($n1,$l);
    else
       return '';
  }
  return '<p>No strings for translation</p>';
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

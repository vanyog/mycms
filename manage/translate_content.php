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

// Този скрипт се използва на многоезични сайтове за превеждане на съдържанието.
// Намира първият непреведен или най-скоро променен на езика по подразбиране стринг 
// и показва форма за превеждането му.

// С цел намиране и превеждане на определени стрингове може да се изпрати
// параметър $_GET['p'], чиято стойност е част, която трябва да се среща в имената на 
// стринговете за превеждане.


// Обработват се изпратените данни, ако има такива
if (count($_POST)) process_trans();

// Добавка в WHERE частта на sql заявката за немиране на стрингове
$w = '';

// Ако има параметър 
if (isset($_GET['p'])) $w = " AND `name` LIKE '%".$_GET['p']."%'"; 

// Четат се иманата на всички стрингове на езика по подразбиране.
$na = db_select_m('name','content',"`language`='$default_language'$w ORDER BY `date_time_2` DESC"); //print_r($na); die;

// Кодовете на всички останали езици без езика по подразбиране
$la = $languages;
unset($la[$default_language]);
$la = array_keys($la);

$page_content = '';

if (!count($la)) $page_content = '<p>Този сайт е само на '.$languages[$default_language].'</p>';
else {

// За всяко име на стринг
foreach($na as $n){
  $n1 = $n['name']; //echo "$n1<br>";
  // Ако е намерен липсващ превод, се показва форма за превеждане и цикълът се прекратява
  $page_content .= untraslated_string($n1);
  if ($page_content) break;
}

} // край на if (само един език) ...

// JavaScript, който маркира и текста за превод, за да може бързо да се копира и постави
// в софтуер за превеждане и после да се постави на негово бясто превода

$page_content .= '<script>
var t = document.getElementById("text");
t.select();
t.setSelectionRange(0, 99999);
// JavaScript, който осигурява изпълнение на бутон Save с Ctrl+Enter
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

// JavaScript, който осигурява изпълнение на бутон Save с Ctrl+Enter


// Показване на страницата
include_once("build_page.php");

//----------- Функции --------------

//
// Функция, която показва форма за превеждане
// $n1 - име на стринг за превеждане
// $l  - език, но който трябва да се преведе

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
// За всеки език, различен от подразбиращия се, се проверява дали има превод на
// стринга с име $n1. Ако няма се връща форма за въвеждането му, а ако има преводи
// на всички езици се връща празен стринг.
//
function untraslated_string($n1){
global $la, $default_language;
  // За всеки език, различен от подразбиращия се
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
// Функцията обработва изпратените данни
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

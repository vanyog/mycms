<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Модул за нормативни документи

include_once($idir.'lib/f_db_insert_or_1.php');
include_once($idir.'lib/f_unset_self_query_var.php');

$page_header .= '<script>
function copyNormDocRef(e){
var u = document.URL;
var i = u.indexOf("#");
if(i>-1) u = u.substring(0, i);
var h = document.getElementById("copyHelper");
h.style.display = "initial";
h.value = u + "#" + e.id;
h.select();
document.execCommand("copy");
h.style.display = "none";
}
</script>
';

// Главна функция на модула
// $a - име на записа в таблица 'content', който съдържа "суровия" текст на документа

function normdoc($a = 'ZDA'){
if(!empty($_GET['doc'])){ $a = $_GET['doc']; unset($_GET['doc']); }
global $adm_pth;
$nd = new NormDoc($a);
if(isset($_GET['get'])) $nd->get($_GET['get']);
$rz = '';
if(isset($_GET['save']) and ($_GET['save']=='1')){
  $nd->from_html();
}
else{
  $nd->from_db();
  if(!$nd->fromdb) $rz = '<p style="color:red;">Not from database</p>'."\n";
}
$lk = '<p><a href="'.set_self_query_var('no', '1').'">'.encode('Необработен текст')."</a></p>\n";
if(isset($_GET['no']) and ($_GET['no']=='1')){
  $lk = '<p><a href="'.unset_self_query_var('no', '1').'">'.encode('Обработен текст')."</a></p>\n";
  return $lk.translate($nd->name);
}
$rz .= $nd->display();
if(in_edit_mode() && count($nd->parts)) $rz .= "<a href=\"$adm_pth"."edit_record.php?t=content&r=$nd->id\">*</a>";
if(isset($_GET['save']) and ($_GET['save']=='1')) $nd->save_to_db();
return $lk.$rz.
'<input type="text" id="copyHelper" style="display:none;">'."\n";
}

// - - - - - - NormDoc - - - - - -

class NormDoc{

public $name;      // Име на норм. документ. Съвпада с името под което, в таблица 'content' се съгранява HTML кода му.
public $html = ''; // HTML код от таблица 'content', съдържащ нормативен документ.
public $id = 0;    // Номер, който съвпада с номера на записа от таблица 'content'
public $txt = '';  // Пълен текст, изчистен от html тагове
public $parts = array(); // Части на нормативния документ Глави, Допълнителна разпоредба и т.н.
                         // Всяка част е обект от клас DocPart, която съдържа още части от същия клас
public $fromdb = false;

function __construct($a){
$this->name = $a;
}

// Извличане структурата на документа по пълния му HTML код

function from_html(){
global $language;
$cd = db_select_1('ID,text', 'content', "`language`='$language' AND `name`='$this->name'");
$this->html = $cd['text'];
$this->txt = str_replace(')<', ') <', $this->html );
$this->txt = strip_tags($this->txt);
$this->txt = str_replace('&nbsp;', ' ', $this->txt );
$this->id = $cd['ID'];
$this->split_parts();
}

// Извличане структурата на документа от таблица 'normdoc'

function from_db(){
global $language;
$id = db_table_field('ID', 'content', "`language`='$language' AND `name`='$this->name'");
if(!$id) return "Document '$this->name' is empty";
$this->id = $id;
$da = db_select_m('*', 'normdoc', "`doc_id`=$id ORDER BY `ID`");
if(!count($da)) { $this->from_html(); return; }
foreach($da as $d) $this->add_part($d);
$this->fromdb = true;
}

function add_part($d){
switch ($d['type']){
case '':
case encode('Глава '):
case encode('Раздел '):
case encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА'):
case encode('Допълнителни разпоредби'):
case encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни разпоредби'):
case encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни и заключителни разпоредби'):
case encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Заключителни разпоредби'):
case encode('ПОСТАНОВЛЕНИЕ'):
                $this->parts[$d['ind']] = new DocPart('', $d['type'], $d['name'], $d['text'], $d['ind']);
                break;
case 'paragraf':
case 'chlen'   :$this->parts[$d['glava']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']],
                                                      $d['type'], $d['name'], $d['text'], $d['ind']);
                break;
case 'alineya' :$this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']],
                                                      $d['type'], $d['name'], $d['text'], $d['ind']);
                break;
case 'tochka':
                if(!isset($this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']]))// die(print_r($d,true));
                     $this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']],
                                                      $d['type'], $d['name'], $d['text'], $d['ind']);
                else $this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']],
                                                      $d['type'], $d['name'], $d['text'], $d['ind']);
                break;
default        :
                break;
}
}

// Разделяне на целия текст на документа на основни части:

function split_parts(){
$sp = '/'.encode('Глава ').
      '|'.encode('Раздел ').
      '|'.encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА').
      '|'.encode('Допълнителни разпоредби').
      '|'.encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ').
      '|'.encode('Преходни разпоредби').
      '|'.encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ').
      '|'.encode('Преходни и заключителни разпоредби').
      '|'.encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ').
      '|'.encode('Заключителни разпоредби').
      '|'.encode('ПОСТАНОВЛЕНИЕ').'/';
$mt = array();
$mc = preg_match_all($sp, $this->txt, $mt );
$ar = preg_split(    $sp, $this->txt );
foreach($ar as $mi=>$m){
  $ty = '';
  if($mi>0) $ty = $mt[0][$mi-1];
  $this->parts[] = new DocPart('', $ty, '', $m, count($this->parts));
  $e = end($this->parts);
  $e->split_part();
}
}

function display(){
$rz = '';
if(!count($this->parts)) return translate($this->name);
foreach($this->parts as $p){
  $rz .= $p->display();
}
return $rz;
}

function save_to_db(){
db_delete_where('normdoc', "`doc_id`=$this->id");
if($this->fromdb) return;
foreach($this->parts as $p) $p->save_to_db($this->id);
}

function get(){
$sp = '/(s|c|p|a|t)(\d+)/';
$m = array();
$mc = preg_match_all($sp, $_GET['get'], $m);
//print_r($m); die;
}

}

// - - - - - - DocPart - - - - - -

class DocPart{

public $parent = NULL;
public $type = '';
public $name = '';
public $txt = '';
public $id = '';
public $index = 0;

public $data = array();

public $parts = array();

function __construct($p, $ty, $nm, $tx, $i){
$this->parent = $p;
$this->type = $ty;
$this->name = $nm;
$this->txt = $tx;
$this->index = $i;
}

// Разделяне на дадена част на нейните по-малки части

function split_part(){
$sp = ''; $ty = '';
$sp = "/".encode('Чл. ')."(\d+".encode('(?:а|б|в|г|д|е|ж|з|и|к|л|м)')."{0,1})\. /";
$ty = 'chlen';
switch ($this->type){
case '': // Ако главната част не съдържа членове, не се прави нищо
        if(!preg_match($sp, $this->txt)) return;
case encode('Глава '):
case encode('Раздел '):
//                $sp = "/".encode('Чл. ')."(\d+".encode('(?:а|б|в|г|д|е|ж|з|и|к|л|м)')."{0,1})\. /"; $ty = 'chlen';
                break;
case encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА'):
case encode('Допълнителни разпоредби'):
case encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни разпоредби'):
case encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни и заключителни разпоредби'):
case encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Заключителни разпоредби'):
case encode('ПОСТАНОВЛЕНИЕ'):
                $sp ="/&sect; (\d+".encode('(?:а|б|в|г|д|е|ж|з|и|к|л|м)')."{0,1})\./"; $ty = 'paragraf';
                break;
case 'chlen'   :$sp = "/\((\d+)\) /";       $ty = 'alineya';
                break;
case 'paragraf':$sp = "/(?:\n| )(\d+)\. /"; $ty = 'tochka'; break;
case 'alineya' :$sp = "/(?:\n| )(\d+)\. /"; $ty = 'tochka'; break;
default:        $sp = "/=========/";        $ty = ''; break;
}
$mt = array();
$mc = preg_match_all($sp, $this->txt, $mt );
// Ако в параграф нe са намерини точки, то се търсят алинеи
if( ($this->type=='paragraf') and !$mc ){
  $sp = "/\((\d+)\) /";
  $ty = 'alineya';
  $mc = preg_match_all($sp, $this->txt, $mt );
}
// Ако в член нe са намерини алинеи, то се търсят точки
if( ($this->type=='chlen') and !$mc ){
  $sp = "/(?:\n| )(\d+)\. /";
  $ty = 'tochka';
  $mc = preg_match_all($sp, $this->txt, $mt );
}
$ar = preg_split(    $sp, $this->txt );
// В случай на параграф или алинея, ако точките не следват последователно, не се прави разделяне
if( !($mc && in_array($this->type, array('paragraf', 'alineya')) && !is_set_correct($mt[1])) )
 foreach($ar as $ci=>$c){
  if($ci==0) switch ($this->type) {
  case encode('Глава '):
  case encode('Раздел '):
  case encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА'):
  case encode('Допълнителни разпоредби'):
  case encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ'):
  case encode('Преходни разпоредби'):
  case encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
  case encode('Преходни и заключителни разпоредби'):
  case encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
  case encode('Заключителни разпоредби'):
  case encode('ПОСТАНОВЛЕНИЕ'):
       $this->txt = '';
       // Част като: (Отм. - ДВ, бр. 59 от 1993 г.) и подобри, се премества от заглавието в текста
       $lm = array();
       if(preg_match('/\(.*\)/s', $c, $lm)){
          $this->name = $this->type.' <br>'.trim(str_replace($lm[0], '', $c));
          $this->txt = trim($lm[0]);
       }
       else $this->name = $this->type."$c";
       break;
  default: $this->txt = trim($c);
  }
  else {
    $this->parts[] = new DocPart($this, $ty, trim($mt[1][$ci-1]), $c, count($this->parts));
  }
}
foreach($this->parts as $p) $p->split_part();
}

function display(){
$h1 = ''; $br = ' <br>'; $h2 = '';
$t1 = ''; $t2 = '';
$ttl = ' title="Click to copy reference" onclick="copyNormDocRef(this);"';
switch ($this->type){
case encode('Глава '):
case encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА'):
case encode('Допълнителни разпоредби'):
case encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни разпоредби'):
case encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни и заключителни разпоредби'):
case encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Заключителни разпоредби'):
case encode('ПОСТАНОВЛЕНИЕ'):
                $this->id = 's'.$this->index;
                $h1 = '<h2 id="'.$this->id.'" style="cursor:pointer;"'.$ttl.'>'; $h2 = '</h2>';
                break;
case encode('Раздел '):
                $this->id = 's'.$this->index;
                $h1 = '<h3 id="'.$this->id.'" style="cursor:pointer;"'.$ttl.'>'; $h2 = '</h3>';
                break;
case 'chlen':   $this->id = 'c'.$this->name;
                $h1 = '<div><strong id="'.$this->id.'"'.$ttl.' style="color:blue;cursor:pointer;">'.encode('Чл. ');
                $br = ' '; $h2 = '.</strong>'; $t2 = '</div>';
                break;
case 'alineya': if(is_object($this->parent)) $this->id = $this->parent->id.'a'.$this->name;
                $h1 = '<p><span id="'.$this->id.'"'.$ttl.' style="color:red;cursor:pointer;">(';
                $br = ' '; $h2 = ')</span>'; $t2 = '</p>';
                break;
case 'paragraf':if(is_object($this->parent)) $this->id = $this->parent->id.'p'.$this->name;
                $h1 = '<div><strong id="'.$this->id.'"'.$ttl.' style="color:green;cursor:pointer;">&sect; ';
                $br = ' '; $h2 = '.</strong>'; $t2 = '</div>';
                break;
case 'tochka':  if(is_object($this->parent)) $this->id = $this->parent->id.'t'.$this->name;
                $h1 = ' <br><span id="'.$this->id.'"'.$ttl.' style="cursor:pointer;">'; $h2 = '.</span>'; $t1 = ' '; $t2 = '';
                break;
}
$this->txt = preg_replace('/(-{4,}|(\.\s*){4,})/', '<p>${1}</p>', $this->txt );
$rz = '';
$rz .= "$h1$this->name$h2\n";
$rz .= "$t1$this->txt";
foreach($this->parts as $p){
   $rz .= $p->display();
}
$rz .= "$t2\n";
return $rz;
}

function save_to_db($id){
global $language;
switch ($this->type){
case '':
case encode('Глава '):
case encode('Раздел '):
case encode('ДОПЪЛНИТЕЛНА РАЗПОРЕДБА'):
case encode('Допълнителни разпоредби'):
case encode('ДОПЪЛНИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни разпоредби'):
case encode('ПРЕХОДНИ И ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Преходни и заключителни разпоредби'):
case encode('ЗАКЛЮЧИТЕЛНИ РАЗПОРЕДБИ'):
case encode('Заключителни разпоредби'):
case encode('ПОСТАНОВЛЕНИЕ'):
                $this->data = array('doc_id'=>$id, 'type'=>$this->type, 'ind'=>$this->index, 'glava'=>$this->index,
                                    'cpind'=>0, 'aind'=>0, 'tind'=>0, 'name'=>$this->name, 'text'=>$this->txt);
                break;
case 'chlen':
case 'paragraf':$this->data = $this->parent->data;
                $this->data['cpind'] = $this->index;
                break;
case 'alineya': $this->data = $this->parent->data;
                $this->data['aind'] = $this->index;
                break;
case 'tochka':  $this->data = $this->parent->data;
                $this->data['tind'] = $this->index;
                break;
}
$this->data['type'] = $this->type;
$this->data['ind']  = $this->index;
$this->data['name'] = $this->name;
$this->data['text']  = $this->txt;
$where = array_to_where($this->data, array('text', 'name'));
//if($this->type==encode('Преходни и заключителни разпоредби') && $this->index==23) die($where);
db_insert_or_1($this->data, 'normdoc', $where, 'b');
foreach($this->parts as $p) $p->save_to_db($id);
}

} // - class DocPart -

// - - - - - - functions - - - - - -

function array_to_where($a, $f){
$rz = '';
foreach($a as $k=>$v) if ( !in_array($k, $f) ) {
  if($rz) $rz .= ' AND ';
  $rz .= "`$k`='$v'";
}
return $rz;
}

function is_set_correct($a){
if(!is_array($a) || !count($a)) return false;
$rz = $a[0]==1; $i = 1;
while( ($i < count($a)) && $rz){ $rz = $rz && ($a[$i]==$a[$i-1]+1); $i++; }
return $rz;
}

?>
<?php
/*
VanyoG CMS - a simple Content Management System
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

// ����� �� ���������� ���������

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

// ������ ������� �� ������
// $a - ��� �� ������ � ������� 'content', ����� ������� "�������" ����� �� ���������

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
$lk = '<p><a href="'.set_self_query_var('no', '1').'">'.encode('����������� �����')."</a></p>\n";
if(isset($_GET['no']) and ($_GET['no']=='1')){
  $lk = '<p><a href="'.unset_self_query_var(['no','save'], '1').'">'.encode('��������� �����')."</a> &nbsp; ";
  if(in_edit_mode()) $lk .= "<a href=\"".set_self_query_var('save', '1')."\">".encode('��������� ������')."</a>";
  $lk .= "</p>\n";
  $rz .= '<pre>'.translate($nd->name).'</pre>';
}
else $rz .= $nd->display();
if (in_edit_mode() && count($nd->parts) && ($nd->id>0) ) 
   $rz .= "<a href=\"$adm_pth"."edit_record.php?t=content&r=$nd->id\">*</a>";
if (isset($_GET['save']) and ($_GET['save']=='1'))
   $nd->save_to_db();
return $lk.$rz.
'<input type="text" id="copyHelper" style="display:none;">'."\n";
}

// - - - - - - class NormDoc - - - - - -
//        ���� ���������� ��������

class NormDoc{

public $name;      // ��� �� ����. ��������. ������� � ����� ��� �����, � ������� 'content' �� ��������� HTML ���� ��.
public $html = ''; // HTML ��� �� ������� 'content', �������� ���������� ��������.
public $id = 0;    // �����, ����� ������� � ������ �� ������ �� ������� 'content'
public $txt = '';  // ����� �����, �������� �� html ������
public $parts = array(); // ����� �� ����������� �������� �����, ������������ ���������� � �.�.
                         // ����� ���� � ����� �� ���� DocPart, ����� ������� ��� ����� �� ����� ����
public $fromdb = false;

function __construct($a){
$this->name = $a;
}

// ��������� ����������� �� ��������� �� ������ �� HTML ���

function from_html(){
global $language;
$cd = db_select_1('ID,text', 'content', "`language`='$language' AND `name`='$this->name'");
$this->html = $cd['text'];
$this->txt = str_replace(')<', ') <', $this->html );
$this->txt = str_replace('.<', '. <', $this->txt );
$this->txt = strip_tags($this->txt);
$this->txt = str_replace('&nbsp;', ' ', $this->txt );
$this->txt = str_replace("\t", ' ', $this->txt );
//$this->txt = str_replace(chr(167), '&sect;', $this->txt );
$this->id = $cd['ID'];
$this->split_parts();
}

// ��������� ����������� �� ��������� �� ������� 'normdoc'

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
try { switch ($d['type']){
case '':
case encode('����� '):
case encode('������ '):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������� ����������'):
case encode('�������� � ������������ ����������'):
case encode('�������� � ������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������������'):
                $this->parts[$d['ind']] = new DocPart('', 
                                                      $d['type'], $d['name'], $d['text'], 
                                                      $d['ind'], $d['ID']);
                break;
case 'paragraf':
case 'chlen'   :$this->parts[$d['glava']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']],
                                                      $d['type'], $d['name'], $d['text'], 
                                                      $d['ind'], $d['ID']);
                break;
case 'alineya' :$this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']],
                                                      $d['type'], $d['name'], $d['text'], 
                                                      $d['ind'], $d['ID']);
                break;
case 'tochka':
                if(!isset($this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']]))
                     $this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']],
                                                      $d['type'], $d['name'], $d['text'], 
                                                      $d['ind'], $d['ID']);
                else $this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']]->parts[$d['ind']]
                                        = new DocPart($this->parts[$d['glava']]->parts[$d['cpind']]->parts[$d['aind']],
                                                      $d['type'], $d['name'], $d['text'], 
                                                      $d['ind'], $d['ID']);
                break;
default        :
                break;
}}
catch (Exception $e) { die("aaa"); }
}

// ��������� �� ����� ����� �� ��������� �� ������� �����:

function split_parts(){
$sp = '/'.encode('����� ').
      '|'.encode('������ ').
      '|'.encode('������������ ����������').
      '|'.encode('������������ ����������').
      '|'.encode('������������ ����������').
      '|'.encode('������������ ����������').
      '|'.encode('�������� ����������').
      '|'.encode('�������� � ������������ ����������').
      '|'.encode('�������� � ������������ ����������').
      '|'.encode('������������ ����������').
      '|'.encode('������������ ����������').
      '|'.encode('�������������').'/';
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
}

}

// - - - - - - class DocPart - - - - - -
//   ���� ���� �� ���������� ��������

class DocPart{

public $parent = NULL;
public $type = '';
public $name = '';
public $txt = '';
public $id = '';
public $index = 0;
private $dbid = ''; // `ID` ���� �� ������� 'normdoc' �� ������ �����

public $data = array();

public $parts = array();

function __construct($p, $ty, $nm, $tx, $i, $di = ''){
$this->parent = $p;
$this->type = $ty;
$this->name = $nm;
$this->txt = $tx;
$this->index = $i;
$this->dbid = $di;
}

// ��������� �� ������ ���� �� ������� ��-����� �����

function split_part(){
$sp = ''; $ty = '';
$sp = "/".encode('��. ').
      "(\d+".encode('(?:�|�|�|�|�|�|�|�|�|�|�|�)')."{0,1})\.".
      "(?:SUP>(\d)\.)* /";
$ty = 'chlen';
switch ($this->type){
case '': // ��� �������� ���� �� ������� �������, �� �� ����� ����
        if(!preg_match($sp, $this->txt)) return;
case encode('����� '):
case encode('������ '): break;
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������� ����������'):
case encode('�������� � ������������ ����������'):
case encode('�������� � ������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������������'):
                $sp ="/&sect; (\d+".encode('(?:�|�|�|�|�|�|�|�|�|�|�|�)')."{0,1})\./"; $ty = 'paragraf';
                break;
case 'chlen'   :$sp = "/\((\d+)\) /";       $ty = 'alineya';
                break;
case 'paragraf':$sp = "/(?:\n| )(\d+)\. /"; $ty = 'tochka'; break;
case 'alineya' :$sp = "/(?:\n| )(\d+".encode('(?:�|�|�|�|�|�|�|�|�|�|�|�)')."{0,1})\. /"; $ty = 'tochka'; 
                break;
default:        $sp = "/=========/";        $ty = ''; break;
}
$mt = array();
$mc = preg_match_all($sp, $this->txt, $mt ); 
// ��� � �������� �e �� �������� �����, �� �� ������ ������
if( ($this->type=='paragraf') and !$mc ){
  $sp = "/\((\d+)\) /";
  $ty = 'alineya';
  $mc = preg_match_all($sp, $this->txt, $mt );
}
// ��� � ���� �e �� �������� ������, �� �� ������ �����
if( ($this->type=='chlen') and !$mc ){
  $sp = "/$(?:\n| )(\d+)\. /";
  $ty = 'tochka';
  $mc = preg_match_all($sp, $this->txt, $mt );
}
$ar = preg_split( $sp, $this->txt ); //if(($this->type=='tochka') && strpos($this->txt, encode('���������, ���������, �������'))){ die($this->txt); }
// � ������ �� �������� ��� ������, ��� ������� �� ������� ��������������, �� �� ����� ���������
if( !($mc && in_array($this->type, array('paragraf', 'alineya')) && !is_set_correct($mt[1])) )
 foreach($ar as $ci=>$c){
  if($ci==0) switch ($this->type) {
  case encode('����� '):
  case encode('������ '):
  case encode('������������ ����������'):
  case encode('������������ ����������'):
  case encode('������������ ����������'):
  case encode('������������ ����������'):
  case encode('�������� ����������'):
  case encode('�������� � ������������ ����������'):
  case encode('�������� � ������������ ����������'):
  case encode('������������ ����������'):
  case encode('������������ ����������'):
  case encode('�������������'):
       $this->txt = '';
       // ���� ����: (���. - ��, ��. 59 �� 1993 �.) � �������, �� ��������� �� ���������� � ������
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
    $tx = trim($mt[1][$ci-1]);
    if(($ty=='chlen') && $mt[2][$ci-1]) $tx .= '<sup>'.$mt[2][$ci-1].'</sup>';
    $this->parts[] = new DocPart($this, $ty, $tx, $c, count($this->parts));
  }
}
//if(($this->type=='tochka') && strpos($this->txt, encode('���������, ���������, �������'))){ var_dump($this->parts); die; }
foreach($this->parts as $p) $p->split_part();
}

function display(){
global $adm_pth;
static $hlev = 2;
$h1 = ''; $br = ' <br>'; $h2 = '';
$t1 = ''; $t2 = '';
$ttl = ' title="Click to copy reference" onclick="copyNormDocRef(this);"';
switch ($this->type){
case encode('����� '):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������� ����������'):
case encode('�������� � ������������ ����������'):
case encode('�������� � ������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������������'):
                $this->id = 's'.$this->index;
                $h1 = '<h2 id="'.$this->id.'" style="cursor:pointer;"'.$ttl.'>'; $h2 = '</h2>';
                $hlev = 2;
                break;
case encode('������ '):
                $this->id = 's'.$this->index;
                $h1 = '<h3 id="'.$this->id.'" style="cursor:pointer;"'.$ttl.'>'; $h2 = '</h3>';
                $hlev = 3;
                break;
case 'chlen':   $this->id = 'c'.$this->name;
                $h1 = '<div><strong id="'.$this->id.'"'.$ttl.' style="color:blue;cursor:pointer;">'.encode('��. ');
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
if($this->type=='tochka') {
   $this->txt = preg_replace('/($(?:'.encode('�|�|�').')\))/', '<br>${1}', $this->txt);
}
$this->txt = preg_replace('/(-{4,}|(\.\s*){4,})/', '<p>${1}</p>', $this->txt );
// �������� �� �������� �� �������� ����, ��� ��� ������
//$h4 = contains_subtitle($this->name, $hlev); ������� �������� ��� ������ �� ������� �����������, ������ ���������.
$h4 = '';
//$h3 = contains_subtitle($this->txt, $hlev);
$h3 = '';
$rz = "$h1$this->name$h2\n$h4\n";
$rz .= "$t1$this->txt";
if(in_edit_mode() && ($this->dbid>0)) $rz .= " <a href=\"$adm_pth"."edit_record.php?t=normdoc&r=$this->dbid\">*</a> ";
foreach($this->parts as $p){
   $rz .= $p->display();
}
$rz .= "$t2$h3\n";
return $rz;
}

// ��������� �� ������� �� ��������� � ������� 'normdoc'

function save_to_db($id){
global $language;
switch ($this->type){
case '':
case encode('����� '):
case encode('������ '):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������� ����������'):
case encode('�������� � ������������ ����������'):
case encode('�������� � ������������ ����������'):
case encode('������������ ����������'):
case encode('������������ ����������'):
case encode('�������������'):
                $this->data = array('doc_id'=>$id, 'type'=>$this->type, 'ind'=>$this->index, 
                                    'glava'=>$this->index, 'cpind'=>0, 'aind'=>0, 'tind'=>0, 
                                    'name'=>$this->name, 'text'=>$this->txt);
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
db_insert_or_1($this->data, 'normdoc', $where, 'b', false);
foreach($this->parts as $p) $p->save_to_db($id);
}

} // - class DocPart -

// - - - - - - functions - - - - - -

function contains_subtitle(&$txt, $hlev){
$m = array();
$i = preg_match_all('/\r*\n\r*\n(.*?)(\(.*\))*$/', $txt, $m);
//if(isset($m[1][0]) && $m[1][0] && $m[2][0]) echo("$i<br>".print_r($m,true)."<p>");
$h3 = '';
if($i && $m[1][0]) $h3 .= "<h".($hlev+1).">".$m[1][0]."</h".($hlev+1).">\n"; 
if($i && !empty($m[2][0])) $h3 .= "<p>".$m[2][0]."</p>\n";
if(!empty($h3)) $txt = str_replace($m[0][0], '', $txt);
return $h3;
}

function array_to_where($a, $f){
$rz = '';
foreach($a as $k=>$v) if ( !in_array($k, $f) ) {
  if($rz) $rz .= ' AND ';
  $rz .= "`$k`='$v'";
}
return $rz;
}

// ��������� ����� true ��� $a � ����� �� �������������� ����� 1,2,...

function is_set_correct($a){
if(!is_array($a) || !count($a)) return false;
$rz = $a[0]==1; $i = 1;
while( ($i < count($a)) && $rz){
   $rz = $rz && (($a[$i]==intval($a[$i-1])+1) || ($a[$i]==$a[$i-1].encode('�')));
//   if(!$rz) die("$i ".print_r($a, true));
   $i++;
}
return $rz;
}

?>

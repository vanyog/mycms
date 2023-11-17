<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2022  Vanyo Georgiev <info@vanyog.com>

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

// ��������� ���� ���������� ����� �� ������ ������� �������� �������� �� ����������� ���������

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/o_form.php');

$rz = ''; $tx = '';
if(isset($_POST['text'])){
  $rz = process();
  $tx = $_POST['text'];
}

$f = new HTMLForm('check_form');
$ta = new FormTextArea(encode('����� �� ������ �� �����������,<br>��������� � Copy-Paste:'), 'text', 100, 10, $tx);
$ta->ckbutton = '';
$f->add_input( $ta );
$f->add_input( new FormInput('', '', 'submit', encode('�����������')) );

$page_header = '<style>
th { vertical-align:top; text-align:right; }
</style>
<script>
function fromClipboard(){
if(navigator.clipboard == undefined){
  alert("navigator.clipboard == undefined");
  return;
}
var tf = document.forms.check_form;
var ta = tf.text;
navigator.clipboard.readText().then((clipText) => (ta.value = clipText));
setTimeout(()=>{tf.submit();},1000)
}
</script>';

$page_content = '<p><a href="/">'.encode('������� ��������').'</a></p>'."\n".
                '<h1>'.encode('�������� �� ���������� � ������').'</h1>'."\n";
$page_content .= '<p>'.encode('������ �� ���������� ��: ').date('d.m.Y',filemtime(mod_path('conference'))).'</p>'."\n";
$page_content .= '<div class="message">'.$rz."</div>\n".
                 '<button onclick="fromClipboard();">'.encode('�� ��������').'</button>'."\n".
                 $f->html().
                 '<h2>'.encode('�����')."</h2>\n".
                 '<p>'.encode('����� "�� ��������" ������ ���� ��� FireFox, ��� �� ����� about:config �� �� ������ �������� true �� ����������� dom.events.asyncClipboard.readText � dom.events.testing.asyncClipboard.');

include_once($idir.'lib/build_page.php');

function process(){
$rz = '';
if(!$_POST['text']) 
    return encode('�� � �������� ����� �� ������!');
// �������� �� ������ �� ��� ����� �� ���������� �� ������ REFERENCES
$a = preg_split('/REFERENCES|'.encode('����������').'/',$_POST['text']);
if(count($a)!=2) 
   return encode('�������� �� ������� ���� REFERENCES/���������� ��� ������ � ���������� �� ���� �����.');
// ����������� �� ����������� �� ������������� ��������� � ������ REFERENCES
$r2 = array();
if(!preg_match_all('/\[(\d+)\]/', $a[1], $r2)) 
    return encode('��������� �� ������� ������ �� �������� ��������� ��� �� �� �� ���������� � [1], [2], ...');
for($i=1; $i<count($r2[1]); $i++){
    if($r2[1][$i]!=$r2[1][$i-1]+1) 
       $rz .= ''.encode('������ ����� �� �������� ').$r2[1][$i].'<br>';
}    
// ����������� �� �����������
$r = array();
preg_match_all('/\[(?:\d+\,{0,1} *)*\]/', $a[0], $r);
$c = array();
foreach($r[0] as $e){
  $m = array();
  preg_match_all('/\d+/', $e, $m);
  foreach($m[0] as $n) $c[$n] = '';
}
for($i = 1; $i<count($r); $i++){
    foreach($r[$i] as $j){
      if($j) $c[$j] = '';
    }
}
$k = array_keys($c); $z = '';
foreach($r2[1] as $i){
   if(!in_array($i, $k)) $z .= "$i, ";
}
if($z) $rz .= encode('�������� � ����������: ').count($r2[1]).'<br>'.
              encode('��������� '.$z.' �� �� �������� �� ������������ ����� � [ ].');
if(!$rz) $rz = encode('������ '.count($r2[1]).' ��������� �� �������� ��������.');
return '<h2>'.encode('��������').'</h2>'.
       '<p>'.encode('�������� � ������ ���������: ').implode(' ',$r[0]).'</p>'.
       '<p>'.$rz.'</p>';
}
?>
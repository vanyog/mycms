<?php
/*
MyCMS - a simple Content Management System
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

// Проверява дали въведеният текст на доклад съдържа правилно цитиране на литературни източници

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
$ta = new FormTextArea(encode('Текст на статия за проверяване,<br>постаавен с Copy-Paste:'), 'text', 100, 10, $tx);
$ta->ckbutton = '';
$f->add_input( $ta );
$f->add_input( new FormInput('', '', 'submit', encode('Проверяване')) );

$page_header = '<style>
th { vertical-align:top; text-align:right; }
</style>';

$page_content = '<h1>'.encode('Проверка на цитирането в статия').'</h1>'.
//                '<p>'.encode('<b>Указание:</b> Запишете doc/docx файла със статията в Plan Text (.txt) формат c encoding Cyrillic (Windows). Поставете с Copy-Paste в полето долу чистия текст на статията от създадения .txt файл.').'</p>'.
                $rz.
                $f->html();

include_once($idir.'lib/build_page.php');

function process(){
$rz = '';
if(!$_POST['text']) 
    return encode('Не е доставен текст на статия!');
// Статията се раздея на две части от заглавието на частта REFERENCES
$a = preg_split('/REFERENCES|'.encode('ЛИТЕРАТУРА').'/',$_POST['text']);
if(count($a)!=2) 
   return encode('Статията не съдържа част REFERENCES/ЛИТЕРАТУРА или същата е озаглавена по друг начин.');
// Проверяване на номерацията на литературните източници в частта REFERENCES
$r2 = array();
if(!preg_match_all('/\[(\d+)\]/', $a[1], $r2)) 
    return encode('Статиятаа не съдържа списък на цитирани източници или те не са номерирани с [1], [2], ...');
for($i=1; $i<count($r2[1]); $i++){
    if($r2[1][$i]!=$r2[1][$i-1]+1) 
       $rz .= ''.encode('Грешен номер на източник ').$r2[1][$i].'<br>';
}    
// Проверяване на цитиранията
$r = array();
preg_match_all('/\[((\d+)\,* *)+\]/', $a[0], $r);
$c = array();
for($i = 1; $i<count($r); $i++){
    foreach($r[$i] as $j){
      if($j) $c[$j] = '';
    }
}
$k = array_keys($c); $z = '';
foreach($r2[1] as $i){
   if(!in_array($i, $k)) $z .= "$i, ";
}
if($z) $rz .= encode('Източници '.$z.' не са цитирани.'); 
if(!$rz) $rz = encode('Всички '.count($r2[1]).' източници са цитирани правилно.');
return '<h2>'.encode('Резултат').'</h2>'.
       '<p>'.encode('Намерени в текста цитирания: ').implode(' ',$r[0]).'</p>'.
       '<p>'.$rz.'</p>';
}
?>
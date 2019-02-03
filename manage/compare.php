<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2015  Vanyo Georgiev <info@vanyog.com>

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

// Скриптът сравнява 2 текста

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_parse_content.php');

$f = new HTMLForm('comp_form');
$ta = new FormTextArea('Text 1:','text1');
$ta->ckbutton = '';
$f->add_input( $ta );
$ta = new FormTextArea('Text 2:','text2');
$ta->ckbutton = '';
$f->add_input( $ta );
$f->add_input( new FormInput('','','submit') );

$t1 = parse_content(db_table_field('text', 'content', "`ID`=5873")); //die($t1);
if (count($_POST)) $page_content = compare_texts($t1, $_POST['text2']);
else $page_content = $f->html();

include($idir.'lib/build_page.php');


function compare_texts($t1, $t2){
$t1 = remove_spaces($t1);
$t2 = remove_spaces($t2);
$a1 = explode(' ', $t1);
$a2 = explode(' ', $t2);
$rz = '';
$lc = 0;
foreach($a1 as $i=>$w){
  if ($i<count($a2))
     if ($w!=$a2[$i]) $rz .= "$w <span style=\"color:red;\">".$a2[$i]."</span><br>\n";
     else{
        $lc = strlen($rz);
        $rz .= "$w ".$a2[$i]."<br>\n";
     }
}
//$rz = substr($rz, 0, $lc);
return $rz.$lc." ".strlen($rz);
}

function remove_spaces($t1){
$i = 0;
$t1 = strip_tags($t1);
$t1 = str_replace("\t", ' ', $t1);
$t1 = str_replace("\n", ' ', $t1);
$t1 = str_replace("\r", ' ', $t1);
$t1 = str_replace("&nbsp;", ' ', $t1);
do $t1 = str_replace('  ', ' ', $t1, $i);
while ($i>0);
return $t1;
}

?>

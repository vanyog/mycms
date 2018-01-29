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

//
// Филтърът състява и показва съдържание на страница.
// Заглавията, отбелязани с тагове <h1>, <h2> и т.н. в стринга $s, се откриват и им се поставят уникални id атрибути.
// На мястото на стринга TOFCONTENTS се вмъква съставено съдържание - линкове към намерените заглавия.

global $h_id, $tof_contents;

function toc($s){// die($s);
global $h_id, $tof_contents;
$h_id = 0;
$tof_contents = '';
$s = preg_replace_callback('/<h(\d+)\s*(id=".+?")*>(.*?)<\/h\1>/s', 'toc_cb', $s);
if($h_id<2) $tof_contents = '';
else $tof_contents = "<div id=\"toc\">\n".
                '<h2>'.translate('filtertoc_toc')."</h2>\n".
                $tof_contents."</div>\n";
$s = str_replace('TOFCONTENTS', $tof_contents, $s);
return $s;
}


function toc_cb($a){
  global $h_id, $tof_contents;
  $h_id++;
//  print_r($a);
  $id = "ct$h_id";
  if(!$a[2]){
    $a[2] = 'id="ct'.$h_id.'"';
  }
  else {
    $m = array();
    preg_match_all('/id="(.*)"/', $a[2], $m);
    if(isset($m[1][0])) $id = $m[1][0];
  }
  $tof_contents .= '<a href="#'.$id.'" class="lev'.$a[1].'">'.strip_tags($a[3])."</a>\n";
  return '<h'.$a[1].' '.$a[2].'>'.$a[3].'</h'.$a[1].'>';
}

?>

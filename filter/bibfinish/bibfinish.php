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

// Филтър bibfinish замества в $t стринга BIBLIO_LIST със списък на литературни източници,
// намиращи се масива $GLOBALS['biblio_list'], който се съставя от модул BIBLIO

function bibfinish($t){ //print_r($GLOBALS['biblio_list']);
global $biblio_list;
if(!isset($biblio_list)) return $t;
// Съставяне на нов масив с изчистени описания на библ. източници
$na = array();
foreach($biblio_list as $k=>$v) $na[$k] = preg_replace('/\|\d*\|. /', '', strip_tags($v));
// Сортиране на новия масив
$oldLocal = setlocale(LC_COLLATE, 'bg_BG.utf8');
if(is_array($biblio_list)) uasort($na, 'strcasecmp');
//  print_r($biblio_list);  print_r($na); die;
setlocale(LC_COLLATE, $oldLocal);
// Вземане само на ключовете от сортирания масив
$na = array_keys($na);
$rz = '';
foreach($na as $k) $rz .= "<p id=\"bib$k\">$biblio_list[$k]</p>\n";
$c = 1;
foreach($na as $k){
  $t  = str_replace("|$k|", "<a href=\"#bib$k\">$c</a>", $t);
  $rz = str_replace("|$k|", "$c", $rz);
  $c++;
}
$t = str_replace("BIBLIO_LIST", "$rz", $t);
return $t;
}

?>

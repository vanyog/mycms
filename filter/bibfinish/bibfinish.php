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

function bibfinish($t){ //print_r($GLOBALS['biblio_list']); die();
global $biblio_list;
if(!isset($biblio_list)) return str_replace("BIBLIO_LIST", "No citations found on this page", $t);;
// Съставяне на нов масив с изчистени описания на библ. източници
$na = array();
foreach($biblio_list as $k=>$v) $na[$k] = preg_replace('/\|\d*\|. /', '', strip_tags($v));
// Сортиране на новия масив
$oldLocal = setlocale(LC_COLLATE, 'bg_BG.utf8');
if(is_array($biblio_list)) uasort($na, 'strcasecmp');
setlocale(LC_COLLATE, $oldLocal);
// Вземане само на ключовете от сортирания масив
$nk = array_keys($na);
$rz = '';
foreach($nk as $k) $rz .= "<p id=\"bib$k\">$biblio_list[$k]</p>\n";
$c = 1;
foreach($nk as $k){
  $tt = str_replace('"','&quot;', $na[$k]);
  $t  = str_replace("|$k|", "<a href=\"#bib$k\" title=\"$tt\">$c</a>", $t);
  $rz = str_replace("|$k|", "$c", $rz);
  $c++;
}
$t = str_replace("BIBLIO_LIST", "$rz", $t);
return $t;
}

?>

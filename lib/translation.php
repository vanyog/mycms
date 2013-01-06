<?php

/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

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

// Набор функции за управление съдържанието на различни езици


include_once($idir."lib/f_stored_value.php");
include_once($idir."lib/f_translate.php");

eval(stored_value('languages','$languages = array("bg"=>"Български");'));
$default_language = stored_value('default_language','bg');


$language = getLanguage();

// Връща избрания от посетителя език, като прави различни проверки

function getLanguage(){
global $languages,$default_language;
$l1 = '';                                           // според браузъра
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $l1 = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
$l2 = '';                                           // според бисквитката language
if (isset($_COOKIE['language'])) $l2 = $_COOKIE['language'];
$l3 = '';
if (isset($_GET['lang'])) $l3 = $_GET['lang'];      // според ?lang=xx
$l = $default_language;                             // по подразбиране
if ($l1) $l = $l1;
if ($l2) $l = $l2;
$ks = array_keys($languages); //print_r($ks);
if ($l3){ 
  $l = $l3;
  if (in_array($l,$ks)) setcookie('language',$l3,time()+30*3600*24,'/');
}  // echo "| $l1 | $l2 | $l3 | $l |";// print_r(array_keys($languages));
if (in_array($l,$ks)) return $l;
else return $default_language;
}

// Връща html код за показване на знаменца, за други езици

function flags(){
global $language, $languages, $dont_translate;
$ls = array_keys($languages);
$r = "";
if (!$dont_translate) foreach($ls as $l) if ($l!=$language){
  $u = $_SERVER['REQUEST_URI'];
  $h = '';
  if (strpos($u,'?')){
     $p = strpos($u,'lang=');
     if ($p) $h = substr_replace($u,'lang='.$l,$p,7); 
     else $h = $u.'&lang='.$l;
  }
  else $h = $u.'?lang='.$l;
  $r .= '<a href="'.$h.'">
<img src="/images/flag-'.$l.'.gif" alt="'.$l.'" border="0">
</a>
';
}
return "\n<!--Флагчета за смяна на езика-->$r<!--Край на флагчетата-->\n";
}

?>

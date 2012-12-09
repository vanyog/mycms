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

// Функцията parse_content($cnt) замества елементите <!--$$_XXX_$$--> в стринга $cnt
// със съдържание, генерирано от php скриптове, които се съхраняват в таблица $tn_prefix.'scripts'

function parse_content($cnt){
global $page_options, $page_data, $content_date_time, $body_adds, $page_header, $idir, $adm_pth, $apth;

$l = strlen($cnt);
$str1 = '<!--$$_';
$str2 = '_$$-->';

// Цикъл за заместване на елементите
while ( !(($p0 = strrpos($cnt,$str1))===false) ){

$p1 = $p0 + strlen($str1);
$p2 = strrpos($cnt,$str2); 
$p3 = $p2 + strlen($str2);

$tg = explode('_',substr($cnt,$p1,$p2-$p1),2);

$tx = ''; // Html код, който ще замести елемента

// Четене на скрипта с име $tg[0] от таблица $tn_prefix.'scripts'
$sc = db_select_1('*','scripts',"`name`='".$tg[0]."'");

if (!$sc){
  $f = strtolower($tg[0]);
  $fn = "$f/f_$f.php";
  if (file_exists("$apth$fn")){
    $c = "include('$fn');\n";
    if (isset($tg[1])) $c .= '$tx = '."$f('$tg[1]');";
    else $c .= '$tx = '."$f();";
    eval($c);
  }
  else {
    if (show_adm_links()) $tx = '<p>Can\'t parse content <a href="'.$adm_pth.'new_record.php?t=scripts&name='.$tg[0].'">'.$tg[0].'</a></p>';
    else $tx = '<p>Can\'t parse content '.$tg[0].'</p>';
  }
}
else eval(stripslashes($sc['script']));

$cnt = substr_replace($cnt,$tx,$p0,$p3-$p0);

} // Край на цикъла за попълване на елементите

return $cnt;

} // Край на функцията parce_content()

?>

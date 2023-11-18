<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Този файл показва линкове към всички страници на сайта или списък на неизползваните номера на страници
// При параметър $_GET['u']=1 се показва списък на неизползваните номера на страници
// При параметър $_GET['lang']=xx се показват само страниците на език xx
// При параметър $_GET['t']=1 се показват и заглавията на страниците

$idir = dirname(dirname(__FILE__)).'/';
$ddir = $idir;

//include_once($idir.'conf_paths.php');
//include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_encode.php');
//include_once($idir.'lib/f_db_table_field.php');
//include_once($idir.'lib/f_set_self_query_var.php');

$page_content = '';

// Максималния използван номер на страница
$ma = db_table_field('MAX(`ID`)', 'pages', 1);

// Данни на всички страници
$pd = db_select_m('ID,content,title,hidden','pages','1 ORDER BY `ID` ASC');

// Показване само на списък на неизползваните номера на страници
if (isset($_GET['u']) && ($_GET['u']=='1')){
  $c = 1;
  foreach($pd as $d){
    while ($d['ID']>$c){ $page_content .= '<a href="'.$main_index.'?pid='.$c.'">'.$c.'</a> '; $c++; }
    $c++;
  }

}

// Показване номерата на съществуващите страници - всички или на посочен език с $_GET['lang']=xx
else {

// Език на страниците, които се показват
$ln = '';
$lnl = '';
if (isset($_GET['lang'])) { $ln = $_GET['lang']; $lnl = '&lang='.$ln; }

$rz = ''; // Редове на таблицата с линкове
$c = 0;   // Брой показани линкове
foreach($pd as $p){
  if ($ln) { 
    $cn = db_table_field('text','content',"`name`='".$p['content']."' AND `language`='$ln'");
    if (!$cn) continue;
  }
  $c++;
  if (isset($_GET['t'])&&($_GET['t']=='1')){ 
     $rz .= '<tr><td><a href="'.$pth.'index.php?pid='.$p['ID'].$lnl.'" target="_blank">'.$p['ID']."</a></td>";
     if ($ln) $tt = db_table_field('text','content',"`name`='".$p['title']."' AND `language`='$ln'");
     else $tt = db_table_field('text','content',"`name`='".$p['title']."' AND `language`='$default_language'");
     if($p['hidden']) $tt .= '<td>hidden</td>';
     else $tt .= '<td>&nbsp;</td>';
     $rz .= "<td>$tt</tr>\n";
  }
  else {
    $rz = '<td><a href="'.$pth.'index.php?pid='.$p['ID'].$lnl.'" target="_blank">'.$p['ID'].'</a></td>'.$rz;
    if (!($c % 10)) $rz = "</tr>\n<tr>$rz";
  }
}

$page_content = encode('<p>Всичко страници: '.count($pd).", показани: $c".', неизползвани номера: <a href="'.set_self_query_var('u',1).'">'.($ma-count($pd))."</a></p>
<p><a href=\"".set_self_query_var('t',1)."\">Със заглавия</a></p>
<table><tr>").$rz.'</tr></table>';

}

include($idir.'lib/build_page.php');

?>

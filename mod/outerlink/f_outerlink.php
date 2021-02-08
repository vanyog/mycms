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

// Показване на хипервръзка от модул OUTERLINKS
// Параметърът $a е във формат x:y, или само y,
// където x е номер на външен сървър, от който се извличат данни за връзка,
// а y e номерът на хипервръзката от таблица outer_links

// Адресите на външни сървъри се задават в таблица options под имена:
// outer_links_server_x

// Параметърът $a може да съдържа и текст, отделен от номера с |, който да замени надписа на линка

function outerlink($a){
global $main_index;
$aa = explode('|',$a);
$bb = explode(':',$aa[0]);
if(isset($bb[1])){
  $n = stored_value('outer_links_server_'.$bb[0]);
  if(!$n) die('outer_links_server_'.$bb[0].' server is not defined in options table');
  if(isset($aa[1])) return '<a href="'.$n.'&lid='.$bb[1].'" target="_blank">'.$aa[1].'</a>';
  $u = $n.'&lid='.$bb[1].'&just=data';
  $d =  file_get_contents($u);
  $o =  json_decode($d);
  return '<a href="'.$n.'&lid='.$bb[1].'" target="_blank">'.$o->Title.'</a>';
}
// Четене данните за хипервръзката
$d = db_select_1('*', 'outer_links', "`ID`=".$aa[0] );
if (isset($aa[1])){
    if ($aa[1]=='href') $aa[1] = $d['link'];
}
else $aa[1] = $d['Title'];
$p = stored_value('outer_links_pid');
if(!$p) die('outer_links_pid option is not set');
if(!$d['private'] || in_edit_mode())
  $rz = '<a href="'.$main_index.'?lid='.$aa[0].'&pid='.$p.'" target="_blank" title="'.
         urldecode($d['link']).'">'.$aa[1].'</a>';
else
  $rz = '<span>'.$aa[1].'</span>';
if(!($d['link']>' ')) $rz .= encode('<sup>(колекция връзки)</sup>');
if(in_edit_mode()) $rz .= ' <a href="/index.php?pid='.$p.'&lid='.$d['up'].'">&gt;&gt;</a>';
return $rz;
}

?>

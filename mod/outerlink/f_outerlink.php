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

// Показване на хипервръзка от модул OUTERLINKS, функциониращ на текущия или друг сайт

// Параметърът $a е във формат x:y, или само y,
// където x е номер на външен сървър, от който се извличат данни за връзка,
// а y e номерът на хипервръзката от таблица outer_links на другия сървър

// Адресите на външни сървъри се задават в таблица options под имена:
// outer_links_server_x

// Параметърът $a може да съдържа и текст, отделен от номера с |, който да замени надписа на линка.
// Ако този текст е 'href', вместо надпис върху линка се показва URL-ът на линка.
// Ако започва с # се добавя като сегмент към адреса на линка, след тире - кам надписа му.

function outerlink($a){
global $main_index;
$aa = explode('|',$a);
if(isset($aa[1])) $aa[1] = stripslashes($aa[1]);
$bb = explode(':',$aa[0]);
// Ако е посочен линк от външен сървър
if(isset($bb[1])){
  $n = stored_value('outer_links_server_'.$bb[0]);
  if(!$n) die('outer_links_server_'.$bb[0].' server is not defined in options table');
  die($aa[1]);
  if(isset($aa[1])){
     return '<a href="'.$n.'&lid='.$bb[1].'" target="_blank">'.$aa[1].'</a>';
  }
  $u = $n.'&lid='.$bb[1].'&just=data';
  $d =  file_get_contents($u);
  $o =  json_decode($d);
  return '<a href="'.$n.'&lid='.$bb[1].'" target="_blank">'.$o->Title.'</a>';
}
// Четене данните за хипервръзката
$d = db_select_1('*', 'outer_links', "`ID`=".$aa[0], false );
if(!isset($d['link'])) $d['link'] = '';
$p = stored_value('outer_links_pid');
if(!$d) return '<a href="'.$main_index.'?pid='.$p.'">Link do not exist</a>';
if (isset($aa[1])){
    if ($aa[1][0]=='#') {
        $h = substr($aa[1],1);
        $p .= "&h=$h";
        $aa[1] = $d['Title'].' - '.$h;
    }
    if ($aa[1]=='href') $aa[1] = $d['link'];
}
else $aa[1] = $d['Title'];
if(!$p) die('outer_links_pid option is not set');
if(!$d['private'] || in_edit_mode()){
  $rz = '<a href="'.$main_index.'?lid='.$aa[0].'&pid='.$p.
         '" target="_blank"'.
          ' title="'.urldecode($d['link']).'"';
  if($d['private']) $rz .=  ' style="opacity: 0.5;"';
  $rz .=  '>'.$aa[1].'</a>';
}
else
  $rz = '<span>'.$aa[1].'</span>';
if(!($d['link']>' ')) $rz .= encode('<sup>(колекция връзки)</sup>');
if(in_edit_mode()) $rz .= ' <a href="/index.php?pid='.$p.'&lid='.$d['up'].'">&gt;&gt;</a>';
return $rz;
}

?>

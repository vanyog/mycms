<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2017  Vanyo Georgiev <info@vanyog.com>

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

// Смяна на протокола и домейна

// Функцията проверява дали протоколът на текущата страница е https
// и ако не е зададена настройка 'stop_https' със стойност 0,
// и в текущата страница не се използва модул USERREG,
// пренасочва към същата страница, но по http.

// Освен това, ако е зададена настройка 'prefere_www' със стойност 'yes'
// се прави пренасочване към адрес, започващ с www. ако текущия адрес не започва така.
// Ако 'prefere_www' има стойност 'no' се пренасочва към адрес без www.
// За да не се променя адреса 'prefere_www' трябва да има стойност, различна от 'yes' и 'no'.

// Параметърът $a е името на съдържанието на текущата страница

function stop_https($a){
global $language, $pth, $ind_fl, $main_index;
$redir = false;
$www = stored_value('prefere_www');
if(($www=='yes')
    && isset($_SERVER['HTTP_HOST'])
    && (substr($_SERVER['HTTP_HOST'],0,4)!='www.')
    && (substr($_SERVER['HTTP_HOST'],0,4)!='xn--')
){
  $_SERVER['HTTP_HOST'] = 'www.'.$_SERVER['HTTP_HOST'];
  $redir = true;
}
if(($www=='no') && isset($_SERVER['HTTP_HOST']) && (substr($_SERVER['HTTP_HOST'],0,4)=='www.') ){
  $_SERVER['HTTP_HOST'] = substr($_SERVER['HTTP_HOST'],4);
  $redir = true;
}
$stps = stored_value('stop_https', 1);
if($stps && isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')){
  $cnt = db_table_field('text', 'content', "`name`='$a' AND `language`='$language'");
  $pos = strpos($cnt, '$$_USERREG_');
  if($pos===false){
    $redir = true;
  }
}
$l = strlen($pth);
//die("$l $ind_fl -- $main_index -- $pth<p>");
if( ($l>1) && ($ind_fl!=$main_index) && (substr($main_index,0,$l)!=$pth) ){
  $l1 = strlen($_SERVER['REQUEST_URI']);
  $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], $l-1, $l1-$l+1);
  $redir = true;
//  die('|'.$_SERVER['REQUEST_URI'].'|');
}
if( $redir && isset($_SERVER['HTTP_HOST']) ) {
  $h = 'Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; die($h);
  header($h);
  die();
}
else return "";
}

?>

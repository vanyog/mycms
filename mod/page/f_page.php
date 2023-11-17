<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Линк към страница с номер $a
// Текстът върху линка е заглавието на страницата, освен ко не е зададен друг текст,
// отделан със знак | след номера в параметъра $a.

function page($a){//die($a);
$aa = explode('|', $a);
global $main_index, $page_hash;
$rz = '';
if(isset($aa[1]))
    $rz .= '<a href="'.$main_index.'?pid='.$aa[0].'">'.$aa[1].'</a>';
else {
    $b = explode('#',$aa[0]);
    if(!is_numeric($b[0])) die("Incorrect parameter '".$b[0]."' for PAGE module");
    $pd = db_select_1('*', 'pages', 'ID='.$b[0]);
    $t2 = '';
    if(!empty($b[1])){
       $t = translate($pd['content']);
       $m = array();
       $i = preg_match_all('/id="'.$b[1].'".*?>(.*)</', $t, $m);
       if(!empty($m[1][0])) $t2 = " - ".$m[1][0];
       $b[1] = '#'.$b[1];
    }
    else $b[1] = $page_hash;
    $rz .= '<a href="'.$main_index."?pid=$b[0]$b[1]\">".translate($pd['title'],false).$t2.'</a>';
}
return $rz;
}

?>
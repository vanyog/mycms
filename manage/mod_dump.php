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

// Чете надписите с имена, започващи с $_GET['p'] и съставя
// content.sql файл в директория 'mod'.$_GET['m'], с който 
// надписите да се импортират в таблица $tn_prefix.'content'
// при инсталиране на модула

$idir = dirname(dirname(__FILE__)).'/';

include($idir.'lib/f_db_select_m.php');

$d = db_select_m('*','content',"`name` LIKE '".$_GET['p']."%'");

$q = "INSERT INTO `content` (`name`,`date_time_1`,`date_time_2`,`language`,`text`) VALUES
(";

foreach($d as $i=>$a){
 $q .= "'".$a['name']."',NOW(),NOW(),'".$a['language']."','".str_replace("\r\n",'\r\n',$a['text'])."'";
 if ($i==count($d)-1) $q .= ");"; else $q .= "),\n(";
}

$fn = $idir.'mod/'.$_GET['m'].'/tables.sql';

$f = fopen($fn,"w");
fwrite($f,iconv('cp1251','UTF-8',$q));
fclose($f);

echo $q;

?>

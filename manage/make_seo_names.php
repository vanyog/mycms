<?php

/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2018  Vanyo Georgiev <info@vanyog.com>

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

// Скриптът генерира SEO подходящи имена на страниците и ги записва в таблица 'seo_names'.
// Трябва да се използва само веднъж, след което трябва да се зададат настроиките:
// 'RewriteEngine', 'SEO_names' и 'redir_pids', както и да се добавят редове във .htaccess.

$idir = dirname(__DIR__).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_first_unicode.php');
include_once($idir.'lib/f_db_insert_m.php');

// Четене номерата и заглавията на всички страници
$pd = db_select_m('A.ID,B.text', '`'.$tn_prefix.'pages` A, `'.$tn_prefix.'content` B', "A.title = B.name AND B.language = 'bg'", false);

// Нов масив със записи за таблица 'seo_names'
$nd = array();

// Масив с ключове новите имена, с цел проверяване за съвпадения
$nn = array();

// Номер, който се добавя, ако има съвпадение на имена
$number = 1;

foreach($pd as $d){
  $n2 = str_replace(' ', '-', transliterate_text($d['text']));
  if(isset($nn[$n2])){
     echo "Conflict of: ".$d['ID'].' - '.$n2."<br>".
          "Renamed to: ".$nn[$n2];
          $n2 .= "-$number";
          $number++; 
     echo " - $n2<br>\n";
  }
  $nn[$n2] = $d['ID'];
  $nd[] = array('ID' => $d['ID'], 'seo_name' => $n2 );
}

echo db_insert_m($nd, 'seo_names', true)." records replaced.";


function transliterate_text($tx){
$tx = strip_tags($tx);
$tx = str_replace("\n"," ",$tx);
$tx = str_replace("\r","",$tx);
global $site_encoding, $utf8_char_lenght;
$tb = array(
1040 => 'A', 1041 => 'B', 1042 => 'V', 1043 => 'G', 1044 => 'D',
1045 => 'E', 1046 => 'Zh', 1047 => 'Z', 1048 => 'I', 1049 => 'Y',
1050 => 'K', 1051 => 'L', 1052 => 'M', 1053 => 'N', 1054 => 'O',
1055 => 'P', 1056 => 'R', 1057 => 'S', 1058 => 'T', 1059 => 'U',
1060 => 'F', 1061 => 'H', 1062 => 'Ts', 1063 => 'Ch', 1064 => 'Sh',
1065 => 'Sht', 1066 => 'A', 1068 => 'Y', 
1070 => 'Yu', 1071 => 'Ya', 1072 => 'a', 1073 => 'b', 1074 => 'v',
1075 => 'g', 1076 => 'd', 1077 => 'e', 1078 => 'zh', 1079 => 'z',
1080 => 'i', 1081 => 'y', 1082 => 'k', 1083 => 'l', 1084 => 'm',
1085 => 'n', 1086 => 'o', 1087 => 'p', 1088 => 'r', 1089 => 's',
1090 => 't', 1091 => 'u', 1092 => 'f', 1093 => 'h', 1094 => 'ts',
1095 => 'ch', 1096 => 'sh', 1097 => 'sht', 1098 => 'a',
1100 => 'y', 1102 => 'yu', 1103 => 'ya');
$tx = iconv($site_encoding, 'UTF-8', $tx);
$p = 0;
$t1 = '';
$l = strlen($tx);
while($p<$l){
  $t = substr($tx, $p, $l-$p);
  $c = first_unicode($t);
  if($c===0) break;
  if(isset($tb[$c])) $t1 .= $tb[$c];
  else if( (($c>64)&&($c<91)) || (($c>96)&&($c<123)) || ($c==45) || ($c==32)) 
           $t1 .= substr($t, 0, $utf8_char_lenght);
  $p += $utf8_char_lenght;
}
return "$t1";
}

?>
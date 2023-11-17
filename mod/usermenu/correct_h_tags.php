<?php
/* 
VanyoG CMS - a simple Content Management System
Copyright (C) 2020 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Коригиране на тагове <h1>, <h2>... в страница $_GET['p']

if (!isset($_GET['p'])) die('Insufficient parameters.');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include("f_usermenu.php");

include_once($idir."conf_paths.php");
include_once($idir."lib/translation.php");
include_once($idir."lib/f_db_insert_1.php");
include_once($idir."lib/f_db_insert_m.php");
include_once($idir."lib/f_db_update_record.php");
//include_once($idir."lib/o_form.php");

// Номер на страницата, в което се коригират заглавията
$page_id = 1*$_GET['p'];

// Данни за страницата
$page_data = db_select_1('*','pages',"`ID`=$page_id");

// Съдържание на страницата, от която е изпратена заявката
$cr = db_select_1('ID,text', 'content',"`name`='".$page_data['content']."' AND `language`='$language'",'');
$pc = $cr['text'];
if(empty($pc)) die('No page content was read.');

// Намиране на всички заглавия
$m1 = array();
$r1 = preg_match_all('/<(h.).*?>(.*?)<\/\1(>)/s', $pc, $m1, PREG_OFFSET_CAPTURE);

// Най-главно заглавие
$mh = "h9";
foreach($m1[1] as $m){
  if($m[0]<$mh) $mh = $m[0];
}

// Разлика между най-главното заглавие и <h2>
$df = $mh[1] - 2;

// Масив с индекси, старите тагове на заглавия и стойности - новите тагова
$nh = array();

foreach($m1[1] as $m){
  $nh[$m[0]] = 'h'.($m[0][1]-$df);
}

// Извършване на преобразуването
$p = 0; // Текуща позиция
$rz = ''; // Преобразуван html код
foreach($m1[1] as $i=>$m){
  $nt = substr($pc,$p,$m1[3][$i][1]-$p-strlen($m1[0][$i][0])+2).
         $nh[$m[0]].
         substr($m1[0][$i][0], 3, strlen($m1[0][$i][0])-6).$nh[$m[0]].'>';
  $p = $m1[3][$i][1]+1;
  $rz .= $nt;
}
$rz .= substr($pc,$p,$m1[3][$i][1]-$p);

$cr['text'] = $rz;
db_update_record($cr, 'content');

header('Location: '.$_SERVER['HTTP_REFERER']);

?>

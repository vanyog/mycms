<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2020  Vanyo Georgiev <info@vanyog.com>

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

// Определя броя на страниците на pdf файловете с пълните текстови и ги записва в таблица proceedings

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_update_record.php');

$pd = db_select_m('ID,pages,fulltextfile2', 'proceedings', "`utype`='vsu2020' AND `publish`='yes' AND `fulltextfile2`>' '");


foreach($pd as $i => $d){
  $fn = '/home/confo2fl/public_html/_vsu2020/'.$d['fulltextfile2'];
//  $fn = '/Users/vanyog/Sites/nauchna.vsu.bg/_vsu2020/'.$d['fulltextfile2'];
  if(!file_exists($fn)) die("File not exists: $fn");
  $pd[$i]['pages'] = getNumPagesPdf($fn);
}

foreach($pd as $d){
  unset($d['fulltextfile2']);
  db_update_record($d, 'proceedings');
}

//echo(print_r($pd,true));
echo count($pd).' records updated';




// Функция, която чете броя страници на pdf файл
// https://stackoverflow.com/questions/1143841/count-the-number-of-pages-in-a-pdf-in-only-php/2314086

function getNumPagesPdf($filepath) {
    $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
    $max = 0;
    if (!$fp) {
        return "Could not open file: $filepath";
    } else {
        while (!@feof($fp)) {
            $line = @fgets($fp, 255);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                preg_match('/[0-9]+/', $matches[0], $matches2);
                if ($max < $matches2[0]) {
                    $max = trim($matches2[0]);
                    break;
                }
            }
        }
        @fclose($fp);
    }

    return $max;
}

?>
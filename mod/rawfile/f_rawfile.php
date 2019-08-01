<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// Вмъкване на "суров" html код от файл или URL
// $a е относителен спрямо $_SERVER['DOCUMENT_ROOT'] път до файла или URL

// Глобални променливи, променят действието на функцията:
// $rowfile_minify=='YES' - предизвиква премахване на празни редове
// $rowfile_noiconv=='YES' - изключва променянето на кодовата таблица

include_once(__DIR__.'/../../lib/f_encode.php');

function rawfile($a = ''){
global $apth, $rowfile_minify, $rowfile_noiconv;
$rz = $apth.$a;
if(empty($apth)) $rz = $_SERVER['DOCUMENT_ROOT'].'/'.$a;
if(!file_exists($rz)) die("File not found '$rz' by RAWFILE module");
$fc = file_get_contents($rz);
if(!(!empty($rowfile_noiconv)&&($rowfile_noiconv=='YES'))) $fc = encode($fc);
if(!empty($rowfile_minify) && ($rowfile_minify=='YES'))
     return preg_replace( array('/\/\/.*\n/', '/ {2,}/', '/\/\*.*?\*\//', '/\n{2,}/'),
                          array("\n",         ' ',       '',              "\n"),
                          $fc
                        );
else return $fc;
}

?>

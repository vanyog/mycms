<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2021  Vanyo Georgiev <info@vanyog.com>

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

// Нареждане на думите от файловете с думи по нарастване на броя на буквите

include('lib.php');

header("Content-Type: text/html; charset=windows-1251");

foreach(array_slice($lfile, 2) as $f){
    // Има на файл с думи
	$l = strlen($f);
	$fr = $_SERVER['CONTEXT_DOCUMENT_ROOT'].'/_files/1klas/';
	$fn = 'a';
	for($i=1; $i<4-$l; $i++) $fn .= '-';
	$fn .= "$f.txt";
	// Четене на файла
	$fc = file_get_contents($fr.$fn);
	// Разделяне на редове
	$ln = explode("\n",$fc);
	// Масив с дължините на думите
	$wl = array();
	foreach($ln as $w) if(!empty(trim($w))) $wl[trim($w)] = strlen(trim($w));
	// Сортиране на масива
	asort($wl);
	// Запис
	file_put_contents("$fr/words/$fn", implode("\n", array_keys($wl)));
	echo "$fn<br>";
}

?>
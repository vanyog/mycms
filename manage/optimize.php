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

// Оптимизиране на изображения. Може да се използва само на локален сървър.
// Всъщност, само генерира команда, кояото трабва да се изпълни от терминал, за да се оптимизира посоченото изображение.

if(!isset($_GET['f'])) die("No file is specified to be optimized");

$idir = dirname(__DIR__).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

$e = strtolower(pathinfo($_GET['f'], PATHINFO_EXTENSION));
$fe = str_replace(' ', '\ ', $_GET['f']);

switch ($e){
case 'png': $f = "/usr/local/bin/mogrify -strip ".$idir.$fe;
            echo passthru("$f");
            echo $f;
            break;
case 'jpg' :
case 'jpeg':$f = "/usr/local/bin/mogrify -sampling-factor 4:2:0 -strip -quality 85 -interlace JPEG -colorspace sRGB ".$idir.$fe;
            echo passthru("$f");
            echo $f;
            break;
case 'gif': $f = "/opt/local/bin/convert ".$idir.$_GET['f']." -strip ".dirname($idir.$_GET['f']).'/'.pathinfo($fe, PATHINFO_FILENAME).'.png';
            echo passthru("$f");
            echo $f;
            break;
default: echo $e;
}

?>
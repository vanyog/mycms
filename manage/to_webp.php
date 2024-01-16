<?php

/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// Конвертиране на изображения към webp формат.
// $_GET['f'] - път до файла с изображението, спрямо кореновата директория на сайта.
// Ако името не файла се намира в таблица 'files', се заменя с файла с раширение .webp

if(!isset($_GET['f'])) die("No file is specified to be optimized");

$idir = dirname(__DIR__).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

// Разширение на файла за конвертиране
$e = strtolower(pathinfo($_GET['f'], PATHINFO_EXTENSION));
// Име на файла за конвертиране с раазширението
$f = pathinfo($_GET['f'], PATHINFO_BASENAME);

// Път до файла, за конвертиране
$fn = "$idir".$_GET['f'];
// Път до .webp файла, който ще се създаде
$nfn = dirname($fn).'/'.pathinfo($fn,PATHINFO_FILENAME).'.webp';

switch ($e){
case 'jpeg':
case 'jpg': $origin=imagecreatefromjpeg($fn); break;
case 'png': $origin=imagecreatefrompng($fn);  break;
default: die('Uknown file extension '.$e);
}
$w=imagesx($origin);
$h=imagesy($origin);
$webp=imagecreatetruecolor($w,$h);
imagecopy($webp,$origin,0,0,0,0,$w,$h);
imagewebp($webp, $nfn, 80);
imagedestroy($origin);
imagedestroy($webp);

// Замяна на разширението ако файлът се използва в таблица 'files'
mysqli_query($db_link,
    "UPDATE `$tn_prefix"."files` SET `filename`=REPLACE(`filename`, '.png', '.webp') ".
    "WHERE `filename` LIKE '%$f';");

header("Location: ".$_SERVER['HTTP_REFERER']);

?>
<?php
/*
MyCMS - a simple Content Management System
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

include_once(__DIR__.'/lib.php');

function randombgword(){
global $page_id;
// Файл от който се чете дума
if(!isset($_COOKIE['limit_of_letters_to_learn'])) $lm = 3;
else $lm = $_COOKIE['limit_of_letters_to_learn'];
if($lm<3) $lm=3;
if(!session_id()) session_start();
$fn = lfile_name($lm-1);
// Номер на реда, който се прочита
if(!isset($_SESSION['last_random'])) $_SESSION['last_random'] = 0;
$c = 'correct'.$page_id; $e = 'errors'.$page_id;
$rc = 0;
if(isset($_COOKIE[$c])) $rc += $_COOKIE[$c];
if(isset($_COOKIE[$e])) $rc += $_COOKIE[$e];
if($rc==0) $_SESSION['last_random'] = 0;
$rz = iconv('windows-1251', 'utf-8', line_of_file($_SESSION['last_random'], $fn) );
$_SESSION['last_random']++;
return $rz;
}

function lfile_name($lm){
global $lfile;
if($lm>29) $lm = 29;
$l = strlen($lfile[$lm]);
$fn = $_SERVER['CONTEXT_DOCUMENT_ROOT'].'/_files/1klas/words/a';
for($i=1; $i<4-$l; $i++) $fn .= '-';
$fn .= $lfile[$lm].".txt";
return $fn;
}

function line_of_file($i, $fn){
if(!file_exists($fn)) die("File '$fn' do not exists.");
$f = fopen($fn, "r");
$j = -1; 
while(!feof($f) && $j<$i){ $l = fgets($f); $j++; }
fclose($f);
return $l;
}

?>
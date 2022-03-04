<?php

/*
MyCMS - a simple Content Management System
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

// Показва съдържанието на файл с относителен спрямо главната директория на сайта адрес $_GET['file'],
// ако същото име на файл се съдържа в масив $_SESSION['can_view_file']

if(empty($_GET['file'])) die('Not enough parameters.');

$fl = $_GET['file'];
$fpth = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
if($fl[0]!='/') $fpth .= "/";
$fpth .= $fl;
if(!file_exists($fpth)) die('File '.$fpth.' not found.');

session_start(); //var_dump($_SESSION['can_view_file']); die;
if( empty($_SESSION['can_view_file']) || !in_array($fl, $_SESSION['can_view_file']) ) die('You have no permission to view this file.');

$e = strtolower( pathinfo($fpth, PATHINFO_EXTENSION) );

$mt = array(
'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'pdf'=>'application/pdf'
);

$fpth = realpath($fpth);// die($fpth);

$ct = file_get_contents(addslashes($fpth), null, null);
if($ct===false) die("Can't read file $fpth");

header('Content-type: '.(isset($mt[$e]) ? $mt[$e] : ''));
header('Content-Disposition: filename="' . basename ($_GET['file']) . '"');
echo $ct;

?>
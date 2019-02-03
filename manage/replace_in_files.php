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

// Заместване на регулярен израз от всички файлове на директория

// Директория
$dir = '/Users/vanyog/Sites/vanyog.tk/public_html/vgedit';

// Рег. израз какво да се намери
$search = '/<script language="php">\s*(.*)<\/script>/is';

// С какво да се замести
$replace = "<?php\n$1?>";

$text_files = array('php');

if(!file_exists($dir)) die("'$dir' do not exists.");
if(!is_dir($dir)) die("'$dir' is not a directory.");

$d = opendir($dir);

while (($f = readdir($d)) !== false){
    $fn = "$dir/$f";
//    echo "$fn";
    $fe = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
    if (!is_dir($fn) && in_array($fe, $text_files) ) {
        $cn = file_get_contents($fn);
//        $ar = array(); preg_match_all($search, $cn, $ar); die(print_r($ar,true));
        $nc = preg_replace($search, $replace, $cn);
        $cc = strlen($cn)-strlen($nc);
        if(!is_writable($fn)) die("File '$fn' is not writable.");
        if($cc!=0) file_put_contents($fn, $nc);
        echo "$fn - $cc<br>\n";
//        if($cc!=0) die;
    }
//    else echo "<br>\n";
}

?>
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

// Скрипт за анализиране на лог файл

// Абсолютен път и име на лог файла
$logpath = '/Users/vanyog/Sites/physics-bg.org/www/logs/access_log';

if (!file_exists($logpath)) die("File $logpath do not exists.");

// Съдържание на лог файла
$fc = file_get_contents($logpath);
$fl = explode("\n", $fc);

echo '<style>
td {white-space:nowrap;}
</style>
<p>'.count($fl).' requests
';

$rz = '</p><table border="1">
';
$ip = array(); // Брой заявки по IP
$by = array(); // Байтове на IP
$bt = 0;       // Сума байтове
$er = '';      // грешки 404
foreach($fl as $l){
  $le = explode(" ",trim($l),11);
  if (isset($ip[$le[0]])) $ip[$le[0]]++; else $ip[$le[0]] = 1;
  if (isset($by[$le[0]])) $by[$le[0]] += $le[9]; else $by[$le[0]] = 0;
  if (isset($le[9])) $bt += $le[9];
  if (isset($le[8]) && ($le[8]=='404')) $er .= $le[6]."<br>\n";
  $rz .= '<tr>';
  foreach($le as $e){
    $c = 80;
    $rz .= '<td>'.substr($e,0,$c);
    if (strlen($e)>$c) $rz .= '...';
    $rz .= '</td>';
  }
  $rz .= '</tr>'."\n";
}

echo count($ip).' IPs '.(count($fl)/count($ip)).'
 Total '.$bt.' bytes '.($bt/count($fl)).' per IP</p>
<p>'.$er.$rz; 
echo '</table>';


?>

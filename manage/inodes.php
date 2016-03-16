<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

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

// Брой на ай-нодовете в директория $_GET['d']

if (!isset($_GET['d'])) die("Missing parameter d");

$d = $_GET['d'];

if (!is_dir($d)) die($d.' is not a directory');

$dh = opendir($d);

if (!$dh) die('Can\'t open directory '.$d);

echo '<table>
';
while($f = readdir($dh)) if (($f!='..') && ($f!='.') && is_dir("$d/$f")) {
  echo "<tr><td>$f </td><td>".inode_count("$d/$f")."</td></tr>\n";
}
echo '</table>';

closedir($dh);

function inode_count($d){
$rz = 0;
$dh = opendir($d);
if (!$dh) echo 'Can\'t open directory '.$d;
while($f = readdir($dh)) if (($f!='..') && ($f!='.')) {
  if (is_dir("$d/$f")) $rz += inode_count("$d/$f");
  else $rz += 1;
}
closedir($dh);
return $rz;
}

?>

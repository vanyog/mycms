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

function rand_string($n){
$i1 = ord('A');
$i2 = ord('Z');
$rz = '';
for($i=0; $i<$n; $i++){
  if (rand(0,1)) $rz .= strtolower(chr(rand($i1,$i2)));
  else if (rand(0,4)) $rz .= rand(0,9);
       else $rz .= chr(rand($i1,$i2));
}
return $rz;
}

?>

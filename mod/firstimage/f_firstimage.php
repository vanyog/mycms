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

// Молулът връща URL-а на първата картинка, качена с модул uploadfile.

function firstimage($a = ''){
$rz = $a;
if(isset($GLOBALS['og_image']) ){
   $ex = pathinfo($GLOBALS['og_image'], PATHINFO_EXTENSION);
   if($ex=='svg') return $rz;
   $h = 'http://';
   if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on') ) $h = 'https://';
   $rz = $h.$_SERVER['HTTP_HOST'].$GLOBALS['og_image'];
}
return $rz;
}

?>

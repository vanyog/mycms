<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2022  Vanyo Georgiev <info@vanyog.com>

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

// Модул, който осигурява правилно показване на предназначени за различни устройства икони на сайта.

function siteicons(){
global $adm_pth;
$icon_path = stored_value('icon_path');
if(empty($icon_path)){
  die("<p>Icon path is not specified.<p>".
'<p>Prepare an 310x310px PNG image for your icons. '.
'Generate Icons from this image by <a href="https://realfavicongenerator.net/" target="blank">realfavicongenerator.net</a>. '.
'Upload generated files in a directory on your serever. '.
'Specify thies path in "<a href="'.$adm_pth.
'new_record.php?t=options&name=icon_path">icon_path</a>" option.</p>');
}
$rz = '<link rel="apple-touch-icon" sizes="180x180" href="'.$icon_path.'apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="'.$icon_path.'favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="'.$icon_path.'favicon-16x16.png">
<link rel="manifest" href="'.$icon_path.'site.webmanifest">
<link rel="mask-icon" href="'.$icon_path.'safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">';
return $rz;
}

?>
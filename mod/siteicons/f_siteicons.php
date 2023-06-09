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
$icon_path = stored_value('icon_path', '/favicon_package_v0.16/');
if(!file_exists($_SERVER['DOCUMENT_ROOT'].$icon_path)){
  die("<p>Icon path is not specified.<p>".
'<p>Prepare an 310x310px PNG image for your icons. '.
'Generate Icons from this image by <a href="https://realfavicongenerator.net/" target="blank">realfavicongenerator.net</a>. '.
'Upload generated files in a directory on your serever. '.
'Specify thies path in "<a href="'.$adm_pth.
'new_record.php?t=options&name=icon_path">icon_path</a>" option.</p>');
}
$fc = file_get_contents($_SERVER['DOCUMENT_ROOT'].$icon_path.'site.webmanifest');
$fc = str_replace('"/android-chrome-', '"'.$icon_path.'android-chrome-', $fc);
file_put_contents($_SERVER['DOCUMENT_ROOT'].$icon_path.'site.webmanifest', $fc);
$fc = file_get_contents($_SERVER['DOCUMENT_ROOT'].$icon_path.'browserconfig.xml');
$fc = str_replace('src="/mstile-150x150.png"', 'src="'.$icon_path.'mstile-150x150.png"', $fc);
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/browserconfig.xml', $fc);
//die($fc);
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
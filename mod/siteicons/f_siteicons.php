<?php
/*
VanyoG CMS - a simple Content Management System
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
// За да поставите икони на своя сайт:
// - Пригответе квадратно изображение с размери поне 300x300 px
// - Генерирайте от това изображение комплект икони с https://realfavicongenerator.net
// - Поставете генерираната директория с изображения-икони на сайта
// - Отворете еднократно главната страница на сайта с добавен параметър icons=set.
// - Проверете резултата прз лика favicon check, който се вижда отдолу на всяка страница в режим на администриране

function siteicons(){
global $adm_pth;
$icon_apath = dirname(dirname(__DIR__)).'/favicon_package_v0.16/';
$icon_path = current_pth(dirname(__DIR__)).'favicon_package_v0.16/';
if(!file_exists($icon_apath)){
  die("<p>Icon path is not prepared.<p>".
'<p>Create a 310x310px PNG image for your icons. '.
'Generate Icons from this image by <a href="https://realfavicongenerator.net/" target="blank">realfavicongenerator.net</a>. '.
'Upload generated folder \'favicon_package_v0.16 2\' in the root directory on your site. ');
}
if(isset($_GET['icons']) && ($_GET['icons']=='set')){
// Коригиране съдържанието на файл site.webmanifest
$fc = file_get_contents($icon_apath.'site.webmanifest');
$fc = str_replace('"/android-chrome-', '"'.$icon_path.'android-chrome-', $fc);
file_put_contents($icon_apath.'site.webmanifest', $fc);
// Коригиране съдържанието на файл browserconfig.xml
$fc = file_get_contents($icon_apath.'browserconfig.xml');
$fc = str_replace('src="/mstile-150x150.png"', 'src="'.$icon_path.'mstile-150x150.png"', $fc);
file_put_contents(dirname(dirname(__DIR__)).'/browserconfig.xml', $fc);
//Копиране на файлове favicon.ico и apple-touch-icon.png в главната директория
copy($icon_apath.'favicon.ico', dirname($icon_apath).'favicon.ico');
copy($icon_apath.'apple-touch-icon.png', dirname($icon_apath).'apple-touch-icon.png');
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
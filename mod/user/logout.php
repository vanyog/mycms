<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Страница, която се показва при излизане на потребителя, ако не е зададена друга.

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir."lib/translation.php");

$page_title = translate('user_logouttitle');
$page_content = "<h1>$page_title</h1>\n".translate('user_logoutcontent').'
<a href="'.$pth.'">'.translate('home_page_title',false).'</a>';

include($idir."lib/build_page.php");

?>

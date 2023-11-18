<?php
/*
VanyoG CMS - a simple Content Management System
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

if (isset($_COOKIE['PHPSESSID'])) session_start();

// Номер на началната страница на сайта
$i = stored_value('main_index_pageid',1);

// Име на заглавието на началната страница
$n = db_table_field('title','pages',"`ID`=$i");

// Премахване на бисквитката за режим на редактиране
setcookie( stored_value('edit_name','edit'), '0', time()+60*60*24*30, '/');

// Премахване на параметъра за режим на редактиране
$pr = stored_value('edit_name','edit').'='.stored_value('edit_value','on');
if (isset($_SESSION['user_returnpage'])) $h = str_replace($pr,'',$_SESSION['user_returnpage']);

$page_title = translate('user_logouttitle');

$page_content = "<h1>$page_title</h1>\n".translate('user_logoutcontent').'
<p>'.translate('user_backto').'<br>
'.translate('user_homepage').'
<a href="'.$pth.'index.php?pid='.$i.'">'.strip_tags(translate($n,false)).'</a><br>
';
if (isset($_SESSION['user_returnpage'])) $page_content .= '<a href="'.$h.'">'.translate('user_lastpage').'</a>';
$page_content .= '</p>';

include($idir."lib/build_page.php");

?>

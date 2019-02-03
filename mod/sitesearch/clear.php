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

// Унищожава променлива $_SESSION['text_to_search'] и се връща на страницата,
// от която е извикан

session_start();
unset($_SESSION['text_to_search']);
unset($_SESSION['sitesearch_saved']);
if (!count($_SESSION)) setcookie('PHPSESSID','',time()-60,'/');
if (isset($_SERVER['HTTP_REFERER'])) header('Location: '.$_SERVER['HTTP_REFERER']);
else echo("'text_to_search' variable have been unset.");

?>

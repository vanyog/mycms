<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Изтриване на собствения потребителски профил

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

if (!session_id() && isset($_COOKIE['PHPSESSID'])) session_start();

if ( !empty($_SESSION['user_username']) && !empty($_SESSION['user_password']) ){

    include_once($idir.'lib/f_stored_value.php');
    include_once($idir.'lib/f_db_delete_where.php');

    // Таблица с данни за потребителите
    $user_table = stored_value('user_table', 'users');

    db_delete_where($user_table, "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
    unset($_SESSION['user_username']);
    unset($_SESSION['user_password']);

}

if(isset($_SERVER['HTTP_REFERER'])) header("Location: ".$_SERVER['HTTP_REFERER'] );

?>
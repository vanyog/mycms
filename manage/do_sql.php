<<<<<<< HEAD
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

// Изпълнява SQL заявка

include("conf_manage.php");
include($idir."lib/usedatabase.php");

$q = $_POST['sql'];

$q = str_replace('INSERT INTO `scripts`',"INSERT INTO `$tn_prefix"."scripts`",$q);
if (!mysql_query($q,$db_link)) die("MySQL query error");

header('Location: edit_data.php');

?>
=======
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

// Изпълнява SQL заявка

include("conf_manage.php");
include($idir."lib/usedatabase.php");

$q = $_POST['sql'];

$q = str_replace('INSERT INTO `scripts`',"INSERT INTO `$tn_prefix"."scripts`",$q);
if (!mysql_query($q,$db_link)) die("MySQL query error");

header('Location: edit_data.php');

?>
>>>>>>> a72a4be4ffa9a80348d1fd87624a3a35be02a861

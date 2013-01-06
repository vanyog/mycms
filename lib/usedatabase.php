<script language="php">

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

// Този файл инициализира променливата $db_link
// която се използва в mysql_query($q,$db_link);

if (!file_exists($idir."conf_database.php")) die("Database is not configured");

include($idir."conf_database.php");

$db_link = get_db_link($user, $password, $database);

function get_db_link($user, $password, $database){
$l = mysql_connect("localhost",$user,$password);
if (!$l){
 echo '<p>Не се получава връзка с MySQL сървъра!'; die;
}
if (!mysql_select_db($database,$l)){
 echo '<P>Не може да бъде избрана база данни.'; die;
}
mysql_query("SET NAMES 'cp1251';",$l);
//mysql_query("SET CHARACTER SET 'cp1251';",$l);
return $l;
}

</script>

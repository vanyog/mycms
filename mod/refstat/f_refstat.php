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

// Регистриране от кои външни страници идват посетители на текущата страница
// Данните се запазват в таблица $tn_prefix.'refstat'

function refstat(){

// Ако няма HTTP_REFERER - край
if (!isset($_SERVER['HTTP_REFERER'])) return;

$r = $_SERVER['HTTP_REFERER'];

// Избягване на някои адреси

//if (preg_match('/^https:\/\//i', $r)) return;
//if (preg_match('/facebook.com\/l.php/i', $r)) return;
//if (preg_match('/mail/i', $r)) return;
//if (preg_match('/google\.[a-zA-Z]{2,}\/url\?/i', $r)) return;
//if (preg_match('/scottkj/i', $r)) return;
//if (preg_match('/my.vsu.bg\/moodle/i', $r)) $r = 'http://my.vsu.bg/moodle';

$u = parse_url($r);

// Ако препртката е от същия сайт - край
if (in_array($u['host'],array($_SERVER['HTTP_HOST'],stored_value('host_local') ))) return;

$r = addslashes($r);

global $page_id, $tn_prefix, $db_link;

// Номер на запис, ако съществува
$id = db_table_field('ID', 'refstat', "`page_id`='$page_id' AND `referer`='$r'");
// Ако записът съществува той се опреснява,
if ($id){ $q1 = "UPDATE `$tn_prefix"."refstat` SET "; $q2 = " WHERE `ID`=$id;"; }
// ако не съществува - се вмъква
else { $q1 = "INSERT INTO `$tn_prefix"."refstat` SET `date_time_1`=NOW(), "; $q2 = ';'; }
$q = $q1."`date_time_2`=NOW(), `page_id`='$page_id', `referer`='$r', `count`=`count`+1, `IP`='"
        .$_SERVER['REMOTE_ADDR']."', `agent`='".$_SERVER['HTTP_USER_AGENT']."'".$q2;
mysqli_query($db_link,$q);
}

?>

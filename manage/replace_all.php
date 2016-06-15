<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2014  Vanyo Georgiev <info@vanyog.com>

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

// Този скрипт търси стринг $_GET['s1'] и го замества със стринг $_GET['s2'] в
// полето $_GET['f'] на всички записи от таблица $_GET['t'] на базата данни.

include('conf_manage.php');
include_once($idir.'lib/f_db_replace_all.php');

// Ако липсва някой от параметрите изпълнението спира
if (!isset($_GET['s1'])) die('No string to replace s1= is specified.');
if (!isset($_GET['s2'])) die('No string to replace width s2= is specified.');
if (!isset($_GET['f'])) die('No field name f= is specified.');
if (!isset($_GET['t'])) die('No database table name t= is specified.');

$s1 = addslashes($_GET['s1']);
$s2 = addslashes($_GET['s2']);
$f  = $_GET['f'];
$t  = $_GET['t'];

$r = db_replace_all($s1, $s2, $f, $t);

echo $r[0].' raplacements made in '.$r[1].' recordr.';

?>

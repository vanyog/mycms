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

// Настройки за достъп до MySQL базата данни

$colation = 'utf8'; // Кодировка в която трябва да се извличат данните от базата
$database ='dbname'; // Име на базата данни
$user     ='dbuser'; // Потребителско име
$password ='dbpass'; // Парола

$tn_prefix = ''; // С тази представка, трябва да започват имената на теблиците, предназначени за системата.
// Във функциите за извличане на данни, имената на таблиците се посочват без представка,
// защото тези функции я добавят.

?>

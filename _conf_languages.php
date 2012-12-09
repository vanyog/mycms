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

// Масив с езиците, на които се показва съдържание
// Ключовете на масива са кратки идентификатори на езика
// Стойностите - имена, с които се показват езиците

$languages = array('bg' => 'Български' /*, 'en' => 'English'*/ );

// Език по подразбиране
// Всеки текст трябва да е въведен поне на този език
// Ако някой текст не е преведен на друг език, се показва на езика по подразбиране
$default_language = 'bg';

?>

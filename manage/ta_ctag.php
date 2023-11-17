<?php

/*
VanyoG CMS - a simple Content Management System
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

// Ако в някой файл се съдържа затварящ таг /textarea това нарушава структурата на страницата edit_file.php.
// За да стане възможно редактирането на такива файлове в  edit_file.php, преди отварянето им този таг се заменя с неправилно изписан такъв,
// а при записване на файла със save_file.php неправилния таг се заменя отново с правилен.
// За да е възможно edit_file.php да редактира себе си в него таг /textarea не се изписва явно, а като променлива, декларирана тук.

$ta_ctag = '</textarea>';
$ta_fctag = '< /textarea>';

?>

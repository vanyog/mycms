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

// Показва линкове към всички страници на сайта

include('conf_manage.php');
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');

$ids = db_select_m('ID','pages','1 ORDER BY `ID` DESC');

$page_content = '<table><tr>';

$c = 0;
foreach($ids as $id){
  $c++;
  $page_content .= '<td><a href="'.$pth.'index.php?pid='.$id['ID'].'" target="_blank">'.$id['ID'].'</a></td>';
  if (!($c % 10)) $page_content .= "</tr>\n<tr>";
}

$page_content .= '</tr></table>';

include('build_page.php');

?>

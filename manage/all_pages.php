<?php
/*
VanyoG CMS - a simple Content Management System
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

// Показва линкове към всички страници на сайта

include('conf_manage.php');
include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_table_field.php');

$ln = ''; $lnl = '';
if (isset($_GET['lang'])) { $ln = $_GET['lang']; $lnl = '&lang='.$ln; }

$pd = db_select_m('ID,content','pages','1 ORDER BY `ID` ASC');

$page_content = '<table><tr>';

$rz = '';
$c = 0;
foreach($pd as $p){
  if ($ln) { 
    $cn = db_table_field('text','content',"`name`='".$p['content']."' AND `language`='$ln'");
    if (!$cn) continue;
  }
  $c++;
  $rz = '<td><a href="'.$pth.'index.php?pid='.$p['ID'].$lnl.'" target="_blank">'.$p['ID'].'</a></td>'.$rz;
  if (!($c % 10)) $rz = "</tr>\n<tr>$rz";
}

$page_content .= $rz.'</tr></table>';

include('build_page.php');

?>

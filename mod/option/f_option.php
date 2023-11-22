<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// Извеждане на съдържанието на опция с име  $a

function option($a){
global $adm_pth;
$d = db_select_1('ID,value', 'options', "`name`='$a'",''.false);
if(!$d) $rz = ''; else $rz = $d['value'];
if(in_edit_mode()) 
   if(!$d)
     $rz .= "\n<a href=\"$adm_pth/new_record.php?t=options&value=$a\">$a option</a>\n";
   else
     $rz .= "\n<a href=\"$adm_pth/edit_record.php?t=options&r=".$d['ID']."\">$a option</a>\n";
return $rz;
}

?>
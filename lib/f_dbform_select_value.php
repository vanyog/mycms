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

// ��������� �������� html ��� �� ����� ������ �� ����� �� ��������,
// ������� �� � ���� $f �� ������� $tn_prefix.$t �� ������ �����.

include_once($idir.'/lib/f_db_field_values.php');

function dbform_select_value($f,$t,$sl='',$js=''){
$va = db_field_values($f,$t,1);
if ($js) $js = ' onchange="'.$js.'"';
$rz = "<select name=\"$f\"$js>\n";
foreach($va as $v){
 if ($v==$sl) $s = ' SELECTED'; else $s = '';
 $rz .= '<option value="'.$v."\"$s>".$v."</option>\n";
}
$rz .= "</select>\n";
return $rz;
}

?>

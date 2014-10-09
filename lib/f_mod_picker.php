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

// ��������� ������ �� ��������� ������ �� ��-����� ��������

function mod_picker(){
global $page_header;
$page_header .= '<script type="text/javascript"><!--
function toClip(a){
window.prompt("Copy to clipboard: Ctrl+C, Enter", "<!--$$_"+a.innerHTML+"_$$-->" );
}
--></script>
<style type="text/css">
#modbtn span { display:inline-block; width:160px; padding:0 5px; font-size:80%; }
#modbtn span span { display:inline; font-size:100%; padding:0; cursor:default; }
#modbtn span span:hover { background-color:#EEEEEE; } 
#modbtn span a { }
</style>
';
$rz = "<p id=\"modbtn\"><strong>Modules:</strong><br>\n";
$ml = mod_list();
foreach($ml as $m) {
  $rm = $m.'README.txt';
  $rz .= '<span><span onclick="toClip(this);">'.
        strtoupper(pathinfo($m, PATHINFO_FILENAME)).'</span>';
  if (file_exists($rm)) $rz .= ' <a href="'.current_pth($rm).'README.txt">help</a>';
  $rz .= '</span> '."\n";
}
$rz .= '</p>';
return $rz;
}


?>

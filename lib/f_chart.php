<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2016 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// Показване на диаграма

function chart($dt){ //die(print_r($dt, true));
global $page_header;
/*$page_header .= '<style><!--
.chart_barr { background-color:#FF0000; display:inline-block; width:1em; vertical-align:bottom; }
.chart_barr div { transform:rotate(270deg); }
--></style>';*/
$rz = '<div style="margin-top:100px; width:'.(20*count($dt)).'px;">'."\n";
$mi = min($dt);
$ma = max($dt);
if ($mi && ($mi==$ma)) $mi = 0;
//die("$mi $ma");
$v1 = 0;
foreach($dt as $k=>$v){
  $v1 = $v-$v1;
  $rz .= '<div style="width:1em; display:inline-block;">'."\n".
         '<div style="background-color:#FF0000; min-height:'.(300*($v-$mi)/($ma-$mi)+1).'px">'."\n".
         '<div style="transform:rotate(270deg); white-space:nowrap; margin-left:2px;">'.
         $v.' '.( ($v1>0) ? '+'.$v1 : $v1).
         '</div>'.
         '</div>'."\n".
         '<div style="transform:rotate(270deg); width:5em; height:5em;">'.$k."</div>\n".
         "</div>\n";
   $v1 = $v;
}
$rz .= '<div style="clear:both;"></div>
</div>';
return $rz;
}


?>

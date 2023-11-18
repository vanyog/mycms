<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

// Показване датите на създаване и на последната промяна на страницата.

global $ddir;

include_once($ddir.'lib/f_db2user_date_time.php');

function pagedates(){
global $page_data, $language;
$d = db_select_1('ID,date_time_1,date_time_2', 'content', "`name`='".$page_data['content']."' AND `language`='$language'");
if(!$d) { return ''; }
$rz = '<p id="pagedates">'.translate('pagedates_created').
      db2user_date_time($d['date_time_1'], false).
      ' '.translate('pagedates_updated').
      db2user_date_time($d['date_time_2'], false);
if(show_adm_links()){
   $s = db_table_field('time', 'worktime', "`name`='content.".$d['ID']."'");
   if($s){
      $h = floor($s/3600); $s -= 3600*$h;
      $m = floor($s/60);   $s -= 60*$m;
      $rz .= "<br>$h:$m:$s";
   }
}
$rz .= '</p>';
return $rz;
}

?>

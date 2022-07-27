<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2022  Vanyo Georgiev <info@vanyog.com>

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

// Преглед, какво ново през последното денонощие

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_db_table_field.php');
include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_encode.php');

$site_encoding = "UTF-8";
$file_encoding = "Windows-1251";

$page_content = encode('<h1>Новото от миналия ден и днес</h1>'."\n");

$date = date('Y-m-d 00:00:00', time()-24*3600 );

$da = db_select_m('*', 'proceedings', "`date_time_1`>'$date' OR `date_time_2`>'$date'");

$page_content .= encode('<p>Нови или редактирани доклади ').count($da).':</p>'; 

foreach($da as $d){
  $updated = '';
  if ($d['date_time_1']<$date) $updated = encode('<sup>Актуализиран</sup> '); 
  $page_content .= '<p>'.$d['title']."<br>".$d['authors']."</p>\n";
}

$ut = stored_value('conference_usertype');
$secret = stored_value('conference_secret_'.$ut,'basa-team');

$page_content .= '<p>'.encode('Общо доклади: ').
   db_table_field('COUNT(*)', 'proceedings', "`utype`='$ut'").'. '.
   '<a href="'.stored_value('conference_abstracts','/index.php?pid=100').
   "&allowtoshow=$secret\">".encode('Всички')."</a>.</p>\n";

include_once($idir.'lib/build_page.php');

?>
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

include('../f_db_my_tables.php');
include('../f_db_select_1.php');
include('../f_db_field_names.php');
include('../conf_paths.php');

$ts = db_my_tables();

$mids = array();
foreach($ts as $t){
  $fns = db_field_names($t);
  if (in_array('ID',$fns)){
    $mid = db_select_1('ID',$t,'1 ORDER BY ID DESC');
    $mids[$t] = $mid['ID'];
  }
}

$fn = $adm_apth.'dumped_ids.txt';
$f = fopen($fn,'w');
$fc = html_entity_decode(http_build_query($mids));
fwrite($f,$fc); 
fclose($f);

$page_content = '<p>Dump done.</p>';

include("build_page.php");
?>

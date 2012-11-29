<?php
// Copyright: Vanyo Georgiev info@vanyog.com

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

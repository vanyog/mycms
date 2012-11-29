<?php
// Copyright: Vanyo Georgiev info@vanyog.com

include('../f_db_my_tables.php');
include('../f_db_select_1.php');
include('../f_db_select_m.php');
include('../f_db_field_names.php');
include('../conf_paths.php');

error_reporting(E_ALL); 
ini_set('display_errors',1);

$fn = $adm_apth.'dumped_ids.txt';
$fc = file($fn); $fc = $fc[0];

$mids = array();
parse_str($fc,&$mids);

$ts = db_my_tables();

$page_content = '<p><textarea rows="40" cols="150" wrap="OFF">'."\n";
foreach($ts as $t){
  $fns = db_field_names($t);
  if (in_array('ID',$fns)){
    $mid = db_select_1('ID',$t,'1 ORDER BY ID DESC');
    if (!isset($mids[$t])) $mids[$t]=0;
    if ($mid['ID']>$mids[$t]){
     $q = 'INSERT INTO `'.$tn_prefix.$t.'` (';
     foreach($fns as $fn) $q .= "`$fn`, ";
     $q = substr($q,0,strlen($q)-2).") VALUES\n";
     $rs = db_select_m('*',$t,"`ID`>".$mids[$t]);
     foreach($rs as $r){
       $q .= "(";
       foreach($r as $f){
         $v = addslashes($f);
         $v = str_replace("\n",'\n',$v);
         $v = str_replace("\r",'\r',$v);
         $q .= "'$v', ";
       }
       $q = substr($q,0,strlen($q)-2)."),\n";
     }
     $q = substr($q,0,strlen($q)-2).";\n\n";
     $page_content .= $q;
//     print_r($rs); $page_content .= "<br>";
    }
  }
}
$page_content .= '</textarea></p>
<p><a href="dump_done.php">Go Next</a></p>';

include("build_page.php");
?>

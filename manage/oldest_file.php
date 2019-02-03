<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// От всички поддиректории на директория $p се определя датата на най-старите файлове в 
// под-поддиректориите $p/*/$s

$p = '/Users/vanyog/Sites/vanyog.com/subdomains/sci';

$s = '';

function aaa(){
echo "aaa";
}

function oldest_file_time($p){
$d = opendir($p);
$t = time();
while ($n = readdir($d)) if(($n!='.')&&($n!='..')){
  $pt = "$p/$n";
  $tn = filemtime($pt);
  if($tn<$t) $t = $tn;
}
return $t;
}

function foreach_dir($p,$f,$s){
$d = opendir($p);
$rz = array();
while ($n = readdir($d)) if( ($n!='.') && ($n!='..') ){
  $pn = "$p/$n/$s";
  if(is_dir($pn)){
    $rz["$n/$s"] = $f($pn);
  }
}
return $rz;
}

$rz = foreach_dir($p, 'oldest_file_time', $s);

echo "<table>\n";
foreach($rz as $k=>$n) echo "<tr><td>$k</td><td>".date("d.m.Y", $n)."</td></tr>\n";
echo "</table>\n";
?>

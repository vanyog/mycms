<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// В някои от таблиците има поле place, което служи за задаване реда на извличане на записите.
// Това поле съдържа целочислени стойности през 10.
// При необходимост от промяна на реда на извличане се променя полето place на записа, 
// който трябва да се премести. Например, за да се 
// премести между редове с place=20 и place=30, се задава place=25.

// Настоящия скрипт променя отново стойностите на поле place през 10
// и трябва да се използва след като са извършвани премествания.

include("../conf_paths.php");
include("../f_db_select_m.php");

$t = $_GET['t']; // Име на таблицата

// Задаване на стойности през 1
$i = 1;
$r = db_select_m('ID', $t, '1 ORDER BY `place` ASC');
foreach($r as $r1){
  $q = "UPDATE `$tn_prefix$t` SET `place`=$i WHERE ID=".$r1['ID'].";";
  mysql_query($q,$db_link);
  $i++;
}

// Умножаване на стойностите по 10
$q = "UPDATE `$tn_prefix$t` SET `place` = `place` * 10;";
$q = mysql_query($q,$db_link);

// Връщане на страницата, извикала скрипта
header('Location: '.$_SERVER['HTTP_REFERER']);
?>

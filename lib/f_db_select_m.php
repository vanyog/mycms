<script language="php">
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията db_select_m, дефинирана в този файл
// чете полетата $fn на всички записи от таблица $tb,
// удовлетворяващи условието $whr.
// Таблицата е от базата данни, определена от променливата $db_link
// (виж usedatabase.php).
// Функцията връща масив от асоциирани масиви,
// съответстващи на всеки от прочетените записи.
// Ключовете на масива за даден запис са имената на полетата,
// а стойностите - съдържанието на полетата от таблицата.

include_once("usedatabase.php");

function db_select_m($fn,$tb,$whr){
global $db_link, $tn_prefix;
$q="SELECT $fn FROM `$tn_prefix$tb` WHERE $whr;"; //echo "$q<br>";
$dbr=mysql_query($q,$db_link);
$r=array();
if (!$dbr) return $r; 
while ( $rc=mysql_fetch_assoc($dbr) ){
 $r[]=$rc; //print_r($rc);
}
mysql_free_result($dbr);
return $r;
}

</script>

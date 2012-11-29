<script language="php">

// Функцията db_select_1, дефинирана в този файл
// чете полетата $fn на един запис от таблица $tb,
// на базата данни, определена от променливата $db_link
// (виж usedatabase.php).
// Прочетеният запис удовлетворява условието $whr.
// Функцията връща, асоцииран масив, или false при неуспех.
// Ключовете на масива са имената на полетата,
// а стойностите - съдържанието на полетата от таблицата.

include_once("usedatabase.php");

function db_select_1($fn,$tb,$whr){
global $db_link, $tn_prefix;
$q="SELECT $fn FROM `$tn_prefix$tb` WHERE $whr LIMIT 1;";
$r=mysql_query($q,$db_link);
if (!$r) return false;
$rc=mysql_fetch_assoc($r);
mysql_free_result($r);
return $rc;
}

</script>

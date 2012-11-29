<script language="php">

// Функцията db_table_field($fn,$tb,$whr), връща съдържанието на полетато $fn
// на първия, отговарящ на условието $whr запис от таблица $tb.

// Таблицата е от базата данни, определена от променливата $db_link,
// дефинирана в usedatabase.php.

// Ако няма запис отговарящ на условието възниква грешка,
// затова ако не е сигурно, че в таблицата има запис, отговарящ на условието $whr 
// за предпочитане е да се използва функцията db_select_1(),
// която в такъв случай връща false без да се генерира грешка

include_once("usedatabase.php");

function db_table_field($fn,$tb,$whr){
global $db_link,$tn_prefix;
$q="SELECT $fn FROM $tn_prefix$tb WHERE $whr;"; //echo "$q<br>";
$r=mysql_query($q,$db_link);
if (!$r){ echo $q.'<br>'; return false; }
$rc=mysql_fetch_assoc($r);
if ($fn[0]=='`') $fn = substr($fn,1,strlen($fn)-2);
return stripslashes($rc[$fn]);
}

</script>

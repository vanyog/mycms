<script language="php">

// Този файл инициализира променливата $db_link
// която се използва в mysql_query($q,$db_link);

include($idir."conf_database.php");

$db_link = get_db_link($user, $password, $database);

function get_db_link($user, $password, $database){
$l = mysql_connect("localhost",$user,$password);
if (!$l){
 echo '<p>Не се получава връзка с MySQL сървъра!'; die;
}
if (!mysql_select_db($database,$l)){
 echo '<P>Не може да бъде избрана база данни.'; die;
}
mysql_query("SET NAMES 'cp1251';",$l);
//mysql_query("SET CHARACTER SET 'cp1251';",$l);
return $l;
}

</script>

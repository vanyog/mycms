<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>

<HEAD>
  <TITLE></TITLE>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
</HEAD>

<BODY>
<?php

//Показва се съдържанието на $_SERVER променливата

$k=array_keys($_SERVER);
sort($k);
echo '<table border=0>';
foreach ($k as $k0){
 echo '<tr><td>$_SERVER[\''.$k0.'\']</td><td nowrap> = '.$_SERVER[$k0].'</td></tr>'."\n";
}
echo '</table>';
phpinfo();

?>
</BODY>
</HTML>

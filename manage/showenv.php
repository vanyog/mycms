<!DOCTYPE html>.
<HTML lang="en">
<head>
  <title>Show $_SERVER</title>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<body>
<?php

//Показва се съдържанието на $_SERVER променливата

$k=array_keys($_SERVER); 
sort($k);
echo '<table border=0>';
foreach ($k as $k0){
 echo '<tr><td>$_SERVER[\''.$k0.
 '\']</td><td nowrap> = '.$_SERVER[$k0].'</td></tr>'."\n";
}
echo '</table>';
phpinfo();

?>
</body>
</html>

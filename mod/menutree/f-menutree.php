<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията menu_tree() показва обратния път от текущата страница към главната страница.
// Използват се записите на таблица $tn_prefix.`menu_tree`, които посочват родителското меню на всяко меню. 

include_once($idir.'lib/f_db_select_1.php');

function menutree(){
global $pth, $page_id, $page_data;
//global $p; // print_r($p);
$rz = '';
$pr = db_select_1('*','menu_tree',"`group`=".$page_data['menu_group']);
if (!$pr) return $rz;
$pg = db_select_1('*','pages','ID='.$pr['index_page']);
if ($page_id==$pg['ID']) $rz = translate($pg['title']);
else $rz = '<a href="'.$pth.'index.php?pid='.$pg['ID'].'">'.translate($pg['title']).'</a>'.$rz;
while ($pr['parent'])
{
  $pr = db_select_1('*','menu_tree',"`group`=".$pr['parent']); // print_r($pr); echo "<br>";
  $pg = db_select_1('*','pages','ID='.$pr['index_page']);
  if ($rz) $rz = ' >> '.$rz;
  $rz = '<a href="'.$pth.'index.php?pid='.$pg['ID'].'">'.translate($pg['title']).'</a>'.$rz;
}
return $rz;
}

?>

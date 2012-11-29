<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията menu($i) съставя последователност от хипервръзки (Меню).
// $i е номер на групата хипервръзки от таблица $tb_preffix.'menu_items'

include_once('conf_host.php');
include_once('conf_paths.php');
include_once('f_db_select_m.php');

function menu($i){
global $pth, $adm_pth, $page_id;
$d = db_select_m('*','menu_items',"`group`=$i ORDER BY `place`"); //if ($i==2){ print_r($d); die; }
$rz = '';
foreach($d as $m){
  $lnn = 1*$m['link'];
  $ln = $m['link'];
  if ($lnn) $ln = $pth.'index.php?pid='.$lnn;
  $pl = '';
  if (in_edit_mode()) $pl = $m['place'];
  if ($page_id!=$lnn) $rz .= '<a href="'.$ln.'">'.$pl.translate($m['name']).'</a> '."\n";
  else $rz .= '<span>'.$pl.translate($m['name'])."</span> \n";
}
if (in_edit_mode()) $rz .= '<a href="'.$adm_pth.'new_record.php?t=menu_items&group='.$i.'&link='.$page_id.'">New</a> '."\n";
return $rz;
}

?>

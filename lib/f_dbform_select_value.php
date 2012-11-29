<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията генерира html код за падащ списък за избор на стойност,
// срещаща се в поле $f на таблица $tn_prefix.$t на базата данни.

include_once($idir.'/lib/f_db_field_values.php');

function dbform_select_value($f,$t,$sl='',$js=''){
$va = db_field_values($f,$t,1);
if ($js) $js = ' onchange="'.$js.'"';
$rz = "<select name=\"$f\"$js>\n";
foreach($va as $v){
 if ($v==$sl) $s = ' SELECTED'; else $s = '';
 $rz .= '<option value="'.$v."\"$s>".$v."</option>\n";
}
$rz .= "</select>\n";
return $rz;
}

?>

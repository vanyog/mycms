<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function schedules_menu_items(){
global $maine_index;
$rz = '';
$p1 = stored_value('schedules_adminpage',false);
if($p1) $rz = "<a href=\"$p1\">Schedules</a> ";
return $rz;
}

?>

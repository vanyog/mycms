<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function schedules_menu_items(){
global $maine_index;
$p1 = stored_value('schedules_adminpage','/index.php?pid=11');
return "<a href=\"$p1\">Schedules</a> ";
}

?>

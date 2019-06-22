<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function conference_menu_items(){
global $maine_index;
$p1 = stored_value('conference_admin','/index.php?pid=1074');
$p2 = stored_value('conference_abstracts','/index.php?pid=100');
return "<a href=\"$p1\">Registrations</a><br>
<a href=\"$p2\">Abstracts</a><br>";
}

?>

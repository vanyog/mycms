<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function conference_menu_items(){
global $maine_index, $page_hash;
$p1 = stored_value('conference_admin');
$rz = '';
if(!empty($p1)){
$p1 = $p1.$page_hash;
$p2 = stored_value('conference_abstracts','/index.php?pid=100').$page_hash;
$p3 = stored_value('conference_abstractBook','/index.php?pid=586').$page_hash;
return "<a href=\"/mod/conference/whatsnew.php\">New titles</a><br>
<a href=\"$p1\">Registrations</a><br>
<a href=\"$p2\">Abstract titles</a><br>
<a href=\"$p3\">Abstract book</a><br>";
}
return $rz;
}

?>

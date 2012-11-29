<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function strip_last_name($fn){
$i = strrpos($fn,'/');
return substr($fn,0,$i);
}

?>

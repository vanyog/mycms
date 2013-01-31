<?php
// Copyright: Vanyo Georgiev info@vanyog.com

function set_self_query_var($n,$v){
$r = $_GET;
$r[$n] = $v;
return $_SERVER['PHP_SELF'].'?'.http_build_query($r);
}

?>

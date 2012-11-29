<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Функцията parse_template($p) подготвя шаблона на страница $p.
// ($p е асоциативен масив със записа за страницата от таблица $tn_prefix.'pages'.)
// Ако шаблонът има един или повече родители го вмъква в тях и след това извиква
// функцията parse_content()

include_once('translation.php');

function parse_template($p){
global $content_date_time;

// Четене на шаблона на страницата от таблица `templates`
$t = db_select_1('*','templates',"ID=".$p['template_id']);
if (!$t) return 'No page template found. May be the system is not installed.';
$cnt = stripslashes($t['template']);

// Ако шаблонът е празен или не същуствува
if (!$cnt) $cnt = '<h1><!--$$_PAGETITLE_$$--></h1>
<!--$$_CONTENT_$$-->';

// Ако шаблонът има родител, родителят се четe и в него се вмъква шаблона
// Повтаря се до шаблон без родител
while ($t['parent']){
$t0 = db_select_1('*','templates',"ID=".$t['parent']);
$cnt = str_replace('<!--$$_TEMPLATE_$$-->', $cnt, stripslashes($t0['template']) );
$t = $t0;
}

return parse_content($cnt);
}

function show_visits($p){
if (show_adm_links()) return '   Visited: '.$p['tcount'].', Today: '.$p['dcount'];
else return '';
}

?>

<?php

/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Функцията parse_template($p) подготвя шаблона на страница $p.
// ($p е асоциативен масив със записа за страницата от таблица $tn_prefix.'pages'.)
// Ако шаблонът има един или повече родители го вмъква в тях и след това извиква
// функцията parse_content()

include_once($idir.'lib/translation.php');

function parse_template($p){
global $content_date_time, $debug_mode, $adm_pth;

// Номер на използвания за показване на страницата шаблон
$tid = $p['template_id'];

// Ако има параметър за смяна на шаблона template=xx
if (isset($_GET['template'])){
  $tid = 1*$_GET['template'];
  // Шаблонът може да се смени само ако е посочен в опцията 'allowed_templates'
  if (strpos(stored_value('allowed_templates'), ",$tid,")===false) $tid = $p['template_id'];
}

// Четене на шаблона на страницата от таблица `templates`
$t = db_select_1('*','templates',"ID=$tid");
if (!$t) return 'No page template found. May be the system is not installed.  See <a href="http://vanyog.com/_new/index.php?pid=91" target="_blank">USAGE.txt</a> file.';
if(!empty($debug_mode)) 
    echo 'Template(<a href="'.$adm_pth.'edit_record.php?t=templates&r='.$tid.'">'.$tid."</a>) ".db_req_count()."<br>\n";
$cnt = stripslashes($t['template']);

// Ако шаблонът е празен или не същуствува
if (!$cnt) $cnt = '<h1><!--$$_PAGETITLE_$$--></h1>
<!--$$_CONTENT_$$-->';

// Ако шаблонът има родител, родителят се четe и в него се вмъква шаблона
// Повтаря се до шаблон без родител
while ($t['parent']){
  $t0 = db_select_1('*','templates',"ID=".$t['parent']);
  if(!empty($debug_mode)) echo "Template(".$t['parent'].") ".db_req_count()."<br>\n";
  $cnt = str_replace('<!--$$_TEMPLATE_$$-->', $cnt, stripslashes($t0['template']) );
  $t = $t0;
}

// Заместване на елементите със съдържние и връщане на резултата

$rz = parse_content($cnt);
if(!in_edit_mode()){
   $rz = preg_replace('/<!--(.*)-->/Uis', '', $rz);
   $rz = preg_replace('/^\n+|^[\t\s]*\n+/m', '', $rz);
}
// Премахване на потребителското меню, ако няма влязъл потребител
$rz = removeusermenu($rz);
return $rz;
}

function show_visits($p){
if (show_adm_links()) return '   Visited: '.$p['tcount'].', Today: '.$p['dcount'];
else return '';
}

function removeusermenu($s){
if(isset($_COOKIE['PHPSESSID']) && !session_id()) session_start();
if( ! (isset($_SESSION['user_username']) || isset($_SESSION['user_password']) ) )
{
$s = preg_replace('/<div id="user_menu">?.*<\/div>/Uis', '', $s);
}
return $s;
}

?>
<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Функцията user_agent() регистрира user-agent-а на потребителя.
// Ако вече има запис за него в таблица $tn_prefix."user_agents", го отброява и отбелязва времето и IP-адреса.
// Ако няма запис вмъква такъв.
// Съхраняват се записи не по-стари от 365 дни.

// Ако параметърът $p=='top10' функцията връща класация на 10-те най-често посещаващи сайта браузъри
// Ако параметърът $p=='user'  функцията връща стринга на браузъра на потребителя

function useragent($p = ''){
global $tn_prefix, $db_link;
switch($p){
case '': break;
case 'top10': return top_10_user_agents(); break;
case 'user': return $_SERVER['HTTP_USER_AGENT']; break;
default: return 'Unknown parameter <strong>'.$p.'</strong> in <strong>useragent</strong> module.';
}
if (show_adm_links()) return;
$ua = addslashes($_SERVER['HTTP_USER_AGENT']);
$ip = $_SERVER['REMOTE_ADDR'];
$ud = db_select_1('ID','user_agents',"`agent`='$ua'");
if ($ud) $q = "UPDATE `$tn_prefix"."user_agents` SET count = count+1, `date_time_2`=NOW(), `IP`='$ip' WHERE `ID`=".$ud['ID'].";";
else $q = "INSERT INTO `$tn_prefix"."user_agents` SET `agent`='$ua', `date_time_1`=NOW(), `date_time_2`=NOW(), `IP`='$ip';";
mysql_query($q,$db_link);
$dt = date('Y-m-d H:i:s',time()-365*24*60*60);
$q1 = "DELETE FROM `$tn_prefix"."user_agents` WHERE `date_time_2`<'$dt';";
mysql_query($q1,$db_link);
return '';
}

// Класация на 10-те най-често посещаващи сайта браузъри

function top_10_user_agents(){
$uad = db_select_m('agent','user_agents','1 ORDER BY `count` DESC LIMIT 0,10');
$rz = '<ol>';
foreach($uad as $ua){
 $rz .= '<li><a href="http://www.useragentstring.com/?uas='.urlencode($ua['agent']).'" target="_blank">'.$ua['agent']."</a></li>\n";
}
return $rz.'</ol>';
}
?>

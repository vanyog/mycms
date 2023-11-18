<?php
/*
VanyoG CMS - a simple Content Management System
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

// Анализира препратките от Google и Bing,
// и показва по кои думи е намерен сайта в тези търсачки

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'mod/user/f_user.php'); user();
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_view_table.php');

// Масив търсачки
$e = array('google.','bing.com','search.abv.bg','search.ask.com','search.tb.ask.com');

// Име на параметъра с търсената фраза на търсачки, в които е различен от q
$qrs = array('search.tb.ask.com'=>'searchfor');

// SQL заявка за намиране на търсачките
$q = '';
foreach($e as $e1){
 if ($q) $q .= ' OR ';
 $q .= "`referer` LIKE '%$e1%'";
}

$d = db_select_m('page_id,referer,count', 'refstat', "$q ORDER BY `count` DESC");

$page_content = '<h1>'.translate('refstat_stitle').'</h1>
<h2>'.translate('refstat_engines').'</h2>
<p>'.translate('refstat_records').' '.db_table_field('COUNT(*)','refstat','1').'</p>
<table>
<tr>
<th>'.translate('refstat_enginе').'</th>
<th>'.translate('refstat_words').'</th>
<th>'.translate('refstat_page').'</th>
<th>'.translate('refstat_count').'</th>
</tr>
';

foreach($d as $d1){
 $u = parse_url($d1['referer']);// echo $u['host']."<br>";
 $p = array();
 parse_str($u['query'], $p);
 if (isset($qrs[$u['host']])){ $qs = $qrs[$u['host']]; } else $qs = 'q';
 if (isset($p[$qs])){// echo $p[$qs]."<br>";
  $rz = iconv("UTF-8", "cp1251", $p[$qs] );
  $page_content .= '<tr>
<td><a href="'.$d1['referer'].'">'.$u['host'].'</a></td>
<td>'.$rz.'</td>
<td><a href="/index.php?pid='.$d1['page_id'].'"> '.$d1['page_id'].'</a></td>
<td>'.$d1['count'].'</td>
</tr>'; 
 }
}
$page_content .= '</table>
<h2>'.translate('refstat_direct').'</h2>
';

$d = db_select_m('page_id,referer,count', 'refstat', "NOT ($q) ORDER BY `count` DESC");

for($i=0;$i<count($d);$i++){
   $d[$i]['page_id'] = '<a href="'.$d[$i]['page_id'].'" target="_blank">'.$d[$i]['page_id'].'</a>';
   $l = strlen($d[$i]['referer']);
   if ($l>53) $a = substr($d[$i]['referer'],0,50).'...'; else $a = $d[$i]['referer'];
   $d[$i]['referer'] = '<a href="'.$d[$i]['referer'].'" target="_blank">'.$a.'</a>';
}

$page_content .= view_table($d,'',array(
'referer'=>translate('refstat_referer'),
'page_id'=>translate('refstat_page'),
'count'=>translate('refstat_count')
));

include($idir.'lib/build_page.php');

?>

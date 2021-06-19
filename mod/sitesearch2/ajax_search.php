<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2021  Vanyo Georgiev <info@vanyog.com>

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

if(!isset($_GET['text'])) die;

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');

$tx = $_GET['text'];
$txu = mb_strtoupper(mb_substr($tx,0,1)).mb_substr($tx,1);

$rz = '';

// Търсене в заглавия на страници
$rt = db_select_m('ID,text', 'content', "`name` LIKE '%_title' AND `text` LIKE '%".addslashes($tx)."%' LIMIT 10");
if(count($rt)) $rz .= "<h2>Страници</h2>\n";
foreach($rt as $r){
  $rz .= '<p><a href="'.current_pth(__FILE__).'open_by_cid.php?cid='.$r['ID'].'">'.
        preg_replace("/($tx|$txu)/i", "<span>$1</span>", $r['text'])."</a></p>\n";
}

// Търсене в колекцията връзки
$rl = db_select_m('ID,Title', 'outer_links', "`Title` LIKE '%".addslashes($tx)."%' LIMIT 10");
if(count($rl)) $rz .= "<h2>Връзки</h2>\n";
foreach($rl as $r){
  $rz .= '<p><a href="'.current_pth(__FILE__).'open_by_cid.php?lid='.$r['ID'].'">'.
        preg_replace("/($tx|$txu)/i", "<span>$1</span>", $r['Title'])."</a></p>\n";
}

echo $rz;

?>
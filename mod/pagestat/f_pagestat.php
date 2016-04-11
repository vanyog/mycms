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

// ¬ режим на администриране функци€та pagestat() показва бро€ на посещени€та на страницата -
// общи€ брой и за ден€, както и линк "page stats", който отвар€ страница с таблица на статистиката 
// на посещени€та на страниците от сайта

function pagestat(){
global $page_data, $can_edit;
if ( !(show_adm_links() || $can_edit) ) return '';
$pth = current_pth(__FILE__);
return 'Total '.$page_data['tcount'].' Today '.$page_data['dcount'].' See <a href="'.$pth.'page_stats.php?pid='.
$page_data['ID'].'">page</a> or <a href="'.$pth.'content_stats.php">content</a> stats';}

?>

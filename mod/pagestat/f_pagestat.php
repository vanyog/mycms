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
global $page_data, $can_edit, $adm_pth;
if ( !( stored_value("pagestat_public") || show_adm_links() || $can_edit ) ) return '';
$pth = current_pth(__FILE__);
$rz = translate('pagestat_total').$page_data['tcount'].
      translate('pagestat_today').$page_data['dcount'];
if ( !( show_adm_links() || $can_edit ) ) return $rz;
$rz .= ' See <a href="'.$pth.'page_stats.php?pid='.$page_data['ID'].'">page</a>'.
       ',    <a href="'.$pth.'page_stats.php?group='.$page_data['menu_group'].'">group</a>'.
       ' or  <a href="'.$pth.'content_stats.php">content</a> stats<br>'."\n".
       'Page ID: <a href="'.$adm_pth.
       'edit_record.php?t=pages&r='.$page_data['ID'].'">'.$page_data['ID']."</a>\n".
       ' template: <a href="'.$adm_pth.
       'edit_record.php?t=templates&r='.$page_data['template_id'].'">'.$page_data['template_id'].
       ' &nbsp; <a href="https://realfavicongenerator.net/favicon_checker?protocol='.$_SERVER['REQUEST_SCHEME'].
       '&site='.stored_value('host_web').'" targit="_blank">favicon check</a>'.
       "</a>\n";      
return $rz;
}

?>
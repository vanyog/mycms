<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2019  Vanyo Georgiev <info@vanyog.com>

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

// Модул за показване на новини

include_once($idir.'lib/f_db2user_date_time.php');

function news01(){
global $adm_pth;
$rz = '';
$per_page = 3;
$news_data = db_select_m('*', 'news01', '1 ORDER BY `date_time_1` DESC LIMIT 0,'.$per_page);
foreach($news_data as $news) $rz .= news01_1news($news);
$news_count = db_table_field('COUNT(*)', 'news01', '1');
if(in_edit_mode()) $rz .= '<a href="'.$adm_pth.'new_record.php?t=news01&text=news01_'.($news_count+1).'">New</a>';
$rz .= '';
return $rz;
}

function news01_1news($news){
$rz = '<div class="news01">'."\n";
$rz .= '<p class="date">'.db2user_date_time($news['date_time_1'], true, true, false)."</p>\n";
$rz .= translate($news['text']);
$rz .= '</div>'."\n";
return $rz;
}

?>
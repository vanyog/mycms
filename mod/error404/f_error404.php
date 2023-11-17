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

function error404(){
global $adm_pth, $page_id;
if (show_adm_links() && ($page_id>0))
  return '<p><a href="'.$adm_pth.'new_record.php?t=pages&ID='.$page_id.
         '&title=p'.$page_id.'_title&content=p'.$page_id.'_content">Click here</a> to create this page.</p>';
}

?>

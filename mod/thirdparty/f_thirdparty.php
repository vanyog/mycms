<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// Показва съдържание, необходимо за външни услуги от Google, Facebook и др.
// Съдържанието не се показва на локален сървър, в режим на редактиране и при администриране.
// При изпращане на параметър $_GET['third']='on' съдържанието се показва във всички случаи.
// При изпращане на параметър $_GET['third']='off' съдържанието не се показва във всички случаи.
// Ако параметърът $a започва с 'content_' е име на запис от таблица `content`.
// ако не започва с 'content_' е съдържанието, което се показва.


function thirdparty($a){
$rz = '';
if( (isset($_GET['third']) && ($_GET['third']=='on')) || 
    !(in_edit_mode() || show_adm_links()  || is_local() || (isset($_GET['third']) && ($_GET['third']=='off')) )
  ){
   if(substr($a,0,8)=='content_') $rz = translate($a);
   else $rz = stripslashes($a);
}
return $rz;
}

?>
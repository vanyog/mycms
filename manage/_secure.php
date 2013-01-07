<?php
/*
MyCMS - a simple Content Management System
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

// “ози скрипт служи за повишаване сигурността на системата

include('conf_manage.php');
include($idir.'conf_paths.php');
include($idir.'lib/f_rand_string.php');

// ћасив от имена на стойности от таблица $tn_prefix.'options', на които се генерират случайни стойности
$na = array('admin_path', 'adm_name', 'adm_value', 'edit_name', 'edit_value');

// ѕром€на на стойностите с имена от масива $na
foreach($na as $i=>$n){
  $v = rand_string(4);
  if (!$i){
    $v[0] = '_';
    echo "Rename 'manage' directory to '$v' and after that <a href=\"$pth"."$v\">click here</a>";
  }
  store_value($n,$v);
}

?>

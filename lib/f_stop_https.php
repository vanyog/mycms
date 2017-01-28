<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2017  Vanyo Georgiev <info@vanyog.com>

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

// Функцията проверява дали протоколът на текущата страница е https
// и ако не е зададена настройта 'stop_https' със стойност 0 и в текущата страница не се използва модул USERREG пренасочва
// към същата страница, но по http.

// Параметърът $a е името на съдържанието на текущата страница

function stop_https($a){
global $language;
if(stored_value('stop_https', 1) && isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')){
  $cnt = db_table_field('text', 'content', "`name`='$a' AND `language`='$language'");
  $pos = strpos($cnt, '$$_USERREG_');
  if($pos===false){
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    die();
  }
}
return "";
}

?>

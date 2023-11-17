<?php
/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2016  Vanyo Georgiev <info@vanyog.com>

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

//
// Променяне кодировката на сстринхг $a от кодировката на файловете,
// към кодировката на сайта.
//

function encode($a){
global $file_encoding, $site_encoding;
if(empty($file_encoding)||empty($site_encoding)||empty($a)) return $a;
return iconv($file_encoding, "$site_encoding//IGNORE", $a);
}


?>

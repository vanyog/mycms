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

// Филтър links открива уеб адреси и ги превръща в активни линкове

function links($t){
$pt = '/(?:[^"]https*:\/\/|[^\/]www\.|[^"]https*:\/\/www\.)[a-zA-Z\.\/\-0-9_?=&;%+#~]*/is';
return preg_replace_callback($pt,'link_this',$t);
}

function link_this($a){
$h = substr($a[0],1);
$u = parse_url($h);
if (!isset($u['host'])){ $u['host'] = $u['path']; $a[0] = 'http://'.$a[0]; }
return $a[0][0].'<a href="'.$h.'" target="_blank">'.$u['host'].'</a>';
}

?>

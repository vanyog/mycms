<?php
/*
MyCMS - a simple Content Management System
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

// Показване на форма за търсене в различни търсачки

include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_encode.php');

function inetsearch(){
global $page_header;
$page_header = '<script type="text/javascript"><!--
function searchBy(a, b){
var l = document.getElementById("selink");
var w = document.forms.inetsearch.words.value;
l.href = a+encodeURIComponent(b+w+b);
if (w) window.open(l.href);
}
--></script>';
$rz = '<p><a href="index.php?pid=" id="selink" target="_blank">'.encode('Вижте резултата').'</a></p>'."\n";
$f = new HTMLForm('inetsearch');
$i = new FormInput(encode('Ключови думи:'), 'words', 'text');
$i -> size = 100;
$f -> add_input( $i );
$i = new FormInput(encode('Търсене в:'), '', 'button', 'google.bg');
$i -> js = 'onclick="searchBy(\'https://google.bg/search?q=\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'scholar.google.bg');
$i -> js = 'onclick="searchBy(\'https://scholar.google.bg/scholar?q=\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'bg.wikipedia.org');
$i -> js = 'onclick="searchBy(\'https://bg.wikipedia.org/wiki/\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'en.wikipedia.org');
$i -> js = 'onclick="searchBy(\'https://en.wikipedia.org/wiki/\', \'\');"';
$f -> add_input( $i );
$i = new FormInput('', '', 'button', 'academic.microsoft.com');
$i -> js = 'onclick="searchBy(\'https://academic.microsoft.com/#/search?iq=\', \'@\');"';
$f -> add_input( $i );
return $rz.$f->html();
}

?>

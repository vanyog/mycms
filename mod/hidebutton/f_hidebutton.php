<?php
/*
MyCMS - a simple Content Management System
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

// Модул който добавя javascript за поставяне на бутон за скриване на някои елементи от страницата

global $page_header;
$page_header .= '<script>
function addCloseButtonTo(e){
e.style.position = "relative";
e.innerHTML = e.innerHTML + 
  "<span style=\"position:absolute;right:4px;top:0;font-weight:bold;font-size:120%;cursor:pointer;\"" +
  " onclick=\"closeContent(this);\">&times;</span>";
e.onclick = function(){ return false; } 
}
function closeContent(e){
var p = e.parentElement;
p.innerHTML = "";
}
</script>';

?>
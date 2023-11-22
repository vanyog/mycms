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

// Скрипт за редактиране на записите с имена ccs_и_нещо_друго, от таблица options, 
// съдържащи CSS правила, добавяни с функция add_style()

$exe_time = microtime(true);

error_reporting(E_ALL); ini_set('display_errors',1);

include_once("conf_manage.php");
include_once($idir.'conf_paths.php');
include_once($idir.'lib/o_form.php');

$da = db_select_m('*', 'options', 'name LIKE "css_%" ORDER BY `name` ASC');

$page_content = "<h1>Edit styles</h1>";

$page_content .= '<p><a href="'.dirname(dirname($_SERVER['PHP_SELF'])).'">Home</a></p>';

$page_content .= "<ul>\n";
foreach($da as $d) 
        $page_content .= '<li><a href="'.$adm_pth.'edit_record.php?t=options&r='.$d['ID'].'">'.
                        $d['name']."</a></li>\n";
$page_content .= "</ul>\n<p>\n<input type=\"text\" id=\"stylename\"> <button onclick=\"newStyle();\">New style</button></p>";

$page_content .= '<script>
function newStyle(){
var v = document.getElementById("stylename").value;
document.location = "'.$adm_pth.'new_record.php?t=options&name=css_" + v;
}
</script>';

include("build_page.php");

?>
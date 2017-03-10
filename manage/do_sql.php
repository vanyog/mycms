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

// Изпълнява SQL заявка

include("conf_manage.php");
include_once($idir."lib/f_db_query.php");
include_once($idir."lib/f_view_table.php");

$page_content = '<p>'.$_POST['sql'].'</p>
<p>Back to: <a href="edit_data.php">Database</a></p>'."\n".
view_table(db_query($_POST['sql'])).
'<p>Back to: <a href="edit_data.php">Database</a></p>';

include("build_page.php");

?>


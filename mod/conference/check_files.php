<?php
/*
MyCMS - a simple Content Management System
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

// ��������� ���� ���������, ������ � ������������ �� ������������� ������������ �� ������ �����

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'conf_paths.php');
include_once($idir.'lib/f_is_local.php');
include_once($idir.'lib/f_stored_value.php');
include_once($idir.'lib/f_db_select_m.php');
include_once($idir.'lib/f_file_list.php');
include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_set_self_query_var.php');
include_once($idir.'lib/f_unset_self_query_var.php');
include_once($idir.'mod/usermenu/f_usermenu.php');

// �������� �� ������� �� ������� ����������
usermenu(true);

// ��� ���� ����� �� ����� conference - ����
if(empty($can_manage['conference'])) die("Not permitted for current user");

// ��� �� �����������
$ut = stored_value('conference_usertype', 'vsu2014');

// ���������� � ������ ������� �� �������������
$p = stored_value('conference_files_'.$ut, '/conference/2014/files/');
$dir = $_SERVER['DOCUMENT_ROOT'].$p;

header("Content-Type: text/html; charset=$site_encoding");

// ��������� �� ����
if(isset($_GET['delete'])){
$fn = $dir.$_GET['delete'];
if(file_exists($fn)){
  unlink("$fn");
  echo '<p>'.encode('���� ������ ���� ').$fn."<p>\n";
}
else echo("File do not exists ".$fn);
}

echo '<p><a href="'.unset_self_query_var('delete').'">Reload</a></p>'."\n";

// ������ ������� �� DOC ���������
$af = db_select_m('fulltextfile', 'proceedings', "`utype`='$ut' AND `fulltextfile`>''");
$ap = array();
echo encode("<h2>�������� DOC �������</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile'];
  $ap[] = $f['fulltextfile'];
  if (!file_exists($fn)) echo $fn."<br>";
}

// ������ ������� �� PDF ���������
$af = db_select_m('fulltextfile2', 'proceedings', "`utype`='$ut' AND `fulltextfile2`>''");
echo encode("<h2>�������� PDF �������</h2>");
foreach($af as $f){
  $fn = $dir.$f['fulltextfile2'];
  $ap[] = $f['fulltextfile2'];
  if (!file_exists($fn)) echo $fn."<br>";
}

$fl = file_list($dir);

echo encode("<h2>������� �������</h2>").'
<script>
function confirm_link(e,n){
if(confirm("'.encode("�������� �� ������ �� �������� ���� ").'\"" + n + "\"?"))
   document.location = e;
}
</script>
';
foreach($fl as $f)
 if (!in_array($f,$ap)){
    if (!is_local()) $fl = rawurlencode($f); else $fl = $f;
    echo "<a href=\"$p$fl\">$f</a> ".
          '<a href="'.set_self_query_var('delete',$f).'" '.
          'style="font-weight:bold;color:red;" '.
          'title="'.encode('��������� �� �����').'" '.
          'onclick="confirm_link(this,\''.$f.'\');return false;">x</a><br>'."\n";
 }

?>

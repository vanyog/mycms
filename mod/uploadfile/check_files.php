<?php
/* 
MyCMS - a simple Content Management System
Copyright (C) 2013 Vanyo Georgiev <info@vanyog.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// ��������� ��������� �� ������� 'files'

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/translation.php');

// ������ �� ������ ����� �� ������� 'files'
$da = db_select_m('*', 'files', '1');
$in = $main_index.'?pid=';

$ar = stored_value('uploadfile_otherroot');

foreach($da as $d){
  // ��� ������ ���� ���, ���-�������� � ������
//  if (!$d['filename'])
  {
    // �������� ���� �� �������� �� ����������
    // ��� �� ������������ �� ����������
    $cn = db_table_field('content', 'pages', "`ID`=".$d['pid']);
    // ��� � ������ �� ������ ��� ��������� ����
//    if (!$cn) break;
    // ���� ��� ����������
    $lk = '<a href="'.$in.$d['pid'].'" target="_blank">'.$d['pid'].'</a>';
    // �������� ��
    $y = false;
    foreach($languages as $language=>$c){
      // ���������� �� ���������� �� ���� $language
      $ct = db_table_field('text', 'content', "`name`='$cn' AND `language`='$language'");
      $p = strpos($ct, '!--$$_UPLOADFILE_'.$d['name']);
      $y = $y || ($p!==false);
 //     if ($y) die(strlen($ct)." $ct $language");
    }
    if (!$y) 
        echo $d['name'].' on page '.$lk." not used. ".chage_root($d['filename'])."<br>";
  }
}

function chage_root($n){
global $ar;
if($ar){
   $ln = substr($n,strlen($ar));
   $n = $_SERVER['DOCUMENT_ROOT'].$ln;
   if(file_exists($n)) $n = "<a href=\"$ln\">$n</a>";
}
return $n;
}

?>

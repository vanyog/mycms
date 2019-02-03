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

// ��������� ��� �� ������ � ������� $tn_prefix.'sitesearch_words' �� ������ �� ����� � ��� ��.

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

//include_once($idir.'/lib/f_db_select_m.php');
include_once('f_sitesearch.php');

// ������ �� ������ ���� �� $tn_prefix.'sitesearch_words'
$aw = db_select_m('*','sitesearch_words',"1 ORDER BY `count` DESC");

header("Content-Type: text/html; charset=windows-1251");

// �� ����� ���� �� �������� �� �� ��������
$lm = 90;
// �� ����� $i1 �� ����� $i2
$i1 = 0;
if (isset($_GET['i1'])) $i1 = 1*$_GET['i1'];
$i2 = $i1 + $lm;
// �� $cl1 � ������
$cl1 = 30;

echo '<h1>������� �� ������, �� ����� � ������� ������� �� �����</h1>
<p>���� ������������ ����: '.count($aw).'</p>
<p>';
for($i=0; $i<count($aw); $i=$i+$lm) 
  if ($i==$i1) echo "<strong>$i-".($i+$lm-1).'</strong> ';
  else echo "<a href=\"check_words.php?i1=$i\">$i-".($i+$lm-1).'</a> ';
echo '</p>
<p>���� � ��� �� ���������� ������� : ���� : ����� ���� � ��������� - � ����� �������� �� ����� (� ����� ��������)</p>
<div style="float:left;margin:5px;">';

for($i=$i1; ($i<$i2)&&($i<count($aw)); $i++){
  echo $aw[$i]['date_time_2'].' : ';
  $w = $aw[$i];
  $w1 = $w['word'];
  $wa = explode(' ',$w1);
  $cn = db_select_m('name','content',where_part($wa,'AND'));
  $c = count($cn);
  $pi = siteserch_pgids($cn);
  $c2 = count($pi);
  if (!$c){ $a1 = '<span style="color:red;">'; $a2 = '</span>'; }
  else { $a1 = ''; $a2 = ''; }
  echo "$a1$w1$a2 : ".$w['count'];
  if ($c) echo " - $c (<a href=\"/index.php?ssr=$w1\">$c2</a>)";
  echo "<br>";
  if ($i&&(($i+1) % $cl1 ==0)) echo '</div><div style="float:left;margin:5px;">'."\n";
}

echo '</div>';
?>

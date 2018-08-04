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

// ��������� db2user_date_time($dts) ������������ �����, �������� ����-���,
// ��������� �� MySQL ���� ����� ��� ������: dd mmmm yyyy hh:mm

// �� �� �� ��������� ��� ������, ���� ������� ������� � ������� $tn_prefix.'content'
// �� ��� ����� � ��� 'month_names', �������� ��������� �� ����� $month � ������� �� 
// �������� �� ����� �� �������, ���������� �� �����.

// ��� ������� ��������� $tm = false �� �� ������� ���.
// ����� ��� �� �� �������, ���� ��� � 00:00:00.

// ������� ��������� ���������/��������� ���������� �� ��������� ���� ������.

function db2user_date_time($dts, $tm = true, $ts = true){
$c = translate('month_names',false); die($c);
eval($c);
if ((substr($dts,11,8)=="00:00:00")||!$tm) $t = '';
else {
  $h = (1*substr($dts,11,2));
  if ( ($h<10) && $ts ) $t = ' &nbsp;'; else $t = ' ';
  $t .= $h.substr($dts,13,3);
  if (1*substr($dts,17,2)) $t .= substr($dts,16,3);
}
$d = 1*substr($dts,8,2);
if ( ($d<10) && $ts ) $d = '&nbsp;'.$d;
if($t) $t .= ' ';
$rz = $t.
  $d.' '.
  $month[1*substr($dts,5,2)].' '.
  substr($dts,0,4);
return $rz;
}

function db2user_from_to($d1,$d2){
$c = translate('month_names',false);
eval($c);
$n1 = 1*substr($d1, 8, 2); $n2 = 1*substr($d2, 8, 2);
$m1 = 1*substr($d1, 5, 2); $m2 = 1*substr($d2, 5, 2);
$y1 = 1*substr($d1, 0, 4); $y2 = 1*substr($d2, 0, 4);
$s1 = '';
$s2 = "$n2 ".$month[1*$m2]." $y2";
if( $y1!=$y2) $s1 = $y1;
if( ($m1!=$m2) || ($y1!=$y2) )   $s1 = $month[1*$m1]." ".$s1;
if( ($y1==$y2) && ($m1==$m2) && ($n1==$n2) ) $s1 = '';
else                                         $s1 = $n1." ".$s1;
if($s1) $s1 .= " - ";
return "$s1$s2";
}

?>
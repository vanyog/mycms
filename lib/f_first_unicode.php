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

// ����� ������� �� ������ ������ �� ������.
// � ���������� ���������� $utf8_char_lenght �� ������� ��������� �� ������� � �������.

// �� ��������� �� ��������� � ��������� ���� ��:
// http://stackoverflow.com/questions/9361303/can-i-get-the-unicode-value-of-a-character-or-vise-versa-with-php

global $utf8_char_lenght;

function first_unicode($c) {
    global $utf8_char_lenght;
    $utf8_char_lenght = 0;
    if(!$c) return 0;
    if (ord($c{0}) >=0 && ord($c{0}) <= 127){
        $utf8_char_lenght = 1;
        return ord($c{0});
    }
    if (ord($c{0}) >= 192 && ord($c{0}) <= 223){
        $utf8_char_lenght = 2;
        return (ord($c{0})-192)*64 + (ord($c{1})-128);
    }
    if (ord($c{0}) >= 224 && ord($c{0}) <= 239){
        $utf8_char_lenght = 3;
        return (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
    }
    if (ord($c{0}) >= 240 && ord($c{0}) <= 247){
        $utf8_char_lenght = 4;
        return (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
    }
    if (ord($c{0}) >= 248 && ord($c{0}) <= 251){
        $utf8_char_lenght = 5;
        return (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
    }
    if (ord($c{0}) >= 252 && ord($c{0}) <= 253){
        $utf8_char_lenght = 6;
        return (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
    }
    if (ord($c{0}) >= 254 && ord($c{0}) <= 255){    //  error
        return FALSE;
    }
    return 0;
}

// ����� ������ ��� $c � ������ �� ��������

function is_cirillic($c){
return (($c>=1040) && ($c<=1103));
}

// ����� ������ ��� $c � ������ �� ��������� ������ �����

function bg_vowel($c){
return in_array($c, array(1040,1045,1048,1054,1059,1066,1070,1071,
                          1072,1077,1080,1086,1092,1098,1102,1103));
}


// ���������� �� ���� �� ����������� ���������� �������

function bg_word_abrev($a){
global $utf8_char_lenght;
$c = first_unicode($a);
$i = $utf8_char_lenght;
// ��� ������� ����� � �� �������� �� ������� ��� ����� �� ����������� �������
if(is_cirillic($c)) {
  $end = false; // ����, �� �� � ������� ��������
  // ������� �� ������ ���� ��������
  while( ($i<strlen($a)-1) ){
    $v = bg_vowel($c); // ��������� ����� � ������
    // ��� � ������ ���� �������� - ����
    if($v && $end){ $i -= $utf8_char_lenght; break; }
    // ��� �� � ������ ������� ����, �� � ������� ��������
    if(!$v) $end = true;
    // �������� ��� ���������� �����
    $c = first_unicode(substr($a, $i));
    $i += $utf8_char_lenght;
  }
}
$rz = substr($a, 0, $i);
if($rz) $rz .= '.';
return $rz;
}

?>

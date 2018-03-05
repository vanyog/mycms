<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2018  Vanyo Georgiev <info@vanyog.com>

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

// ���� ����� $url � ������ ������ ������� �� html ��������

// ����� �� ����������
$url = 'http://epi/index.php';
// ����� �� bootstrap.css
$bsc = '/Users/vanyog/Sites/bulepid.org/httpdocs/_bootstrap-3.3.7/dist/css/bootstrap.css';

// ������ ���� �� ����������
$cnt = file_get_contents($url);
// ������ �� html ��������, ����� �� ������ �� ����������
$ht = htags($cnt);
// ������ �� ���������, ����� �� ��������� �� ����������
$cs = classes($cnt);

// ������ �� CSS ���� �� bootstrap.css
$bs = file_get_contents($bsc);
// ���������� �� �����������
$bs = preg_replace('/(\n|^)\/\*.*?\*\//s', '', $bs);
// ��������� �� CSS ���� �� �����, ������������� �� �������� �����
$md = medias($bs);
die('<pre>'.$md[1].'</pre>');


/*
foreach($cs as $c){
   $p = "/\.$c.*?\{.*?\}/s";
   $i = preg_match_all($p, $bs, $m);
      echo "<h4>$c</h4>\n";
   if($i){
      echo print_r($m, true);
  }
}
*/

echo "<h4> </h4>\n<pre>".$bs."</pre>";


//-----------------------------------

function medias($cnt){
$nc = preg_replace('/(@media.*?\{)/', '------$1', $cnt);
$rz = explode('------', $nc);
return $rz;
}

function htags($cnt){
$m = array();
$i = preg_match_all('/<([a-zA-Z]+?)(?:\s|>)/', $cnt, $m);
$cs = array();
foreach($m[1] as $c) $cs[$c] = '';
ksort($cs);
return array_keys($cs);
}

function classes($cnt){
$m = array();
$i = preg_match_all('/ class="(.+?)"/', $cnt, $m);
$cs = array();
foreach($m[1] as $c) $cs[$c] = '';
ksort($cs);
return array_keys($cs);
}



?>
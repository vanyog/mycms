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

// ��������� parse_content($cnt) �������� ���������� <!--$$_XXX_$$--> � ������� $cnt
// ��� ����������, ���������� �� php ���������, ����� �� ���������� � ������� $tn_prefix.'scripts'
// ��� ������ �� ���������� mod.
// ��� ������ � ������������ �� ������� eval(), ������ �� ���������� �� ������� � �������
// `options` ��� ��� 'eval_error_uri'.

include_once($idir.'lib/f_translate.php');
include_once($idir.'lib/f_adm_links.php');
include_once($idir.'lib/f_mod_path.php');
include_once($idir.'mod/rawfile/f_rawfile.php');

function parse_content($cnt){
global $page_options, $page_data, $page_title, $body_adds, $page_header, $content_date_time,
       $idir, $pth, $adm_pth, $apth, $mod_pth, $mod_apth,
       $can_visit, $can_manage, $site_encoding, $debug_mode;

$l = strlen($cnt);
$str1 = '<!--$$_'; // ��������� �� ������ �� ����������� �������
$str2 = '_$$-->';  // ��������� �� ���� �� ����������� �������

// ����� �� ���������� �� ��������
// $p0 - ������� �� ������� ���������� �����
while ( !(($p0 = strrpos($cnt,$str1))===false) ){

$p1 = $p0 + strlen($str1); // ������� �� ������ ������ �� ����� �� ��������
$p2 = strrpos($cnt,$str2); // ������� �� ������ ������ �� ����������� �� ���� �� ��������� �������
// echo "$l $p1 $p2 ".substr($cnt,$p1,$p2-$p1)."<br>";
// ��� �� � �������� ��������� �� ����, ����������� �� ������ �� ������� �� ����� ������,
// � ���� ���� �� ������ ��������� == Not closed ! ==
if ($p2<$p1){
  $cnt = substr_replace($cnt,'&lt;&nbsp;!--$$_== Not closed ! ==',$p0,strlen($str1));
  continue;
} 
$p3 = $p2 + strlen($str2); // ������� �� ��������� ��������� ������

// �������� �� ����� �� ����������
$tg = explode('_',substr($cnt,$p1,$p2-$p1),2);

$tx = ''; // Html ���, ����� �� ������� ��������

// ������ �� ������� � ��� $tg[0] �� ������� $tn_prefix.'scripts'
//$sc = db_select_1('*','scripts',"`name`='".$tg[0]."'");
$sc = script($tg[0]);

if (!$sc){ // ��� ���� ����� ������ �� ����� ����� � ���� ���
  if( !empty($debug_mode) ) echo "Module ".$tg[0]."(".( isset($tg[1]) ? $tg[1] : '' ).") ";
  $f = strtolower($tg[0]);
  $fn = mod_path($f);
  if ($fn){
    // ��������� �� ���� _style.css, ��� � ������������ �� ������ ��� �����
    $sf = dirname($fn).'/_style.css';
    if(file_exists($sf) && !function_exists($f)){
      $sfn = substr($sf,strlen($apth));
      $page_header .= "<style>\n".rawfile($sfn)."</style>\n";
    }
    // ��������� �� ������ � ����������� �� �������� �� �������
    $c = "include_once('$fn');\n";
    if (isset($tg[1])) $c .= '$tx = '."$f('".addslashes($tg[1])."');";
    else $c .= '$tx = '."$f();";
    if (eval($c)===false){
      // ��� ������ �� ������� ������ �� ���������� � ���������� ���, ����������� ��������
      store_value("eval_error_uri", $_SERVER['REQUEST_URI']);
      store_value("eval_error_code", $c);
    }
  }
  else { // ��� ���� ����� �� ������� ���� �� ����������� ��������� �� �����
    if (show_adm_links()) $tx = ' (Can\'t parse content <a href="'.$adm_pth.'new_mod.php?n='.$tg[0].'">'.$tg[0].'</a>) ';
    else $tx = '<p>Can\'t parse content '.$tg[0].'</p>';
  }
}
else {
  if( !empty($debug_mode) ) echo "Script ".$tg[0]."(".( isset($tg[1]) ? $tg[1] : '' ).") ";
  if (eval(stripslashes($sc['script']))===false){ // ��������� �� ������
     store_value("eval_error_uri", $_SERVER['REQUEST_URI']);
  }
}

// ���������� �� �������� � ����������� html ���, ����� � �������� �� $tx
$cnt = substr_replace($cnt,$tx,$p0,$p3-$p0);

if( !empty($debug_mode) ) echo db_req_count()."<br>\n";

} // ���� �� ������ �� ��������� �� ����������

return $cnt;

} // ���� �� ��������� parce_content()

function script($nm){
static $script = array();
if(!count($script)) {
  $sn = db_select_m('name', 'scripts', 1);
  foreach($sn as $n) $script[$n['name']] = '';
}
if(!isset($script[$nm])) return false;
if(empty($script[$nm])) $script[$nm] = db_select_1('*','scripts',"`name`='$nm'");
return $script[$nm];
}

?>
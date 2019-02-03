<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// ��������� gallery01($a) ����� html ��� �� ��������� �� ������� �� ������.

// ��������� ��� ������ ������ �� �� ������� � ���������� $a ������� �������� ���������� �� �����.
// ��������� ������ ������ ������ �� �� ������� ��� ������� ��� ������ �����, � ������������� "$a/th".

// ���������� �� �������� �� ��������� ��� ���� "$a/titles.php � ����������� ����� � ��� $title, ����� ��� �� �������
// ������� �� ��������� � ��������� - ���������� �� ��������.

// �� �� �� �������� ���������� ��� ��������� ������ ��� ���� titles.php ������ �� � ���������� ����������
// $write_title = true; � �������� ������ ���������� �� �������� ���� ���� ��������� �������.

// � ������, �� ��������� ������ �� ������ �� ����� �������, ��� ���� titles.php
// �� �������� ���������� $no_links = true;

include_once($idir.'lib/f_encode.php');
include_once($idir.'lib/f_set_self_query_var.php');

function gallery01($a){
$i = 0; // ����� �� ��������, ����� �� �� ������
if (isset($_GET['iid'])) $i = 1*$_GET['iid'];
// ��������� ��� �� ������������ ��� ��������
$p = $_SERVER['DOCUMENT_ROOT']."/$a";
// ��������� ���������� �� ��������
$tf = $p."/titles.php";
if (file_exists($tf)){
  include_once($tf);
  // ������ �� *.jpg ���������
  $fl = array_keys($title);
}
else $fl = file_g_list($p,'jpg');
if( !count($fl) ) return encode('�� �� �������� ������� � ���������� ').$p;
if (!isset($write_title)) $write_title = false;
if (!isset($no_links)) $no_links = false;
// ���������� �� html ����
$rz = '<div id="gallery01">'."\n";
if ( !$i || $no_links ) // ��� �� � �������� ����� �� ������, ��� � �������� �� ���� �������
                        // �� �������� ������ ������ � ������ ������
foreach($fl as $j => $f){
  $tn = "$a/th/$f";
  if (!file_exists($_SERVER['DOCUMENT_ROOT']."/$tn")) $tn = "$a/$f";
  if (isset($title[$f])) $tt = encode($title[$f]); else $tt = '';
  if (!$no_links){ $a1 = '<a href="'.set_self_query_var('iid',$j+1).'#big">'; $a2 = '</a>';}
  else { $a1 = ''; $a2 = ''; }
  $rz .= '<div><div>'."\n".
         "$a1<img alt=\"".strip_tags($tt)."\" title=\"".strip_tags($tt)."\" src=\"/$tn\">$a2\n";
  if ($write_title) $rz .= "<br>$tt\n";
  $rz .= "</div></div>\n";
}
else { // ��� � �������� ����� �� ������ �� ������� ���� ���� ������
  if (isset($fl[$i-1])) $f = $fl[$i-1]; else $f = '';
  if (isset($title[$f])) $tt = encode($title[$f]); else $tt = '';
  $k = strlen($_SERVER['DOCUMENT_ROOT']);
  $mp = dirname(__FILE__);
  $mp = substr($mp,$k,strlen($mp)-$k).'/';
  $mp = str_replace('\\','/',$mp);
  // ������ �� ���������
  $nv = "<p id=\"big\">\n";
  if ($i>1) $nv .= '<a href="'.set_self_query_var('iid',$i-1).'#big"><img alt="prev" src="'.$mp.
                   'arrow_prev.gif"></a>'."\n";
  $nv .= '<a href="'.set_self_query_var('iid',0).'#big"><img alt="up" src="'.$mp.'arrow_up.gif"></a>'."\n";
  if ($i<count($fl)) $nv .= '<a href="'.set_self_query_var('iid',$i+1).'#big"><img alt="next" src="'.$mp.
                            'arrow_next.gif"></a>'."\n";
  $nv .= "<p>\n";
  $rz .= "$nv<p><img alt='$tt' src=\"/$a/$f\" class=\"big\"></p>\n";
  $rz .= "<p>$tt</p>\n$nv";
}
$rz .= '</div>
<p style="clear:both;">&nbsp;</p>';
return $rz;
}

// ����� ����� � ������� �� ��������� �� ���������� $p
// ����� ���� ���������� $e
function file_g_list($p,$e){
$r = array(); // �������� ��������
if( !file_exists($p) ) return $r;
if ($d = opendir($p)){
  while(($n = readdir($d))!==false){
    if (strtolower(pathinfo($n,PATHINFO_EXTENSION))==$e) $r[]=$n;
  }
}
sort($r,SORT_NUMERIC);
return $r;
}

?>

<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// �������� �� ��������� �� ��������� �� ���� ��� � .po ��� .pot ����

// ��� �� ������������ �� ��������, ����� �� ��������
$path   = '/Volumes/Extreme_SSD/Sites/vanyog.com/public_html/wp/';
// ��� �� ���� � ��������� ���
$source = '/Volumes/Extreme_SSD/Sites/vanyog.com/public_html/wp/wp-includes/js/dist/edit-site.js';
// ��� �� �������, ����� ������ ����������� �� ����������
$pattern = 'Object\(external_wp_i18n_\["__"\]\)';
// ��� �� .pot �����, � ����� �� ������� ���������� ���������
$pot = '/Users/vanyog/Downloads/bg_BG.po';

// �������� ���� ��������� �����������
if(!file_exists($source)) die ("File not found: $source");
if(!file_exists($pot))    die ("File not found: $pot");

// ������ �� ������������ �� ���������
$cnt = file_get_contents($source);
$cnp = file_get_contents($pot);

// ��������� �� ���������� ��� �� ������
$cnl = explode("\n", $cnt);

// �������� �� ����� �� ����� � ��������� ���
$plen = strlen($path);
$fn = substr($source, $plen);

$rz = '';

// ����������� �� ����� ��� ��������� ���
foreach($cnl as $i=>$l){

  $r = array();
  $n = preg_match_all("/$pattern\('(.*?)'\)/", $l, $r);
  
  // ��� �� �������� ���������
  if($n){
    // ��������� �� ����� ������� ������
    foreach($r[1] as $j=>$s){
      $rz .= "\n#: $fn:".$i."\n".
             "msgid \"".$s."\"\n".
             "msgstr \"\"\n";
    }
  }
}

echo $cnp.$rz;

?>

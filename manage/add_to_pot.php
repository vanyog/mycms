<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// Добавяне на дефиниции на стрингове от сорс код в .po или .pot файл

// Път до директорията на софтуера, който се превежда
$path   = '/Volumes/Extreme_SSD/Sites/vanyog.com/public_html/wp/';
// Път до файл с програмен код
$source = '/Volumes/Extreme_SSD/Sites/vanyog.com/public_html/wp/wp-includes/js/dist/edit-site.js';
// Име на функция, която подава стринговете за превеждане
$pattern = 'Object\(external_wp_i18n_\["__"\]\)';
// Път до .pot файла, в който се добавят намерените стринкове
$pot = '/Users/vanyog/Downloads/bg_BG.po';

// Проверка дали файловете съществуват
if(!file_exists($source)) die ("File not found: $source");
if(!file_exists($pot))    die ("File not found: $pot");

// Четене на съдържанието на файловете
$cnt = file_get_contents($source);
$cnp = file_get_contents($pot);

// Разделяне на програмния код на редове
$cnl = explode("\n", $cnt);

// Отделяне на името на файла с програмен код
$plen = strlen($path);
$fn = substr($source, $plen);

$rz = '';

// Обработване на всеки ред програмен код
foreach($cnl as $i=>$l){

  $r = array();
  $n = preg_match_all("/$pattern\('(.*?)'\)/", $l, $r);
  
  // Ако са намерени стрингове
  if($n){
    // Обработка на всеки намерен стринг
    foreach($r[1] as $j=>$s){
      $rz .= "\n#: $fn:".$i."\n".
             "msgid \"".$s."\"\n".
             "msgstr \"\"\n";
    }
  }
}

echo $cnp.$rz;

?>

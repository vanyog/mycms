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

// Функцията edit_record_form($cp, $tn, $ck = true) връща html код на
// форма за редактиране на запис от таблица $tn на базата данни.
// Параметърът $cp e асоциативен масив с ключове - имената на полетата,
// и стойности - пояснителни надписи, които се поставят пред тези полета във формата.
// В този масив трябва да има и елемент 'ID'=>НомерНаЗаписа, който не се редактира.
// $ck определя дали пред textarea полетата да се показва бутон за включване на CKEditor

// След име на поле, може да има добавка:
// '|file', когато полето съдържа път до файл, който се качва в директория $GLOBALS['upfile_path']


include_once($idir."lib/f_db_field_names.php");
include_once($idir."lib/f_db_field_types.php");
include_once($idir."lib/f_db_table_field.php");
include_once($idir."lib/f_db_show_columns.php");
include_once($idir."lib/f_db_enum_values.php");
include_once($idir."lib/f_element_correction.php");
include_once($idir."lib/o_form.php");

function edit_record_form($cp, $tn, $ck = true){// print_r($cp); die;
global $countries;
// Прочитане имената на полетата на таблицата
$fn = db_field_names($tn);
// Прочитане типовете на полетата на таблицата
$ft = db_show_columns($tn, '', 'Type');
// Съставяне на нов асоциативен масив с ключове имената на полетата и стойности - типовете им
$ft = array_combine($fn, $ft);
// Прочитане стойностите по подразбиране на полетата на таблицата
$fd = db_show_columns($tn, '', 'Default');
// Съставяне на нов асоциативен масив с ключове имената на полетата и стойности - типовете им
$fd = array_combine($fn, $fd);
// Прочитане на записа, който ще се редактира
$d = db_select_1('*', $tn, "`ID`=".$cp['ID']);
// Връщан резултат
$rz = '';
// Максимална дължина на текстовите полета
$max_size = 80;
// Максимален брой стълбове на текстовите области
$max_cols = 60;
// Максимален брой редове на текстовите области
$max_lines = 25;
// Масив за специални опции, като '|file'
$opt = array();
// Съставяне на формата
$hf = new HTMLForm('editrecord_form');
// Добавяне на елементи за всяко от полетата, които ще се редактират
foreach($cp as $n => $v){
  switch ($n) {
  case 'ID': // Номерът - скрито поле
    $fi = new FORMInput('', 'ID', 'hidden', $v);
    $hf->add_input($fi);
    break;
  default:
    if(!isset($ft[$n])){
       $fa = explode('|',$n);
       if(count($fa)==1) die("Field $n do not exist in table $tn.");
       $n = $fa[0];
       $opt[$n] = $fa[1];
    }
    // Анализиране типа на полетата
    preg_match('/([a-z]*)\((.*)\)/', $ft[$n], $tp);
    if (count($tp)<2) $tp[1] = $ft[$n];
    switch ($tp[1]){
    case 'varchar': case 'datetime':
      $t = 'text';
      if ($n=='country'){
        if (isset($countries[$d[$n]])) $vl = $countries[$d[$n]];
        else $vl = 'Bulgaria';
        $fi = formCountrySelect($v, $n, $vl );
        $hf->add_input($fi);
        break;
      }
      if ($n=='password'){
        $vl = '';
        $t = $n;
        $fi =  new FORMInput($v, $n, $t, $vl);
        if ($tp[2]<$max_size) $fi->size = $tp[2];
        else $fi->size = $max_size;
        $hf->add_input($fi);
        $n = 'password2';
        $v = translate('user_passwordconfirm');
      }
      else { $vl = htmlspecialchars(stripslashes($d[$n]), ENT_COMPAT, 'cp1251'); }
      // Ако полето е за попълване на дата и час, се попълва с текущите дата и час
      if (($tp[1]=='datetime')&& !$vl) $vl = date("Y-m-d H:i:s");
      if(!empty($opt[$n])){
         $t = $opt[$n];
//         die($vl);
      }
      $fi =  new FORMInput($v, $n, $t, $vl);
      $fi->size = 80;
      $hf->add_input($fi);
      break;
    case 'text': case 'mediumtext':
      $vl = str_replace('&', '&amp;', stripslashes($d[$n]) );
      $ms = '';
      if (!(strpos($vl,'<!--$$_')===false)) $ms = '<p class="message">'.translate('edit_record_form_$$').'</p>';
      $la = explode("\n", $vl);
      $lc = count($la);
      if ($lc<3) $lc = 3;
      if ($lc>$max_lines) $lc = $max_lines;
      $fi = new FormTextArea($cp[$n].$ms, $n, $max_cols, $lc, $vl);
      if (!$ck) $fi->ckbutton = '';
      $fi->js = ' onclick="onTeFocus();"';
      $hf->add_input( $fi );
      break;
    case 'int':
      $vl = $d[$n];
      $fi =  new FORMInput($v, $n, 'text', $vl);
      $hf->add_input($fi);
      break;
    case 'tinyint': switch($tp[2]){
      case 1:
        $fi =  new FORMInput($v, $n, 'checkbox', 1);
        if ($d[$n]) $fi->checked = ' checked';
        $hf->add_input($fi);
        break;
        case 4:
        case 11:
          $fi =  new FORMInput($v, $n, 'text', $d[$n]);
          $hf->add_input($fi);
          break;
      default: die("Unknown subtype of '$ft[$n]' $tp[2]");
      }
      break;
    case 'enum':
      $op = str_getcsv($tp[2], ',', "'");
      $i = array_search($d[$n], $op);
      if(($i===false) && $fd[$n]) $i = array_search($fd[$n], $op);;
      $fi =  new FormSelect($v, $n, $op, $i);
      if ($d[$n]) $fi->checked = ' checked';
      $hf->add_input($fi);
      break;
    case 'float':
        $vl = str_replace(',', '.', $d[$n]);
        $fi =  new FORMInput($v, $n, 'text', $vl);
        $hf->add_input($fi);
        break;
    default: die("Unknown type '$ft[$n]' of field `$n`");
    }
  }
}
$i = new FORMInput('','','submit',translate('saveData'));
$i->js = ' class="sub"';
$hf->add_input( $i );
$rz .= $hf->html();
return $rz;
}

// Записване на попълнените във формата данни.
// $cp - изпратените данни, най-често съвпада с масива $_POST
// $tn - име на таблицата
// $m  - дали да се показва съобщение при успешен запис.

function process_record($cp, $tn, $m = true){//die(print_r($cp,true));
global $tn_prefix, $db_link;
// Прочитане типовете на полетата на таблицата
$ft = db_field_types($tn);
// Прочитане имената на полетата на таблицата
$fn = db_field_names($tn);
// Съставяне на нов асоциативен масив с ключове имената на полетата и стойности - типовете им
$ft = array_combine($fn, $ft);// print_r($ft); die;
$k = array_keys($cp); // Масив от имената на полетата, за които са изпратени данни.
$rz = ''; // Връщан резултат - надпис, относно резултата от запазването на данните.
$w = ''; // WHERE частта на SQL заявката.
$pu = false; // Дали да се обновят данните за потребителя в текущата сесия.
// Използва се, когато се редактира записът с данни на текущия потребител.
// Създаване на нов запис
if(empty($cp['ID'])){
  $q = "INSERT INTO `$tn_prefix$tn` (`ID`) VALUES (NULL);";
  mysqli_query($db_link,$q);
  $cp['ID'] = mysqli_insert_id($db_link);
//  die(print_r($cp,true));
}
$q = ''; // SQL заявка, която се генерира.
foreach($k as $n) switch($n){
case 'MAX_FILE_SIZE':
  if(!empty($cp['ID'])) foreach($_FILES as $f=>$a) if(!$a['error'])
  {
     if(empty($GLOBALS['upfile_path'])) die('$GLOBALS[\'upfile_path\'] is not defined.');
     $fln = $_SERVER['DOCUMENT_ROOT'].$GLOBALS['upfile_path'].$cp['ID'].'.'.pathinfo($a['name'], PATHINFO_EXTENSION );
     $m = move_uploaded_file($a['tmp_name'], $fln);
     if($m){
        if ($q) $q .= ', ';
        $q .= "`$f`='$fln'";
     }
  }
  break;
case 'ID':
  // Ако не е изпратен номер на запис, не се прави нищо.
  if (!isset($cp['ID'])) return;
  // За проверка дали има запис с изпратения номер:
  $id = db_table_field('ID', $tn, "`ID`=".(1*$cp['ID']));
  // Ако има запис с изпратения номер се генерира заявка UPDATE,
  // а в противен случай - заявка INSERT.
  if ($id) $w = " WHERE `ID`=".(1*$cp['ID']).";";
  break;
case 'password':
  // Ако е изпратена нова парола и нейно повторение
  if ( isset($cp['password2']) && $cp['password2'])
    if ( ($cp['password2']==$cp['password']) ){
      if ($q) $q .= ', ';
      $q .= "`$n`='".sha1($cp[$n])."'";
      $pu = true;
      $rz .= '<span class="message">'.translate('user_passwordchanged')."</span><br>\n";
    }
    else $rz .= '<span class="warning">'.translate('user_passwordinvalid')."</span><br>\n";
  break;
default:
  if ($q) $q .= ', ';
  if ($ft[$n]==3){ // Цяло число
    if (isset($cp[$n])) $q .= "`$n`='".(1*$cp[$n])."'";
    else $q .= "`$n`=0";
  }
  else if ($ft[$n]==4){ // Реално число
    $v1 = str_replace(',', '.', $cp[$n]);// die($v1);
    $q .= "`$n`='".addslashes($v1)."'";
  }
  else {
    $v1 = element_correction($cp[$n]);
    if( ($ft[$n]==12) && ($cp[$n]=='NOW()') )
       $q .= "`$n`=NOW()";
    else
       $q .= "`$n`='".addslashes($v1)."'";
  }
}
//echo "<br>$q<br>".print_r($fn,true)."<br>";
// Обновяване данните за потребителя в текущата сесия.
if ($pu) process_user();
// Обновяване данните в базата данни.
//if (in_array('date_time_2', $fn)) $q = "`date_time_2`=NOW(), $q";
if ($w) $q = "UPDATE `$tn_prefix"."$tn` SET $q$w";
else {
//  if (in_array('date_time_1', $fn)) $q = "`date_time_1`=NOW(), $q";
  $q = "INSERT INTO `$tn_prefix"."$tn` SET $q;";
}
//die($q);
if (mysqli_query($db_link,$q) && $m) $rz .= '<span class="message">'.translate('dataSaved')."</span>";
//echo "$q<p>".print_r($cp,true)."<p>".print_r($_FILES,true); die();
if ($rz) $rz = '<p class="message">'.$rz.'</p>';
return $rz;
}

?>

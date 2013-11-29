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

// Сливане на две таблици от базата данни.
// Чете данните от таблица $_GET['t1'] и ги записва и в таблица $_GET['t2'].
// Параметрите t1 и t2 са пълните имена на таблиците с префиксите.

// При съвпадение на записите не прави нищо.
// При конфликт между записите спира и показва конфликта.

include('conf_manage.php');
include($idir.'lib/f_db_select_1.php');
include($idir.'lib/f_db_select_m.php');
include($idir.'lib/f_db_insert_1.php');
include($idir.'lib/o_form.php');

if (!isset($_GET['t1'])) die('Липсва параметър t1=ИмеНаПърваТаблица');
if (!isset($_GET['t2'])) die('Липсва параметър t2=ИмеНаВтораТаблица');

$t1 = $_GET['t1'];
$t2 = $_GET['t2'];

set_prefix('');

session_start();

process_post();

// Четене на цялата първа таблица
$d = db_select_m('*',$t1,'1');

// Брой прочетени записи
$c = count($d);

$page_content = "<p>Прочетени от таблица $t1 $c записи.</p>\n";

// $fn - първото име на поле, различно от 'ID'.
$fn = '';
if ($c) $fn = fnoidfield($d[0]);

if (!$fn) $page_content .= "<p class=\"warning\">Няма име на поле, различно от 'ID'.</p>";

// Брой пропуснати
$sk = 0;

// За всеки прочетен запис
if ($c) foreach($d as $r){
  // Ако е избрано пропускане
  if (!(strpos($_SESSION['records_to_skip'], ",".$r['ID']."," )===false)){ $sk++; continue; }
  // Прочитат се всички записи от втората таблица, на които поле $fn съвпада със същото поле на поредния запис от първата таблица
  $d2 = db_select_m('*',$t2,"`$fn`='".$r[$fn]."'");
  // Ако няма такива записи, записа от първата таблица се вмъква във втората.
  if (!count($d2)) {
    $r1 = $r;
    unset($r1['ID']);
    $i = db_insert_1($r1,$t2);
  }
  // Иначе сравнява записите
  else{
   // Ако намери съвпадение прекратява сравняването
   foreach($d2 as $r2) if ($s = same_records($r,$r2)) break;
   // Ако е открита разлика спира и пита какво да се направи
   if (!$s){
     $page_content .= what_to_do($r,$d2);
     break;
   }
  }
}

$page_content .= "<p>Пропуснати: $sk.</p>";

include('build_page.php');

//
// Първото име на поле различно от ID 
//
function fnoidfield($a){
if (!is_array($a)) return '';
$ks = array_keys($a);
foreach($ks as $k) if ($k!='ID') return $k;
return '';
}

//
// Сравнява записите $r1 и $r2.  Връща истина ако се различават само по 'ID'.
//
function same_records($r1,$r2){
foreach($r1 as $f => $v){
  // Ако във втория запис няма поле със същото име - разлика в структурата на таблиците
  if (!isset($r2[$f])){
    print_r($r1); echo "<br>"; 
    print_r($r2); echo "<br>";
    die("Двете таблици нямат еднаква структура");
  }
  if ( ($f!='ID') && ($r1[$f]!=$r2[$f]) ) return false;
}
return true;
}

//
// При откриване на разлика в записите се показва форма за избор какво да се направи
//
function what_to_do($r,$d2){
$rz = "<p>\n".print_r($r,true)."<br>\n";
foreach($d2 as $d) $rz .= print_r($d,true)."<br>\n";
$rz .= "</p>\n";
$c = count($d2);
$f = new HTMLForm('whattodo');
$f->add_input( new FormInput('','id1','hidden',$r['ID']));
if ($c==1) $f->add_input( new FormInput('','id2','hidden',$d2[0]['ID']));
$f->add_input( new FormInput('Избирете:','what','radio','insert','Да се вмъкне'));
$f->add_input( new FormInput('','what','radio','skip','Да се пропусне'));
if ($c==1) $f->add_input( new FormInput('','what','radio','update','Да замести'));
$f->add_input( new FormInput('','what','radio','terminate','Край на сливането'));
$f->add_input( new FormInput('','','submit','Продължаване')); 
return $rz.$f->html();
}

//
// Обработка на изпратените с $_POST данни
//
function process_post(){
if (!count($_POST)) return '';
global $t1,$t2;
$id1 = 1*$_POST['id1'];
// Четене на записа от таблица $t1
$r1 = db_select_1('*',$t1,"`ID`=$id1");
switch ($_POST['what']){
case 'insert':
  unset($r1['ID']);
  db_insert_1($r1,$t2);
  break;
case 'skip':
  if (!isset($_SESSION['records_to_skip'])) $_SESSION['records_to_skip'] = ',';
  $_SESSION['records_to_skip'] .= "$id1,";
//  print_r($_SESSION); die;
  break;
  case 'terminate':
  unset($_SESSION['records_to_skip']);
  die("Сливането беше прекратено.");
  break;
default: die("Непознато действие ".$_POST['what']);
}
return '';
}

?>

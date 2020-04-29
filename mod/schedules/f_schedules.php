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

// Работа с графици

include_once($idir.'lib/f_dbform_select_value.php');
include_once($idir.'lib/f_view_table.php');
include_once($idir.'lib/f_db2user_date_time.php');
include_once($idir.'lib/o_form.php');

// Главна функция на модула

// Ако параметърът $a == '' функцията генерира страница за управление на графиците.

// Ако $a не е празен стринг, е разделен на части от символа |.

// Първата част е името на график.
// Втората - действие, което се извършва с този график:
//    'current' - показване на текущите събития от графика;
//    'next30' - показване на започващите през следващите 30 дни събития от графика;
//    'all' - показване на всички събития от графика;
//    име на събитие - показване само на датата на събитието

function schedules($a = ''){
if ($a){
  $p = explode('|',$a);
  if (count($p)!=2) return "Invalid parameters for schedules module.";
  switch ($p[1]) {
  case 'current': return schedules_current($p[0]);    break;
  case 'next30' : return schedules_next30($p[0], 30); break;
  case 'all'    : return schedules_all($p[0]);        break;
  default: $r = db_select_1('*', 'schedules', "`sch_name`='".$p[0]."' AND `ev_name`='".$p[1]."'");
     if($r) return schedules_ev($r);
     return 'Invalid action name \''.$p[1].'\' for schedules module.'; break;
  }
}
global $page_header, $can_manage;
// Ако потребителят няма право да използва модула - съобщение
if (!isset($can_manage['schedules']) || !$can_manage['schedules']) 
   return '<p class="message">'.translate('schedules_nopermition').'</p>';
// Име на текущия график
$l = '';
if (isset($_GET['schn'])) $l = $_GET['schn'];
// Адрес за смяна с друг график
$q = set_self_query_var('schn','aaa',false);
$q = str_replace('aaa','"+n', $q);
// Javascript за смяна на графика с избрания от падащия списък
$page_header .= '<script>
function sch_name_changed(){
var l = document.getElementsByName("sch_name");
var n = l[0].value;
document.location = "'.$q.';
}
</script>';
// Падащ списък за избиране на график
if (!$l) $l = db_table_field('sch_name', 'schedules', '1 ORDER BY `ID` DESC');
$s = schedules_select($l);
if (!session_id()) session_start();
$_SESSION['http_referer'] = $_SERVER['REQUEST_URI'];
return $s.'
<p><a href="'.current_pth(__FILE__).'new.php">'.translate('schedules_linktextnew').'</a></p>
<p>&lt; !--$$_SCHEDULE_'.$l.'_$$--&gt;</p>
<h2>'.translate($l).'</h2>
'.schedules_table($l);
}

// HTML код на форма за избиране на график

function schedules_select($l){
// Четене имена на графици
$da = db_select_m('sch_name', 'schedules', '1 GROUP BY `sch_name` ORDER BY `ID` DESC');
$sl = array();
foreach($da as $d){
  $sl[$d['sch_name']] = db_table_field('text', 'content', "`name`='".$d['sch_name']."'");
}
$f  = new HTMLForm('schedul_select');
$fs = new FormSelect(translate('schedules_schlist'), 'sch_name', $sl, $l);
$fs->values='k';
$fs->js = ' onchange="sch_name_changed();"';
$f->add_input( $fs );
return $f->html();

}

// Показване таблицата на събитията от графика с име $n

function schedules_table($n){
$d = db_select_m('*', 'schedules', "`sch_name`='$n' ORDER BY `date_time_1` ASC, `date_time_2` ASC");
$d = schedules_data_prepare($d);
$a = array(
'ID'=>'ID',
'ev_name'=>translate('schedules_event'),
'date_time_1'=>translate('schedules_datetime1'),
'date_time_2'=>translate('schedules_datetime2'),
'copy'=>'Copy'
);
// Брой на наличните графици
$sn = db_table_field('COUNT(*)','schedules','1');
// Връщан резултат
$rz = view_table($d, 'sch_table', $a);
if ($sn) $rz .= '
<p><a href="'.current_pth(__FILE__).'newevent.php?schn='.$n.'">'.translate('schedules_newenent').'</a></p>';
return $rz;
}

// Променяне на данните преди показването им в таблица

function schedules_data_prepare($d){
global $adm_pth;
$c = 1;
for($i=0; $i<count($d); $i++){// die(print_r($d,true));
  $d[$i]['copy'] = '<a href="'.$adm_pth.'new_record.php?t=schedules'.
                   '&sch_name='.$d[$i]['sch_name'].
                   '&ev_name=schedule_event_'.(db_table_field('MAX(`ID`)', 'schedules', '1')+1).
                   '&date_time_1='.$d[$i]['date_time_1'].
                   '&date_time_2='.$d[$i]['date_time_2'].'">2</a>';
  $d[$i]['ev_name'] = translate($d[$i]['ev_name']);
  $d[$i]['date_time_1'] = '<span class="nowrap"><a href="'.current_pth(__FILE__).'edit.php?id='.$d[$i]['ID'].'">'.
                          db2user_date_time($d[$i]['date_time_1']).'</a></span>';
  $d[$i]['date_time_2'] = '<span class="nowrap">'.db2user_date_time($d[$i]['date_time_2']).'</span>';
  $c++;
}
return $d;
}

// Показване на текущите събития от графика с име $s

function schedules_current($s){
// Текущата дата и час в MySQL формат
$ct = date('Y-m-d H:i:s', time());
// Четене на текущите събития
$ce = db_select_m('*','schedules',
      "`sch_name`='$s' AND `date_time_1`<='$ct' AND `date_time_2`>='$ct' ORDER BY `date_time_1` ASC, `date_time_2` ASC");
// Връщан резултат
$rz = '';
foreach($ce as $e){
 $rz .= '<p><span class="date_time">'.db2user_from_to($e['date_time_1'],$e['date_time_2']).'</span>
<br>'.translate($e['ev_name'])."</p>\n";
}
return $rz;
}

// Показване на предстоящите през следващите $n дни събития от графика с име $s

function schedules_next30($s, $n){
// Текущата дата и час в MySQL формат
$t1 = date('Y-m-d H:i:s', time());
// Дата и час в MySQL формат след $n дни
$t2 = date('Y-m-d H:i:s', time()+$n*24*3600);
// Четене на събитията, започващи през следващите $n дни
$ce = db_select_m('*','schedules',
      "`sch_name`='$s' AND `date_time_1`>='$t1' AND `date_time_1`<='$t2' ORDER BY `date_time_1` ASC, `date_time_2` ASC");
// Връщан резултат
$rz = '';
foreach($ce as $e){
   $rz .= '<p><span class="date_time">'.db2user_from_to($e['date_time_1'],$e['date_time_2'])."</span>\n<br>".
          translate($e['ev_name'])."</p>\n";
}
if(!$rz) $rz = translate('schedules_noact');
return $rz;
}

// Показване на предстоящите през следващите $n дни събития от графика с име $s

function schedules_all($s){//die($s);
// Четене на събитията
$ce = db_select_m('*', 'schedules', "`sch_name`='$s' ORDER BY `date_time_1` ASC, `date_time_2` ASC");
// Текущата дата и час в MySQL формат
$ct = date('Y-m-d H:i:s', time());
// Връщан резултат
$rz = '';
$y0 = ''; // Последно показана година
$m0 = ''; // Последно показан ден
$hr = false; // Дали и сложена линия на днешната дата
eval(translate('month_names'));
$c = 0; // Брой показани събития
foreach($ce as $e){
 $y = substr($e['date_time_1'], 0, 4);
 $m = substr($e['date_time_1'], 5, 2);
 if(empty($GLOBALS['schedules_noyear']) && ($y!=$y0)){
   $rz .= "<h3>$y</h3>\n";
   $y0 = $y;
 }
 // Хоризонтална линия на днешната дата
 if(empty($GLOBALS['schedules_noline']) && !$hr && ($e['date_time_1']>$ct) ){
   $hr = true;
   if($c){// echo("<p>$c<br>$rz");
     if(!isset($GLOBALS['schedules_tcount'])) $GLOBALS['schedules_tcount'] = 0;
     else $GLOBALS['schedules_tcount']++;
     $rz .= "<hr id=\"today".$GLOBALS['schedules_tcount']."\">\n";
   }
 }
 if(empty($GLOBALS['schedules_nomonth']) && ($m!=$m0)){
   $rz .= "<h4>".$month[1*$m]."</h3>\n";
   $m0 = $m;
 }
 $st = '';
 if($ct>=$e['date_time_1'] && $ct<=$e['date_time_2']) $st = ' class="current"';
 if($ct>$e['date_time_2'])                            $st = ' class="past"';
 $rz .= "<p$st>".'<span class="date_time">';
 if(empty($GLOBALS['schedules_nostart']))
    $rz .= db2user_from_to($e['date_time_1'],$e['date_time_2']);
 else
    $rz .= db2user_date_time($e['date_time_2'], false);
 if(in_edit_mode()){
   $p = current_pth(__FILE__);
   $rz .= ' <a href="'.$p.'edit.php?id='.$e['ID'].'">*</a>';
 }
 $rz .= "</span>\n".
        '<br>'.translate($e['ev_name'])."</p>\n";
 $c++;
}
return '<div class="schedule">'."\n$rz</div>\n";
}

//
// Връща истина ако е в ход събитие $e от график $s

function schedules_in_event($e,$s){
// Текущата дата и час в MySQL формат
$ct = date('Y-m-d H:i:s', time());
// Номер на събитието $e от график $s, ако текущото време не е извън сроковете му
$id = db_table_field('ID', 'schedules', 
      "`sch_name`='$s' AND `ev_name`='$e' AND `date_time_1`<='$ct' AND `date_time_2`>='$ct'", 0);
$rz =  "$id"!="0";
return $rz;
}

function schedules_ev($r){
return db2user_date_time($r['date_time_2']);
}

?>

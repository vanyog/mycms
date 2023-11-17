<?php
/*
VanyoG CMS - a simple Content Management System
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

// Модул за научна конференция
// За повече информация - README.txt

include_once($idir.'lib/f_mod_path.php');
include_once(mod_path('userreg'));
include_once(mod_path('schedules'));
include_once(mod_path('emailsend'));
include_once($idir.'lib/f_view_record.php');
include_once($idir.'lib/f_db_insert_1.php');
include_once($idir.'lib/f_db_update_where.php');
include_once($idir.'lib/f_view_table.php');
include_once($idir.'lib/f_translate_to.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'lib/f_message.php');
include_once($idir.'mod/usermenu/f_usermenu.php');
include_once($idir.'mod/uploadfile/f_uploadfile.php');

global $user_table, $utype, $fdir,
       $today, $day_a_submit, $day_a_approve, $day_start_rev, $day_start, $day_program, $year, $day2, 
       $adm_pth, $page_header, 
       $page_hash, $proccount, $plogin, $pedit, $editing;

$editing = in_edit_mode();

// Таблица с данни за потребителите
$user_table = stored_value('user_table', 'users');
// Тип на потребителите
$utype = stored_value('conference_usertype', 'vsu2014');
// Адрес на страницата за влизане
$plogin = stored_value('userreg_login_'.$utype);
// Адрес на страницата за редактиране на личните данни
$pedit = str_replace('&user2=login', '&user2=edit', $plogin);

// Директория за качване на файлове
$fdir = stored_value('conference_files_'.$utype, '/conference/2014/files/');

// Име на събитие и име на график от таблица 'schedules' на срока за качване на пълния текст на докладите
$day2 = explode(',', stored_value('conference_day2event','schedule_event_4,schedule_1'));
// Брой разрешени доклади
$proccount = stored_value('conference_repnumber', 2);

$page_header .= '<script>
function confDelPaper(e){
if (!confirm("'.translate('conference_confirmdel', false).'")) return;
document.location = e;
}
function confDelUser(e){
if (!confirm("'.translate('conference_confirmdeluser', false).'")) return;
document.location = e;
}
</script>
';

// Настоящия момент
$today = date('Y-m-d H:i:s');

// Срок за приемане на резюмета
$day_a_submit = conference_important_day('conference_day_a_submit');
// Срок за потвърждаване приемането на резюметата
$day_a_approve = conference_important_day('conference_day_a_approve');
// Започване на рецензирането
$day_start_rev = conference_important_day('conference_day_start_rev');
// Обявяване на програмата
$day_program = conference_important_day('conference_day_program');
// Започване на конференцията
$day_start = conference_important_day('conderence_day_start');
// Година на провеждане
$year = substr($day_start,0,4);

// Важна дата в MYSQL формат
// $n - има на срока от таблица 'options'

function conference_important_day($n){
$i = stored_value($n);
if(!$i) die("No '$n' deadline has been set in 'options' table.");
$r = db_select_1('date_time_2', 'schedules', "`ID`='$i'");
if(!isset($r['date_time_2'])) die("Incorrect value `$n`='$i' in 'options' table.");
return $r['date_time_2'];
} 

// Главна функция на модула
// Ако не са използвани, описаните по-долу опции, е предназначена да генерира 
// лична страница на участник от 2 части: лични данни и доклади.
// Параметърът $a може да се състои от две части, отделени с |
// При различни стойности на първата част функцията връща:
// '' - Лична страница на потребителя с неговите лични данни и данни за заявените доклади
// 'admin' - Страница за администриране със списък на всички участници и техните доклади
// 'edit' - Форма за редактиране данните за доклад
// 'stats' - Текуща статистика
// 'abstract_t' - Заглавия на приетите доклади с линкове към файловете с пълните им текстове и презентациите.
//                При хостване на повече от едно издание на конференция, след знак | се посочва типът участници,
//                с цел идентифициране на изданието.
// 'abstracts' - Сборник резюмета
// 'review'   - Попълване на рецензия за доклад с номер $_GET['rev2']
// 'procrevs' - Рецензии на доклад с номер $_GET['proc']
// 'participants' - Имейли на потребителите в обратан ред на последната актуализация
// 'topics' - НТML код - списък на тематибните направления и краткото им оисание
// 'program' - програма на конференцията

// Втората част на параметъра $a, след знака | съдържа други параметри

function conference($a = ''){

global $user_table, $can_manage, $utype, $today, $day_a_submit, $day2, $proccount, $pth, 
       $plogin, $pedit, $page_hash, $page_header, $body_adds, $day_start_rev;

// Номер на влезлия потребител
$uid = userreg_id($utype);

// Дали потребителят е администратор
$adm = isset($can_manage['conference']) && $can_manage['conference'];

$aa = explode('|', $a);
if(!isset($aa[1])) $aa[1] = '';

if(isset($_GET['rev1'])) return conference_rev1($uid, $adm); // Рецензиране на резюме
if(isset($_GET['rev2'])) return conference_rev2($uid, $adm); // Рецензиране на пълен текст

switch ($aa[0]){
case ''            : break;
case 'admin'       : return conference_admin($uid);
case 'edit'        : return conference_edit($uid);
case 'stats'       : return conference_stats();
case 'abstract_t'  : return conference_abstract_titles($aa[1]);
case 'abstracts'   : return conference_abstract_book($uid, $adm, $aa[1]);
case 'participants': return conference_participants();
case 'review'      : return message("Missing report ID.");
case 'procrevs'    : return conference_procRevs();
case 'topics'      : return conference_topics($aa[1]);
case 'program'     : return conference_program();
default            : return message("Unknown parameter value '$a' in 'conference() function.");
}

// Обработка на изтратени с $_POST данни
if(isset($_POST) && count($_POST)) $ms = conference_process();

// Лични данни на потребителя
$d = db_select_1('*', $user_table, "`ID`=$uid");
$cp = array(
'language'=>translate('user_language'),
'username'=>translate('user_username'),
'email'=>translate('user_email'),
'firstname'=>translate('user_firstname'),
'secondname'=>translate('user_secondname'),
'thirdname'=>translate('user_thirdname'),
'country'=>translate('user_country'),
'institution'=>translate('user_institution'),
'position'=>translate('user_position'),
'address'=>translate('user_address'),
'telephone'=>translate('user_telephone')
);

add_style('conferenceProfile');
$rz = '';
// Данни за докладите на потребителя
$pd = db_select_m('*', 'proceedings', "`user_id`=$uid AND `utype`='$utype' ORDER BY `ID` ASC");
// Съобщение за превишаване броя
if(count($pd)>$proccount) $rz .= message(translate('conference_manyreps'));

// Ако потребителят е потвърден рецензент
$rl = conference_userRevList($uid);
if($rl) $rz .= '<h2>'.translate('conference_reviewing')."</h2>\n".$rl;
       
$rz .= '<h2>'.translate('conference_mydata').'</h2>
'.view_record($d, $cp);
// Линк "Редактиране на профила"
$rz .= '<p><a href="'.$pedit.$page_hash.'">'.translate('conference_mypersonal')."</a>";
// Линк "Изтриване на профила"
if(!count($pd)) $rz .= ' &nbsp; <a href="'.$pth.
                'mod/userreg/delete_me.php" onclick="confDelUser(this);return false;" class="danger">'.
                translate('conference_deleteuser');
$rz .= "</a></p>\n";

$cp = array(
'approved_a'=>translate('conference_approvedA'),
'fee'=>translate('conference_fee'),
'date_time_1'=>translate('conference_cdate1'),
'date_time_2'=>translate('conference_cdate2'),
'form'=>translate('conference_cform'),
'topic'=>translate('conference_ctopic'),
'title'=>translate('conference_ctitle'),
'authors'=>translate('conference_cauthors'),
'addresses'=>translate('conference_caddresses'),
'keywords'=>translate('conference_ckeywords'),
'keywords'=>translate('conference_ckeywords'),
'abstract'=>translate('conference_cabstract'),
'abstracttextfile'=>translate('conference_cabstracttextfile'),
'fulltextfile'=>translate('conference_cfulltextfile'),
'fulltextfile2'=>translate('conference_cfulltextfile2'),
'fulltextfile3'=>translate('conference_cfulltextfile3')
);
$rz .= '<h2>'.translate('conference_mypaper')."</h2>\n";

// Показване данните за всеки доклад
for($i=0; $i<=count($pd); $i++){
if ($i<count($pd)) $rz .= '<h3>'.translate('conference_'.($i+1).'paper')."</h3>\n";
// Дали e период за редактиране
$et = ($today < $day_a_submit) || $adm;
if (!$et && !count($pd)) return $rz.message(translate('conference_noabs1'));
// Дали е период за качване на доклади
$ut = schedules_in_event($day2[1],$day2[0]) || $adm;
// Адрес на страницата за редактиране на резюме и качване на доклад от участник
$edp = stored_value('conference_editpaper', '/index.php?pid=1068');
// Показване на следващ доклад, ако има право на такъв
if ( $et && ($i=count($pd)) && ($i<$proccount) )
    $rz .= '<h3>'.translate('conference_'.($i+1).'paper')."</h3>\n";
if (isset($pd[$i]['title'])) 
    $rz .= "<ptitle>".mb_strtoupper($pd[$i]['title'])."</ptitle>\n";
// Линк "Редактиране"
if ($i<count($pd)){
    $rz .= '<p><a href="'.$edp.'&amp;proc='.$pd[$i]['ID'].$page_hash.'">'.
           translate('conference_editpaper').'</a>';
} else 
   if ($et && ($i<$proccount))
      $rz .= '<p><a href="'.$edp.$page_hash.'">'.translate('conference_editpaper').'</a>';
// Линк "Изтриване"
if ($i<count($pd)) $rz .= ' &nbsp; <a href="'.current_pth(__FILE__).'delete_paper.php?a=1'.
                     '&amp;proc='.$pd[$i]['ID'].
					 '" onclick="confDelPaper(this);return false;" class="danger">'.
					 translate('conference_deletepaper').'</a>';
$rz .= "</p>\n";
$js = '';
if ($i<count($pd)){
   // Мнения на рецензентите
   $locked = 0;
   $rm = conference_viewRev2rezult($pd[$i], $locked);
   if($rm){
      if(!$locked) $rz .= message(translate('conference_revNotice'));
   }
   else
      if ($today > $day_start_rev) $rz .= message(translate('conference_noRevYet'));
   $rz .= $rm;
   if(db_table_field('COUNT(*)', 'reviewer_work', 
                     "`proc_id`=".$pd[$i]['ID']." AND `decision`=3",false,false )
     ){
      $js .= 'CKEDITOR.replace("revanswer");'."\n";
      $f = new HTMLForm('conference_varA_'.$pd[$i]['ID']);
      $f->add_input( new FormInput('', 'ID', 'hidden', $pd[$i]['ID']));
      $fi = new FormTextArea(translate('coference_revAnswer'), 'revanswer', 100, 10, $pd[$i]['revanswer']);
      $fi->ckbutton = '';
      $f->add_input($fi);
      $f->add_input( new FormInput('','','submit',translate('conference_saveRevAnswer')));
      $rz .= $f->html();
   }
   $rz .= view_record(conference_trprec($pd[$i]), $cp);
}
if($js){ 
  $js = "function activate_ckeditor(){\n$js}";
  $page_header .= "<script>\n$js\n</script>\n";
  $body_adds .= ' onload="activate_ckeditor()"';
}
if($i==0) $rz .= '
<p>&nbsp;</p>
'.translate('conference_feenote').'
<p>&nbsp;</p>
';
}

$rz .= '
<p>&nbsp;</p>';

return $rz;
}

// Обработка на изпратени данни

function conference_process(){
//die(print_r($_POST,true));
$rz = '';
if(isset($_POST['ID']) && ($_POST['ID']==db_update_record($_POST, 'proceedings')))
   $rz .= translate('conference_savedRevAnswer');
return $rz;
}

// Списък заглавия на доклади, които трябва да рецензира, потребител с номер $uid
// При $url=false - заглавията са хипервръзки
// При $status=true - под всяко заглавие се показва датата на последната редакция на рецензия

function conference_userRevList($uid, $url=false, $status=true){
global $utype, $fdir, $page_hash;
$rz = '';
// Данни за рецензии
$rv = db_select_m('*', 'reviewers', 
                  "`utype`='$utype' AND `user_id`=$uid AND `confirmed`=1 ORDER BY `topic` ASC",false);
if(count($rv)){
  $rvp = stored_value('conference_reviewpage');
  foreach($rv as $r){ // За всяка рецензия
    $rp = db_select_m('*', 'reviewer_work', '`rev_id`='.$r['ID']);
    foreach($rp as $p){
      // Дани на доклад
      $d = db_select_1('title,fulltextfile4', 'proceedings', "`ID`=".$p['proc_id'],false);
      $cr = '';
      $fe = conference_file_age($d['fulltextfile4']);
      if($fe > $p['date_time_2']) 
         $cr = ' <span style="color:red;">CORRECTED</span> '.db2user_date_time($fe);
      if($url){ // Неформатиран текст
         $rz .= mb_strtoupper( $d['title'] )."\n".
         $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$fdir.
         db_table_field('fulltextfile4', 'proceedings', '`ID`='.$p['proc_id']).$cr."<br>\n";
      }
      else{ // Html код
         if(!$_SERVER['SERVER_NAME']) $_SERVER['SERVER_NAME'] = stored_value('host_web');
         $rz .= '<p><a href="https://'.$_SERVER['SERVER_NAME'].
                $rvp.'&rev2='.$p['proc_id'].$page_hash.'" target="_blank">'.
                mb_strtoupper( $d['title'] )."</a>".$cr;
         if($status){
            if($p['date_time_2']>$p['date_time_1']){ 
               $rz .= '<br>'.translate('conference_lastRevSave').
                       db2user_date_time($p['date_time_2']);
               if($p['locked']) $rz .= ' <span style="color:red;">LOCKED 1</span>';
            }
            else
               $rz .= '<br><span style="color:red;">'.translate('conference_noRevYet').'</span>';
         }
         $rz .= "</p>\n";}
    }
  }
}
return $rz;
}

// Връща предназначен за четене от хора надпис на тематично направление с номе $i

function conference_htopic($i){
global $utype;
static $topic = array();
if(!count($topic)){
   // Научни направления - масив $tp
   eval(translate('conference_topics_'.$utype,false));
   $topic = $tp;
}
return $topic[$i];
}

//
// Изписване с думи на формата и тематичното направление, линкове към файловете с пълния текст
// и др. промени над данните преди показването им

function conference_trprec($d){
global $utype;
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
if($d['approved_a']=='1') $d['approved_a'] = '<span style="color:green">'.translate('conference_approvedAYes').'</span>';
else $d['approved_a'] = '<span style="color:red">'.translate('conference_approvedANo').'</span>';
$d['form'] = $fs[$d['form']];
if(isset($tp[$d['topic']])) $d['topic']=$tp[$d['topic']];
else $d['topic'] = "-?-";
$d['title'] = '<strong>'.$d['title'].'</strong>';
$d['abstracttextfile' ] = file_link_and_size($d['abstracttextfile' ]);
$d['fulltextfile' ] = file_link_and_size($d['fulltextfile' ]);
$d['fulltextfile2'] = file_link_and_size($d['fulltextfile2']);
$d['fulltextfile3'] = file_link_and_size($d['fulltextfile3']);
return $d;
}

//
// Хипервръзка и големина на файла
// $fn - име на файл от поле fulltextfileX на таблица proceedings
// $f_d -  път до директорията с файлове на конференцията. Ако е празен стринг,
// се използва пътят до файловете на текущата конференция $fdir

function file_link_and_size($fn, $f_d = ''){
global $fdir,$editing;
if(empty($f_d)) $f_d = $fdir;
if (!$fn) return '';
// Абсолютен път към файла
$af = $_SERVER['DOCUMENT_ROOT'].$f_d.$fn;
// Големина на файла
if (file_exists($af)){
  $sz = filesize($af);
  $dt = filemtime($af);
  $dt = gmdate("d.m.Y H:i:s", $dt);
}
else return "$fn - <span class=\"message\">".translate('conference_filenotexists')."</span>";
if (!is_local()) $fl = rawurlencode($fn);
else $fl = $fn;
$gh = '';
$fe = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
if($editing && in_array($fe,array('doc','docx')) && !is_local())
  $gh = 'https://docs.google.com/gview?url='.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
return "<a href=\"$gh$f_d$fl\" target=\"showFile\">$fn</a> - $sz bytes $dt";
}

//
// Форма за редактиране на данните за доклад
// $uid - номер на доклада в таблица 'proceedings'

function conference_edit($uid){
global $body_adds, $utype, $can_manage, $today, $day_a_submit, $day2, $debug_mode, $user_table;
usermenu(true);
//if(!empty($debug_mode)) print_r($GLOBALS);
// Дали се редактира от администратор
$adm = isset($can_manage['conference']) && ($can_manage['conference']==1);
// Дали сме в период за качване на пълния текст
$ut = schedules_in_event($day2[1], $day2[0]) || $adm;
if (count($_POST)) return conference_pprocess($uid);
if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['conference_returnpage'] = $_SERVER['HTTP_REFERER'];
else $_SESSION['conference_returnpage'] = stored_value('conference_editpage', '/index.php?pid=1074');
global $languages, $language;
// Начални стойности на полетата
$d = array(
 'user_id'=>$uid,
 'utype'=>$utype,
 'form'=>0,
 'topic'=>0,
 'fee'=>0,
 'currency'=>'BGN',
 'approved_a'=>0,
 'approved_f'=>0,
 'publish'=>'no',
 'oc_decision'=>'',
 'keylec'=>0,
 'language'=>$language,
 'title'=>'',
 'authors'=>'',
 'addresses'=>'',
 'keywords'=>'',
 'abstract'=>'',
 'abstracttextfile'=>'',
 'fulltextfile'=>'',
 'fulltextfile2'=>'',
 'fulltextfile3'=>'',
 'fulltextfile4'=>'',
 'vol'=>1,
 'pages'=>0,
 'place'=>0
);
// Ако е изпратен номер на запис - четене от таблица 'proceedings'.
if (isset($_GET['proc'])){
  $d = db_select_1('*', 'proceedings', "`ID`=".(1*$_GET['proc']) );
  $d['topic']++;
}
// Директория с файловете на докладите
$fdir = stored_value('conference_files_'.$d['utype']);
if ( !( ($d['user_id']==$uid) || $adm ) )
   return message(translate('conference_cnnotedit'));
// Име на участника
$ud = db_select_1('*', $user_table, "`ID`=".$d['user_id']);
if(empty($ud['ID'])) return '';
if(!empty($ud['firstname'])) $un = $ud['firstname']." ".$ud['secondname']."  ".$ud['thirdname'];
else $un = '';
if(empty(trim($un)) && !empty($ud['email'])) $un = $ud['email'];
if($adm) {
   $un = '<a href="'.stored_value('conference_admin','/index.php?pid=1358').'#pof'.$d['user_id']."\">$un</a>";
   // Дата на последно актуализиране на доклада
   if(isset($d['date_time_2'])) $un .= '<br>'.$d['date_time_2'];
}
$f = new HTMLForm('conference_peform',true,false);
$f->add_input( new FormInput('', 'user_id', 'hidden', $d['user_id']) ); 
$f->add_input( new FormInput('', 'utype',   'hidden', $d['utype']));
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
array_unshift($tp,translate('conference_choos',false));
// Ако се редактира от администратор - поле за платена такса, полета за одобряване и др.
if ($adm) {
  $ti = new FormCurrencyInput(encode('Такса:'), 'fee', 'currency', $d['fee'], $d['currency']);
  $f->add_input($ti);
  $ti = new FormInput(translate('conference_approvedA'), 'approved_a', 'checkbox');
  if ($d['approved_a']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('Одобрен пълен текст:'), 'approved_f', 'checkbox');
  if ($d['approved_f']) $ti->checked = ' checked';
  $f->add_input($ti);
  $pi = new FormSelect(encode('За публикуване:'), 'publish', array('no','yes'), $d['publish']);
  $f->add_input($pi);
  $pi = new FormTextArea(encode('Решение на ОК:'), 'oc_decision', 63, 5, stripslashes($d['oc_decision']) );
  $f->add_input($pi);
  $ti = new FormInput(encode('Пленарен доклад:'), 'keylec', 'checkbox');
  if ($d['keylec']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('Том:'), 'vol', 'text', $d['vol']);
  $f->add_input($ti);
  $ti = new FormInput(encode('Място:'), 'place', 'text', $d['place']);
  $f->add_input($ti);
}
else {
  $f->add_input(new FormInput('', 'approved_a', 'hidden', $d['approved_a']));
  $f->add_input(new FormInput('', 'approved_f', 'hidden', $d['approved_f']));
  $f->add_input(new FormInput('', 'keylec',     'hidden', $d['keylec']));
}
// Дали сме в период на редактиране на резюмета
$et = ($today < $day_a_submit) || $adm;
// Съставяне на формата за редактиране
$ti = new FormSelect(translate('conference_language'), 'language', $languages, $d['language']);
if (!$et) $ti->js = ' disabled="disabled"';
$ti->values = 'k';
$f->add_input($ti);
$fi = new FormSelect( translate('conference_cform'), 'form', $fs, $d['form'] );
if (!$et) $fi->js = ' disabled="disabled"';
$fi->values = 'k';
$f->add_input($fi);
$fi = new FormSelect( translate('conference_ctopic'), 'topic', $tp, $d['topic'] );
$fi->js = ' style="width:530px;"';
if (!$et) $fi->js .= ' disabled="disabled"';
$fi->values = 'k';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_ctitle'), 'title', 63, 5, stripslashes($d['title']) );
if (!$et) $fi->js = ' disabled="disabled"';
$fi->ckbutton = '';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_cauthors'), 'authors', 63, 3, stripslashes($d['authors']) );
if (!$et) $fi->js = ' disabled="disabled"';
//$fi->ckbutton = '';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_caddresses'), 'addresses', 63, 5, stripslashes($d['addresses']) );
if (!$et) $fi->js = ' disabled="disabled"';
//$fi->ckbutton = '';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_ckeywords'), 'keywords', 63, 3, stripslashes($d['keywords']) );
if (!$et) $fi->js = ' disabled="disabled"';
$fi->ckbutton = '';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_cabstract'), 'abstract', 61, 10, stripslashes($d['abstract']) );
if (!$et) $fi->js = ' disabled="disabled"';
else $body_adds .= ' onload="CKEDITOR.replace(\'abstract\');"';
$fi->ckbutton = '';
$f->add_input($fi);
// Дали сме в период на качване на пълен текст
$ft = schedules_in_event($day2[1], $day2[0]) || $adm;
// Дали докладът има забележки от рецензенти, изискващи коригиране
//$mc = db_table_field('decision', "reviewer_work", "`proc_id`=".$d['ID']." AND `decision`='1'",'',false) == "1";
$mc = false; // временно задаване за да се предотврати качване
$ti = new FormInput(translate('conference_pages'), 'pages', 'text', $d['pages']);
$ti->js = ' onfocus="this.select();"';
if (!$adm) $ti->js .= ' disabled="disabled"';
$f->add_input($ti);
$fl = new FormInput(translate('conference_cabstracttextfile'), 'abstracttextfile', 'file', 
                    !empty($d['abstracttextfile']) ? $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['abstracttextfile']) : '');
if (!$ft && !$mc) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile'), 'fulltextfile', 'file', 
                    !empty($d['fulltextfile']) ? $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile']) : '');
if (!$ft && !$mc) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile2'), 'fulltextfile2', 'file', 
                    !empty($d['fulltextfile2']) ? $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile2']) : '');
if (!$ft && !$mc) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile3'), 'fulltextfile3', 'file', 
                    !empty($d['fulltextfile3']) ? $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile3']) : '');
$fl->size = 63;
$f->add_input($fl);
if($adm){
  $fl = new FormInput(translate('conference_cfulltextfile4'), 'fulltextfile4', 'file', 
                      !empty($d['fulltextfile4']) ? $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile4']) : '');
  $fl->size = 63;
  $f->add_input($fl);
}
$fi = new FormInput('', '', 'button', translate('conference_csubmit') );
$fi->js = ' onclick="checkArticleForm();"';
$f->add_input( $fi );
// Съобщение, че някои полета са неактивни
$ms = '';
if (!$et) $ms = message(translate('conference_noabs'));
if (!$ft) $ms = message(translate('conference_nofull'));
return $ms."\n<p>$un</p>\n".
// Javascript, проверяващ формата
'<script>
function checkArticleForm(){
var f = document.forms.conference_peform;
var i = f.topic.selectedIndex;
if(!i) alert("'.translate('conference_noTopic',false).'");
else f.submit();
}
</script>
'.
$f->html().'
<p>&nbsp;</p>';
}

//
// Обработване на редактирни данни за доклад

function conference_pprocess($uid){
global $language, $can_manage, $utype, $proccount;
// Данни за доклад
$d = array(
'user_id'=>(1*$_POST['user_id']),
'utype'=>addslashes($_POST['utype']),
'date_time_2'=>'NOW()',
'publish'=>isset($_POST['publish'])?addslashes($_POST['publish']):'no'
);
if (!isset($_POST['approved_a'])) $d['approved_a']=0; else if($_POST['approved_a']=='on') $d['approved_a']=1;
if (!isset($_POST['approved_f'])) $d['approved_f']=0; else if($_POST['approved_f']=='on') $d['approved_f']=1;
if (!isset($_POST['keylec']))     $d['keylec']=0;     else if($_POST['keylec']=='on')     $d['keylec']=1;
if (isset($_POST['language'])) $d['language']=addslashes($_POST['language']);
if (isset($_POST['form'])) $d['form']=addslashes($_POST['form']);
if (isset($_POST['topic'])) $d['topic']=addslashes($_POST['topic']-1);
if (isset($_POST['title'])) $d['title']=addslashes($_POST['title']);
if (isset($_POST['authors'])) $d['authors']=addslashes($_POST['authors']);
if (isset($_POST['addresses'])) $d['addresses']=addslashes($_POST['addresses']);
if (isset($_POST['keywords'])) $d['keywords']=addslashes($_POST['keywords']);
if (isset($_POST['abstract'])) $d['abstract']=addslashes($_POST['abstract']);
if (isset($_POST['vol'])) $d['vol']=addslashes($_POST['vol']);
if (isset($_POST['pages'])) if(is_numeric($_POST['pages'])) $d['pages']=1*$_POST['pages'];
                            else $d['pages']=0;
if (isset($_POST['place'])) if(is_numeric($_POST['place'])) $d['place']=1*$_POST['place'];
                            else $d['place']=0;
// Данни за платена такса
$d2 = array(); 
if (isset($_POST['fee'])) $d2['fee']=addslashes($_POST['fee']);
if (isset($_POST['currency'])) $d2['currency']=addslashes($_POST['currency']);
// Качване на второ резюме, ако е изпратено
$r = conference_upload('abstracttextfile',$uid,$d['user_id']);
$ms = $r[0];
if ($r[1]) $d['abstracttextfile' ] = $r[1];
// Качване на doc файл, ако е изпратен
$r = conference_upload('fulltextfile',$uid,$d['user_id']);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile' ] = $r[1];
// Качване на pdf файл, ако е изпратен
$r = conference_upload('fulltextfile2',$uid,$d['user_id']);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile2'] = $r[1];
// Качване на файл с презентация, ако е изпратен
$r = conference_upload('fulltextfile3',$uid,$d['user_id']);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile3'] = $r[1];
// Качване на файл с анонимен пълен текст, ако е изпратен
//if(isset($_POST['fulltextfile4']))
{ // Проверката се налага, защото този файл се
  // показва във формата и се качва само от администратор.
  $r = conference_upload('fulltextfile4',$uid,$d['user_id']);
  $ms .= $r[0];
  if ($r[1]) $d['fulltextfile4'] = $r[1];
}
// Ако е изпратен номер на доклад с $_GET['proc']
// се актуализират данните на доклада с този номер
if (isset($_GET['proc'])){
  $d['ID'] = 1*$_GET['proc'];
  db_update_record($d, 'proceedings', false);
}
// Иначе се вмъкват данни за нов доклад
else{
  $d['date_time_1'] = 'NOW()';
  $i = db_table_field('COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `user_id`=".$d['user_id']);
  if ($i<$proccount) db_insert_1($d, 'proceedings', false);
  else $ms = translate('conference_toomany')."<br>\n";
}
// Ако са изпратени данни за платена такса
// таксата се записва на всички доклади на участника
if ((count($d2)==2) && $d2['fee']) db_update_where($d2, 'proceedings', "`utype`='$utype' AND `user_id`=".$d['user_id']);
// При редактиране от администратор се зарежда страницата от която е отворена формата
if (isset($can_manage['conference']) && $can_manage['conference']){
   $ap = stored_value('conference_admin','/index.php?pid=1358');
   $rp = $_SESSION['conference_returnpage'];
   if(strpos($rp,$ap)===false) $rp .= '#pof'.$d['ID'];
   else $rp .= '#pof'.$d['user_id'];
   if(!$ms) header('Location: '.$rp);
   die($ms);
}
return message($ms.translate('dataSaved'));
}

//
// Качване на файл от $_POST[$fn]
// Връща двумерен масив с първи елемент - съобщение
// и втори елемент - име на качения файл

function conference_upload($fn,$uid,$pid){
global $fdir, $utype;
if(!isset($_FILES[$fn])) return array('','');
$fl = $_FILES[$fn];//die(print_r($_FILES,true));
$ms = ''; $n = '';
if (!$fl['error']){
  // Проверка дали друг потребител не е качил файл със същото име
  $n = db_table_field($fn, 'proceedings',
       "`$fn`='".$fl['name']."' AND `user_id`<>$pid AND `utype`='$utype'", '', false);
//  die($n);
  if ($n) $ms = translate('conference_fileagain')."<br>\n";
  else {
    // Име на качен вече от потребителя стар файл
    $of = db_table_field($fn, 'proceedings', "`user_id`=$pid AND `utype`='$utype'");
//    die( "$of<br>".$fl['name']."<br>".$fl['tmp_name']);
    // Ако има стар файл, той се изтрива
    if ( $of ) //&& ($of!=$fl['name']) )
    {
       $ofn = $_SERVER['DOCUMENT_ROOT'].$fdir.$of;//die($ofn);
       if (file_exists($ofn)) unlink($ofn);
    } 
    $nfn = $_SERVER['DOCUMENT_ROOT'].$fdir.$fl['name'];
    if (!move_uploaded_file($fl['tmp_name'], $nfn)) $ms = translate('conference_noupload')."<br>\n";
    $n = $fl['name'];
  }
}
else if ($fl['name']) die(print_r($_FILES, true));
return array($ms,$n);
}

//
// Администриране на регистрираните потребители

function conference_admin($uid){
global $can_manage, $utype, $page_header, $can_visit, $user_table, $proccount, $pedit, 
       $page_hash;
usermenu(true);
$is_editor = isset($can_manage['conference']) && ($can_manage['conference']==1);
if ( empty($can_manage['conference']) )
   return message("You have no permission to view this information.");
// Параметър за сортиране
$asc = array('+'=>'ASC','-'=>'DESC');
$sp = '-'; $q = '';
$sby = '';
if ( isset($_GET['sby']) && $_GET['sby'] ){
  $p = $_GET['sby'][0];
  $q = substr($_GET['sby'], 1);
  $sby = $q;
  $q = ' ORDER BY `'.addslashes($q).'` '.$asc[$p];
  if ($p=='+') $sp = '-'; else $sp = '+';
}
// Javascript, който се изпълнява при щракване на бутона за сортиране
$page_header .= '<script>
function doSort(){
var s = document.getElementById("sby");
var o = s.options[s.selectedIndex];
var l = "'.set_self_query_var('sby',$sp.'',false).'" + o.value;
document.location = l;
}
function copyEMail(e){
var c = document.getElementById("textForClpibd");
var t0 = "" + c.value;
if(t0.length) t0 = t0 + ", ";
var t1 = e.innerText;
c.value = t0 + t1;
c.select();
document.execCommand("copy");
}
</script>
';
// Четене данни за потребители
$ud = db_select_m('*', $user_table, "`type`='$utype' AND `username`>''$q",false);
// Четене данните за доклади
$pd = db_select_m('*', 'proceedings', "`utype`='$utype' ORDER BY `user_id`");
// Масив с трите имена на участниците с индекси id-тата им
$u = array();
// Масив с броя на заявените доклади на всеки потребител с индекси id-тата им
$up = array();
foreach($pd as $d) if (isset($up[$d['user_id']])) $up[$d['user_id']]++; else $up[$d['user_id']]=1;

$rz = '<p><a href="'.set_self_query_var('rev2','on').'" target="allRevs">'.encode('Рецензенти')."</a></p>\n";

$rz .= encode('<p>Регистрирани потребители - ').count($ud).encode(', заявки - ').count($pd).
', &nbsp; '.translate('user_sortby').': 
<select id="sby">'."\n";
if ($sby=='date_time_2') $rz .= '<option value="date_time_2">'.translate('user_lastlogin').'</option>
<option value="date_time_1">'.translate('user_lastupdate').'</option>'."\n";
else $rz .= '<option value="date_time_1">'.translate('user_lastupdate').'</option>
<option value="date_time_2">'.translate('user_lastlogin').'</option>'."\n";
$rz .= '</select>
<input type="button" value="'.translate('user_sort').'" onclick="doSort();"> '.$sp.'</p>
<table id="utab">
<tr>
<th><a href="'.set_self_query_var('sby',$sp.'ID').         '">ID</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'email').      '">'.translate('user_email').'</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'firstname').  '">'.translate('conferente_pnames').'</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'country').    '">'.translate('user_country').'</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'institution').'">'.translate('user_institution').'</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'address').    '">'.translate('user_address').'</a></th>
<th><a href="'.set_self_query_var('sby',$sp.'telephone').  '">'.translate('user_telephone').'</a></th>
<th>'.encode('Брой<br>доклади').'</th>
</tr>
';
// Адрес на скрипта за създаване на нов доклад
$lk = current_pth(__FILE__).'new_proceeding.php';
// Адрес на страницата за редактиране данните на участник
$ep = stored_value('userreg_edit_'.$utype, $pedit);
// Таблица с данни на потребителите
foreach($ud as $d){
  $u[$d['ID']]=$d['position']."<br>\n".$d['firstname'].' '.$d['secondname'].' '.$d['thirdname'];
  $rz .= '<tr>
<td id="us'.$d['ID'].'">
<a href="'.$ep.'&amp;uid='.$d['ID'].'" target="_blank">'.$d['ID'].'</a>';
  if(isset($can_manage['userreg']) && ($can_manage['userreg']==1))
     $rz .= "\n<a href=\"".current_pth(mod_path('userreg')).'login_as.php?uid='.$d['ID'].'">></a>';
  $rz .= "\n".' <a href="'.current_pth(__FILE__).'make_reviewer.php?uid='.$d['ID'].'" title="Make reviewer" target="makeReviewer">R</a>';
  $rz .= '
</td>
<td><a style="cursor:pointer;" title="Click to copy e-mail address." onclick="copyEMail(this);">'.$d['email'];
  if(!empty($d['aemails'])) $rz .= ', '.$d['aemails'];
  $rz .= '</a>';
  if (substr($sby, 0, -1)=='date_time_') $rz .= "<br>".$d[$sby];
  $rz .= '</td>
<td>'.$u[$d['ID']].'</a></td>
<td>'.$d['country'].'</td>
<td>'.$d['institution'].'</td>
<td>'.$d['address'].'</td>
<td>'.$d['telephone']."</td>\n";
  $rz .= '<td>';
  if (isset($up[$d['ID']])) $rz .= '<a href="#pof'.$d['ID'].'">'.$up[$d['ID']]."</a>";
  if ( $is_editor ) $rz .= " <a href=\"$lk?uid=".$d['ID'].'">+</a>';
  $rz .= "</td>\n</tr>\n";
}
$n = array(
'ID'=>'ID',
'email'=>translate('user_email'),
'names'=>translate('conferente_pnames'),
'country'=>translate('user_country'),
'institution'=>translate('user_institution'),
'address'=>translate('user_address'),
'telephone'=>translate('user_telephone')
);
$rz .= '</table>
<button>All</button><br>
<textarea id="textForClpibd" style="width:100%"></textarea>';
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
$rz .= encode('<h2>Заявени доклади - ').count($pd).'</h2>
';
foreach($pd as $d){
  $rz .= '<a name="pof'.$d['user_id'].'"></a>'."\n";
  $rz .= '<p><a href="'.'#us'.$d['user_id'].encode('">Данни на участника</a>')."</p>\n";
  if (isset($u[$d['user_id']])&& trim($u[$d['user_id']])) $d['user_id']=$u[$d['user_id']];
  else {
    $e = db_table_field('email', $user_table, '`ID`='.$d['user_id']);
    $d['user_id']='<a href="meilto:'.$e.'">'.$e.encode('</a> - няма имена в профила');
  }
  $d['fee'] = $d['fee'].' '.$d['currency'];
  $d['form'] = $fs[$d['form']];
  if(isset($tp[$d['topic']])) $d['topic']=$tp[$d['topic']];
  else $d['topic'] = "-?-";
  $d['title']='<strong>'.$d['title'].'</strong>';
  $d['abstracttextfile' ]=file_link_and_size($d['abstracttextfile' ]);
  $d['fulltextfile' ]=file_link_and_size($d['fulltextfile' ]);
  $d['fulltextfile2']=file_link_and_size($d['fulltextfile2']);
  $d['fulltextfile3']=file_link_and_size($d['fulltextfile3']);
  $d['fulltextfile4']=file_link_and_size($d['fulltextfile4']);
  // Адрес на страницата за редактиране на резюме и качване на доклад от участник
  $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
  // Отбелязване със зелена линия отгоре, ако е ключов доклад
  $st = '';
  if($d['keylec']) $st = 'border-right:solid 4px green;';
  $rz .= encode('<p>Доклад <strong>No:'.$d['ID'].'</strong> <a href="'.$edp.'&amp;proc='.$d['ID'].$page_hash.'">Редактиране</a></p>
  ').view_record($d, array(
  'user_id'=>translate('conference_by'),
  'fee'=>translate('conference_fee'),
  'date_time_1'=>translate('conference_cdate1'),
  'date_time_2'=>translate('conference_cdate2'),
  'form'=>translate('conference_cform'),
  'topic'=>translate('conference_ctopic'),
  'title'=>translate('conference_ctitle'),
  'authors'=>translate('conference_cauthors'),
  'addresses'=>translate('conference_caddresses'),
  'keywords'=>translate('conference_ckeywords'),
  'keywords'=>translate('conference_ckeywords'),
  'abstract'=>translate('conference_cabstract'),
  'abstracttextfile'=>translate('conference_cabstracttextfile'),
  'fulltextfile'=>translate('conference_cfulltextfile'),
  'fulltextfile2'=>translate('conference_cfulltextfile2'),
  'fulltextfile3'=>translate('conference_cfulltextfile3'),
  'fulltextfile4'=>translate('conference_cfulltextfile4')
  ), $st);
}
return $rz.'<p>&nbsp;</p>
';
}

//
// Показване на статистика за конференцията

function conference_stats(){
global $countries, $utype, $user_table;
// Номера на потребителите с доклади
$us = db_select_m('user_id', 'proceedings', "`utype`='$utype' GROUP BY `user_id`");
$q = '';
foreach($us as $u){
  if($q) $q .= ' OR ';
  $q .= "`ID`=".$u['user_id'];
}
// Брой регистрирани
$cp  = db_table_field('COUNT(`ID`)', $user_table, "`type`='$utype' AND `username`>''" );
$cp2 = db_table_field('COUNT(`ID`)', $user_table, "($q) AND `type`='$utype' AND `username`>''" );
// Участници по държави
$ca  = db_select_m('country,COUNT(`ID`)', $user_table, "`type`='$utype' AND `username`>'' GROUP BY `country`" );
$ca2 = db_select_m('country,COUNT(`ID`)', $user_table, "($q) AND `type`='$utype' AND `username`>'' GROUP BY `country`" );
// Брой доклади
$cr = db_table_field('COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>''" );
// По направления
$cc = db_select_m('topic,COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>'' GROUP BY `topic`" );
// По форма
$cf = db_select_m('form,COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `fulltextfile4`>' ' GROUP BY `form`", false);
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
$rz = '<p>'.translate('conference_partcount').': '.$cp.'</p>
<p>'.translate('conference_bycountry').':</p>
<p>';
foreach($ca as $c){
 $rz .= $c['COUNT(`ID`)'].' - ';
 if (!$c['country']) $rz .= translate('conference_notspec');
 else $rz .= $countries[$c['country']];
 $rz .= '<br>';
}
$rz .= '</p>
<p>'.encode('С доклади:').'</p>
<p>';
foreach($ca2 as $c){
 $rz .= $c['COUNT(`ID`)'].' - ';
 if (!$c['country']) $rz .= translate('conference_notspec');
 else $rz .= $countries[$c['country']];
 $rz .= '<br>';
}
$rz .= "</p>\n".translate('conference_proccount').': '.$cr.'</p>
<p>'.translate('conference_bycat').':</p>
<p>';
foreach($cc as $c) $rz .= $c['COUNT(`ID`)'].' - '.$tp[$c['topic']].'<br>';
$rz .= '</p>
<p>'.translate('conference_byform').':</p>
<p>';
foreach($cf as $c) $rz .= $c['COUNT(`ID`)'].' - '.$fs[$c['form']].'<br>';
$rz .= '</p>';
return $rz;
}

function conference_addanonymfiles(){
global $utype,$fdir;
// Четене имената на PDF файловете с пълни текстове
$fn = db_select_m('ID,fulltextfile2', 'proceedings', "`utype`='$utype' AND `fulltextfile2`>' '");
foreach($fn as $d){
  $f1 = str_replace('article', 'anonym', $d['fulltextfile2']);
  $d['fulltextfile4'] = $f1;
  unset($d['fulltextfile2']);
  $f = $_SERVER['DOCUMENT_ROOT'].$fdir.$f1;
  if(file_exists($f)) echo db_update_record($d, 'proceedings', false);
  
}
}

//
// Показване заглавията на приетите резюмета

function conference_abstract_titles($a = ''){
global $utype, $adm_pth, $can_manage, $can_edit, 
       $today, $day_a_approve, $day_program, $page_header, $page_hash, 
       $fdir, $user_table;
// Установяване правата на влезлия потребител
usermenu(true);
// Дали потребителят е от екипа на конференцията
$team = !empty($can_manage['conference']) || $can_edit;
// Ако са качени файлове с анонимизирани пълни текстове с имена anonymXXX.pdf и е изпратен 
// параметър $_GET['addanonym']=='on' се изпълнява 
// процедура, която ги открива и добавя имената им в съответните полета
if($team && isset($_GET['addanonym']) && ($_GET['addanonym']=='on')) conference_addanonymfiles();
// Дали освен това е изпратен параметър за фиксиране на реда на докладите $_GET['fixorder']=1
$fixo = $team && isset($_GET['fixorder']) && ($_GET['fixorder']=='1');
// Разрешение за преглед чрез таен параметър
$secret = stored_value('conference_secret_'.$utype,'basa-team');
$allowtoshow = !empty($_GET['allowtoshow']) &&  ($_GET['allowtoshow']==$secret);
// Показване на заглавията след дата на одобряване на резюметата
$afretapr = stored_value('anference_titles_after_a_approve');
$afretapr = 1;
$current = ($utype == $a);
if($a){
  $utype = $a;
  $fdir = stored_value('conference_files_'.$utype, $fdir);
}
$crp = current_pth(__FILE__);
$pdfi = '<img src="'.$crp.'Download-PDF.png">';
$ppti = '<img src="'.$crp.'Download-PPT.png">';
$mp4i = '<img src="'.$crp.'Open-MP4.png">';
$page_header .= '<script>
function deleteAbstract(id){
if( confirm("'.encode('Потвърждавате ли изтриване на запис за доклад с ID=').'"+id+"?") )
   document.location = "'.$adm_pth.'delete_record.php?t=proceedings&r="+id;
}
</script>';
// Томове
$vl = db_select_m('vol', 'proceedings', "`utype`='$utype' GROUP BY `vol` ORDER BY `vol` ASC");
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
$rz = '';
$tc = 0; // Общ брой доклади
$docs = 0; // Брой doc файлове с пълен текст на доклад
$pdfs = 0; // Брой pdf файлове с пълен текст на доклад
$anos = 0; // Брой пълни текстове без имена за анонимно рецензиране
$rc = 0; // Брой готови доклади 
$pc = 0; // Доклади с `pulish`='yes';
if ($current && ("$today"<"$day_program") && !($team || $allowtoshow))
    return message(translate("conference_shoeafter").db2user_date_time($day_program));
// Начин на подреждане
$on = 'conference_'.$utype.'_order';
$oid = db_table_field('ID', 'options', "`name`='$on'",'',false);
$order = ' ORDER BY '.stored_value($on, '`keylec` DESC, `authors` ASC');
$olink = ' <a href="'.set_self_query_var('order','date').$page_hash.'">By title</a>';
if(isset($_GET['order']) && ($_GET['order']=='date') ){
  $order = " ORDER BY `date_time_2` DESC";
  $olink = ' <a href="'.unset_self_query_var('order').$page_hash.'">By date</a>';
}
// Стойност на поле `place`, задаващо реда на докладите.
// Използва се, когато има параметър $_GET['fixorder']=1
$place = 10;
// Имена на автори, които се колекционират с цел статистика
$auth = array();
// Флаг за показване на авторите и номерата на докладите
$s_auth = true;
// Номера на страници в том на поредния доклад.
// Номер на страницата на първия доклад е настройка за всеки том.
$pn = array(
0=>stored_value("conference_$utype"."_pageStart0",1),
1=>stored_value("conference_$utype"."_pageStart1",1),
2=>stored_value("conference_$utype"."_pageStart2",1),
3=>stored_value("conference_$utype"."_pageStart3",1)
);
// Брой страници, които се оставят за заглавие на секция
$spages = stored_value("conference_$utype"."_pageSection",'0');
// За всеки том
foreach($vl as $vl1) {
if($vl1['vol']=='p'){
  $pc = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `vol`='p' AND `approved_a`=1", 0);
  if(!$pc && !$team) continue;
  $rz .= '<h2>'.translate('conference_posters')."</h2>\n";
}
else if(count($vl)>1) $rz .= '<h2>'.translate('conference_volume').' '.$vl1['vol']."</h2>\n";
// За всяко научно направление
for($i = 0; $i<count($tp); $i++){
   $zip_command = 'zip arh_'.($i+1).'.zip ';
   $c = 0; $c2 = 0; $doc = 0; $pdf = 0; $ano = 0; // Брой доклади в научнато направление
   $cn = array(0=>0, 1=>0, 2=>0, 3=>0, 'p'=>0); // Брой доклади в том. Изглежда в момента не се показва никъде.
   $sr = '';  // html код за докладите от научното направление
   $filter = " AND ( ((`utype`<'vsu2020') AND `approved_a`) OR `publish`='yes')";
   // Ако и минал денят за потвърждаване на резюметата при преглеждане чрез таен код неодобрените се прескачат
//   if(($allowtoshow || $afretapr) && ($today > $day_a_approve)) $filter = " AND `approved_a`";
   if ( $team || $allowtoshow ){ $filter = ''; }
   // За всеки от езиците
   {
     // Доклади от направлението
     $da = db_select_m('*', 'proceedings',
          "`utype`='$utype'".
          " AND `topic`='$i'".
          " AND `vol`='".$vl1['vol']."'".
          $filter.$order, false );
     if($vl1['vol']!='p'){
         if(count($da)) $pn[$vl1['vol']] += $spages;
         if(!($pn[$vl1['vol']] % 2)) $pn[$vl1['vol']]++;
     }
     $lr = ''; // html код за докладите на език $l от научното направление
     $c += count($da);
     // Текуща форма
     $fr = -1;
     // За всеки доклад
     foreach($da as $d){
        if($d['publish']=='yes') $pc++;
        if(!isset($d['fulltextfile']))  $d['fulltextfile'] = '';
        if(!isset($d['fulltextfile2'])) $d['fulltextfile2'] = '';
        if(!isset($d['fulltextfile3'])) $d['fulltextfile3'] = '';
        if(($utype>='vsu2020') && ($d['form']!=$fr)){
           if(strpos($order,'`form`')!==false) $lr .= '<h4>'.$fs[$d['form']]."</h4>\n";
           $fr = $d['form'];
        }
        $lk = ''; // Забележки, които се показват в режим редактиране
        $cr = 7; // Число, което се намалява с единица при всяка забележка
        $nm = ''; // Номера пред заглавието
        $st = ' id="pof'.$d['ID'].'"'; // id атрибут на доклада
        $stl = ''; // Стил за предизвикване на оцветяване
        $ex3 = strtolower( pathinfo( $d['fulltextfile3'], PATHINFO_EXTENSION ) );
        if ( $team ){
        //   Преименуване на файловете. Само ако е необходимо се откоментирва този ред
           if(isset($_GET['rename']) && ($_GET['rename']=='on')) $d = conference_rename_files($d);
           if ($d['form']==4){ // Форма на участие "Слушател"
              // Име на участника
              $ud = db_select_1('*', $user_table, "`ID`=".$d['user_id']);
              $un = $ud['firstname']." ".$ud['secondname']."  ".$ud['thirdname'];
              $lk = "$un - ".encode('Слушател');
           }
           else{
              if(empty($d['title']))         { $lk .= translate('conference_noTitle'); $cr--; }
              if(empty($d['authors']))       { $lk .= translate('conference_noAuthors'); $cr--; }
              if(empty($d['abstract']))      { $lk .= translate('conference_noAbstract'); $cr--; }
              if(!in_array(strtolower(pathinfo($d['fulltextfile'], PATHINFO_EXTENSION)), array('doc', 'docx')) )
                                             { $lk .= translate('conference_noDoc'); $cr--; }
              if(strtolower(pathinfo($d['fulltextfile2'], PATHINFO_EXTENSION)) != 'pdf')
                                             { $lk .= translate('conference_noPDF'); $cr--; }
              if(empty($d['fee']))           { $lk .= translate('conference_noFee'); $cr--; }
              if(empty($d['approved_a']))    { $lk .= translate('conference_noAprAbstract'); $cr--; }
              if($d['approved_a'] && empty($d['approved_f']))
                                             { $lk .= encode('одобрение пълен текст, '); $cr--; }
              if(empty($d['keywords']))      { $lk .= translate('conference_noKeyWords'); $cr--; }
              if(!empty($d['fulltextfile2']) && empty($d['fulltextfile4']))
                                             { $lk .= translate('conference_noAnon'); $cr--; }
              if($lk) $lk = '<br><span class="message">'.translate('conference_Missing')."$lk</span>\n";
              else {
                $stl .= ' style="background-color: lightgreen; padding: 0.5em;';
                $rc++;
              }
           }
           $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
           $lk .= '<br><a href="'.$edp.'&proc='.$d['ID'].$page_hash.'" target="editproc">'.encode('Редактиране')."</a> \n";
           $lk .= encode(' Съобщаване за: ').emailsend('select|&uid='.$d['user_id'].'&proc='.$d['ID'])."\n";
           if($cr<=0)
              $lk .= $cr.' <a href="#" style="font-weight:bold;color:red;" onclick="deleteAbstract('.$d['ID'].');return false;">x</a>';
           if($d['fulltextfile4']) $lk .= conference_rev2data($d);
           if($d['abstracttextfile'])  $lk .= '<br>Abstract 2: '.file_link_and_size($d['abstracttextfile'], $fdir);
           if($d['fulltextfile'])  { $lk .= '<br>Doc: '.file_link_and_size($d['fulltextfile'], $fdir);
              $zip_command .= $d['fulltextfile'].' '; }
           if($d['fulltextfile2']) { $lk .= '<br>Pdf: '.file_link_and_size($d['fulltextfile2'], $fdir); }
           if($d['fulltextfile3']) { $lk .= '<br>Presentation: '.file_link_and_size($d['fulltextfile3'], $fdir); }
           $nm = $d['language'].
                 ', ID:<a href="'.$adm_pth.'/edit_record.php?t=proceedings&r='.$d['ID'].'" target="_blank">'.$d['ID'].
                 "</a>, place:".$d['place'].", form:".$d['form'].", ";
        } // if ( $team ) 
        if($d['fulltextfile2']>' ') $pdf++;
        if ( $team || $allowtoshow ){
          if($d['approved_a']) conference_add_auth($auth, $d['authors']);
          if($d['fulltextfile']) $doc++; 
          if($d['fulltextfile4']) $ano++;
//          if($doc!=$ano) die("$doc!=$ano<br>".print_r($d,true));
        }
        
        if( $team || !empty($d['title']) || $allowtoshow ) {
           if($d['keylec']){
             if(!$stl) $stl .= ' style="';
             $stl .= 'border-right:solid 4px green;';
           }
           if($stl) $st .= $stl.'"';
           $lr .= "<div$st>$nm";
           if(!empty($d['approved_a']) || $allowtoshow){
             if( $team ) $lr .= "vol:".$d['vol'].", ".
                                db_table_field('email', 'users', "`ID`=".$d['user_id'])." ";
             if($d['fulltextfile2']){
               $cn[$d['vol']]++;
//               $lr .= ($i+1)."-".$cn[$d['vol']].". ";
             }
           }
//       if( ($utype!='vsu2020') ||
//           ( isset($_GET['allowtoshow']) && ($_GET['allowtoshow']=='fulltext') )
//       )
       {
           // PDF с пълния текст
           if($d['fulltextfile2'] && ($team || !$current || $s_auth) ){
               $lr .= '<a href="/_pdfjs-2.2.228-dist/web/viewer.html?file='.
                       $fdir.$d['fulltextfile2'].'" title="'.translate('conference_dfull', false).'">'.$pdfi.'</a> ';
               // Фиксиране на реда чрез записване на стойност в поле 'place'
               if($fixo){
                  db_update_record(array('ID'=>$d['ID'], 'place'=>$place), 'proceedings', false);
                  $place += 10; 
               }
                
           }
           // PDF с презентация
           if($d['fulltextfile3'] && ($team || !$current || $s_auth))
              switch ($ex3){
              case 'pdf':
                 $lr .= '<a href="/_pdfjs-2.2.228-dist/web/viewer.html?file='.
                        $fdir.$d['fulltextfile3'].'" title="'.translate('conference_prez', false).'">'.$ppti.'</a> ';
                 break;
              case 'mp4':
                 $lr .= '<a href="'."$fdir/".$d['fulltextfile3'].'" title="'.translate('conference_prez', false).'">'.$mp4i.'</a> ';
                 break;
              default:
                 $lr .= '<a href="'."$fdir/".$d['fulltextfile3'].'" title="'.translate('conference_prez', false).'">'.$ppti.'</a> ';
              }
       }
           if($s_auth) {
//             if( ($d['fulltextfile2']>'') && $d['approved_f'] )
//             if( ($d['fulltextfile2']>''))
             if(($d['publish']=='yes'))
             {
                $c2++;
                $lr .= ($i+1);
                $lr.= "-".$c2.". ";
             }
//             else $lr .= ". ";
           }
           
           if($d['title']) $lr .= "<ptitle>".mb_strtoupper(stripslashes($d['title']))."</ptitle>\n";
           else $c--;
           
           // При добавен в адреса на страницата таен параметър се показва и резюмето и ключовите думи.
           /* if($allowtoshow){
              $lr .= '<pabstract>'.$d['abstract']."</pabstract>\n".
                     '<p class="keywords"><span>'.
                     translate_to('conference_ckeywords',$d['language']).
                     '</span> '.
                     conference_formatKeyWorts($d['keywords'])."</p>\n";
              if($d['fulltextfile4']) $lr .= '<p>Anonimouse full text: '.file_link_and_size($d['fulltextfile4'], $fdir)."</p>\n";
           } */
           
           if ($team || !$current || $s_auth) 
               $lr .= '<author>'.conference_only_names($d['authors'])."</author>";
           if($d['pages']){
             $lr .= " &nbsp; &nbsp; ".translate('conference_pg').$pn[$d['vol']];
             $pn[$d['vol']] += $d['pages'];
             if($d['pages']>1) $lr .= "-".($pn[$d['vol']]-1);
             if(!($pn[$d['vol']] % 2)) $pn[$d['vol']]++;
           }
           if ( $team && $d['fulltextfile2'] && ($d['publish']=='yes')) $lr .= ' ('.$d['pages'].')';
           if( ($team || (isset($_GET['text']) && ($_GET['text']=='anonimous')) )
               && $d['fulltextfile4']
              ){ 
                 $lk .= '<br>Anonimouse: ';
                 if(conference_file_age($d['fulltextfile2']) > 
                    conference_file_age($d['fulltextfile4'])
                    ) $lk .= 'OLD! ';
                 $lk .= file_link_and_size($d['fulltextfile4'], $fdir);
               }
           if( isset($_GET['allowtoshow']) && ($_GET['allowtoshow']=='rev2data') ) 
               $lk .= conference_rev2data($d);
           if(isset($_GET['allowtoshow']) && ($_GET['allowtoshow']=='docfiles')) 
              $lr .= '<br>'.file_link_and_size($d['fulltextfile'], $fdir);
           $lr .= "$lk</div>\n";
        } // Край на if( $team || !empty($d['title']) || $allowtoshow )
     } // Край на цикъла по доклади
     $sr .= $lr;
   } // Край на цикъла по езици. В момента няма значение този цикъл.
   if( $team || $allowtoshow || !empty($c) ){
     $rz .= '<h3>'.$tp[$i];
     $rz .= " - $pdf ".translate('conference_reports');
     if ( $team || $allowtoshow) $rz .= "$c abstracts, $doc doc, $ano anonymous files";
     $rz .= "</h3>\n";
     if (!$current || ("$today">="$day_a_approve") || $team || $allowtoshow) $rz .= $sr;
     $tc += $c;
     $docs += $doc;
     $pdfs += $pdf;
     $anos += $ano;
   }
} // Край на цикъла по научни направления

} // Край на цикъла по томове
if($fixo) die(encode('Беше извършено фиксиране на текущия ред на докладите. '.
                       'За да се показват в този ред променете настройката за реда на докладите. '.
                       '<a href="'.unset_self_query_var('fixorder').'">Връщане</a>'));
// Добавяне на обща статистика
$rz = '<p>'.translate('conference_fullcount')."$pc</p>\n".$rz; 
if ( $team || $allowtoshow ){
     if($oid) $ol = $adm_pth.'edit_record.php?r='.$oid.'&t=options';
     else $ol = $adm_pth.'new_record.php?t=options&name='.$on.'&value=`keylec` DESC, `authors` ASC';
     $rz = "<p>".count($auth).
           " authors, $tc abstracts, $docs doc, $pdfs pdf, $anos anonymous, $rc ready $pc to publish.<br>".
           "Order: $olink".
           (in_edit_mode() ? " <a href=\"$ol\">*</a>".
                             " <a href=\"".set_self_query_var('fixorder','1')."\">Fix</a>": '').
           " Secret link: <a href=\"".
           set_self_query_var('allowtoshow', $secret).$page_hash."\">$secret</a></p>\n".$rz;
}
return '<div id="conference_abstracts">'."\n".$rz."</div>\n";

} // Край на function conference_abstract_titles()

// Премахва цифрите от полето с имената на авторите

function conference_only_names($a){
return preg_replace('/\d/', '', strip_tags($a));
}

// Информация за рецензентите на доклад с данни $d

function conference_rev2data($d){
global $utype, $year;
$lk = '';
$lk .= "<br>".encode('Рецензенти: ')."<a href=\"/index.php?pid=79&proc=".$d['ID'].'" target="_blank">'.
       db_table_field('COUNT(*)', 'reviewer_work', "`proc_id`=".$d['ID']).
       "</a>, <a href=\"".current_pth(__FILE__)."assign_reviewer.php?proc=".$d['ID'].'" target="setRev">'.
       encode('назначаване')."</a>";
$rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision` IS NOT NULL');
$lk .= ", ".encode('попълнили').": $rwc";
$rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].
                      ' AND (`locked`=1)');
if($rwc) $lk .= ", ".encode('заключени').": $rwc";
$rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision`=0');
if($rwc) $lk .= ", ".encode('отхвърлили').": $rwc";
$rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision`=1');
if($rwc){
   $fe = conference_file_age($d['fulltextfile4']); 
   $i = db_select_1('rev_id', 'reviewer_work', "`proc_id`=".$d['ID'].
                    " AND `date_time_2`<'$fe' AND `again`=1", false);
   if($i) $lk .= ', <span style="color:red;">'.encode('ЗА КОРЕКЦИИ').": $rwc</span>";
   else   $lk .= ", ".encode('за корекции').": $rwc";
}
$rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].
                      ' AND (`decision`=2)');
if($rwc) $lk .= ", ".encode('одобрили').": $rwc";
$ue = db_select_1('email', 'users', "`ID`=".$d['user_id']." AND `type`='$utype'");
$rwc = db_table_field('COUNT(*)', 'mail_sent', 
       "`email`='".$ue['email']."' AND `date_time_2`>'$year-01-01 00:00:00'".
       " AND (`template_id`=17 OR `template_id`=18 OR `template_id`=19".
         " OR `template_id`=20 OR `template_id`=34 OR `template_id`=46)",'',false);
$lk .= ", ".encode('съобщения').": $rwc";
if($rwc)
{
   // Брой доклади на автора
   $c = db_table_field('COUNT(*)', 'proceedings', "`user_id`=".$d['user_id'].
                      " AND `utype`='$utype'");
   $lk .= "/$c";
   $c1 = db_table_field('COUNT(*)', 'mail_sent', "`email`='".$ue['email'].
                      "' AND `template_id`=18");
   if($c1) $lk .= ", ".encode('корекции').": $c1";
   $c2 = db_table_field('COUNT(*)', 'mail_sent', "`email`='".$ue['email'].
                      "' AND `template_id`=17");
   if($c2) $lk .= ", ".encode('приет').": $c2";
   $c3 = db_table_field('COUNT(*)', 'mail_sent', "`email`='".$ue['email'].
                      "' AND (`template_id`=19 OR `template_id`=20)");
   if($c3) $lk .= ", ".encode('отхвърлен').": $c3";
}
return $lk;
}

// Добавяне на разделените със запетаи имена $n в масив $auth
// Ключове на масива са имената, а стойности, броя на изпращанията за добавяне на всяко име

function conference_add_auth(&$auth, $n){
$n = conference_only_names($n);
$ns = explode(',', $n);
foreach($ns as $m) if (!isset($auth[trim($m)])) $auth[trim($m)] = 1; else $auth[trim($m)]++;
//die(print_r($auth));
}

// Преименуване на файловете

function conference_rename_files(&$d){
global $fdir, $utype;
foreach(array('fulltextfile','fulltextfile2') as $f) if ($d[$f]) {
  $n  = 'article'.$d['ID'].'.'.strtolower(pathinfo($d[$f], PATHINFO_EXTENSION));
  if($n==$d[$f]) continue;
  $nn = $_SERVER['DOCUMENT_ROOT'].$fdir.$n;
  $on = $_SERVER['DOCUMENT_ROOT'].$fdir.$d[$f];
  $r = file_exists($on) && rename($on, $nn);
  if($r){
    $d1['ID'] = $d['ID'];
    $d1[$f] = $n;
    $d[$f] = $n;
    db_update_record($d1, 'proceedings');
  }
}
return $d;
}

// Имейли на потребителите в обратан ред на последната актуализация

function conference_participants(){
global $utype, $user_table;
$da = db_select_m('user_id', 'proceedings', "`utype`='$utype' GROUP BY user_id");
$q = '';
foreach($da as $d) $q .= $d['user_id'].', ';
$q = substr($q,0,strlen($q)-2);
$dt = db_select_m('*', $user_table, "`type`='$utype' AND `ID` NOT IN ($q) ORDER BY `date_time_2` DESC");
$rz = '';
$c = 0;
foreach($dt as $d){
  if($c % 50 == 0) $rz .= "<br><br>".$d['date_time_2']."<br><br>\n";
  $rz .= $d['email'].', ';
  $c++;
}
return $rz;
}

// Генериране на сборник резюмета

function conference_abstract_book($uid, $adm, $utype){
if(isset($_GET['proc']) && is_numeric($_GET['proc'])) return conference_1abstract($uid, $adm);
global $main_index, $page_id, $today, $day_start, $day_a_approve, $fdir, $page_hash;
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
// Форми на докладите - масив $fs
eval(translate('conference_forms',false));
$rz = '';
// Начини за подреждане
$order = ' ORDER BY '.stored_value('conference_'.$utype.'_order', '`authors` ASC');
// Начини за филтриране
$access = '';
$filter = stored_value('conference_'.$utype.'_filter', " AND `publish`='yes'");
$fp = '';
if(isset($_GET['until'])){
  $tm = strtotime($_GET['until']);
  if(!is_numeric($tm)) die('Incorect until value.');
  $filter .= " AND `date_time_1`<'".$_GET['until']."'";
  $fp = '&until='.$_GET['until'];
}
if(isset($_GET['after'])){
  $tm = strtotime($_GET['after']);
  if(!is_numeric($tm)) die('Incorect after value.');
  $filter .= " AND `date_time_1`>'".$_GET['after']."'";
  $fp = '&after='.$_GET['after'];
}
// Тайна стойност за разрешаване на достъп
$acb = stored_value('conference_aBookAccess');
// Тайна стойност за разрешаване достъп за рецензиране на резюме
$aca = stored_value('conference_aRev1Access');
// Дали преглеждането е с цел рецензиране
$rev = isset($_GET['ac']) && ($_GET['ac']==$aca);
$abl = false; // Дали е разрешен достъп чрез линк
if($acb && isset($_GET['ac']) && ($_GET['ac']==$acb)) $abl = true;
// Дата за обявяване на приетите пълни доклади
$editp = stored_value('conference_editpaper');
$revp = stored_value('conference_reviewpage');
if($adm || $rev){
  if(!$revp) die('"conference_reviewpage" option not set');
  $c = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `title` > ' ' AND `topic`>-1 $access $filter", '', false);
  $rz .= "<p>Proceeding count: $c\n";
  if(!$rev) $rz .=" &nbsp; Access by <a href=\"$main_index?pid=$page_id&ac=$acb$fp\">Link</a>\n".
    " &nbsp; or &nbsp; <a href=\"$main_index?pid=$page_id&ac=$aca$fp\">For review</a>".
    "</p>\n";
}
else if (($today<$day_start)&&!$abl&&!$rev) 
        return message(translate('conference_contentAfter').db2user_date_time($day_start).$rz);
// За всяко научно направление
for($i = 0; $i<count($tp); $i++){
  $inf = translate($utype.'_sec_'.$i.'_info');
  if($inf!=$utype.'_sec_'.$i.'_info') $rz .= $inf;
  $count = 0;
  // Доклади от секцията
  $da = db_select_m('*', 'proceedings',
        "`utype`='$utype'".
        " AND `title` > ' '".
        " AND `topic`='$i'".
        " ".$filter.$order, false ); //die;
  if(count($da)){ 
     $rz .= '<h2>'.$tp[$i];
     if($adm || $rev) $rz .= ' '.count($da);
     $rz .= "</h2>\n";
  }
  // За всеки доклад
  foreach($da as $d){
//       if(($d['publish']=='yes') || $rev) 
       $count++;
       $rz .= '<div class="who">'."\n";
       $rz .= '<h3 id="pof'.$d['ID'].'">'.($i+1)."-$count. ".mb_strtoupper(stripslashes($d['title']))."</h3>\n";
       // При показване с цел рецензиране, не се показват авторите
       if( !$rev ){
          if(!empty($d['authors'])&&($d['authors'][0]!='<'))
             $rz .= '<p>'.$d['authors']."</p>\n";
          else
             $rz .= $d['authors'];
       }
       if($d['form']) $rz .= '<p>'.$fs[$d['form']].'</p>'."\n";
       if(empty($d['abstract'])) $rz .= '<span style="color:red;">'.encode('ЛИПСВА ТЕКСТ!').'</span>';
       else $rz .= $d['abstract'];
       $rz .= "\n";
       if($d['keywords'])
          $rz .= '<p class="keywords"><span>'.translate_to('conference_ckeywords',$d['language']).'</span> '.
                 conference_formatKeyWorts($d['keywords'])."</p>\n";
       // При показване с цел рецензиране, не се показват данните на авторите 
       if( !$rev ){         
          if(!empty($d['addresses'])&&($d['addresses'][0]!='<'))
              $rz .= '<p>'.stripslashes($d['addresses'])."</p>";
          else
              $rz .= stripslashes($d['addresses']);
       }
       // Линк "Редактиране" не се показва при разглиждане с цел рецензиране
       if($adm && !$abl && !$rev ) 
          $rz .= '<p><a href="'.$editp.'&proc='.$d['ID'].$page_hash.'">'.
                 translate('conference_editpaper')."</a></p>\n";
       // Линк "Рецензиране на резюмето"
       if( ($adm || $rev) && ($today<=$day_a_approve))
           $rz .= '<p><a href="'.$revp.'&rev1='.$d['ID'].'&ac='.$aca.$page_hash.'">'.
           translate('conference_rev1')."</a></p>\n";
       $rz .= '<p>';
       // PDF файл с пълен текст - не се показва при преглеждане за рецензиране
       if($d['fulltextfile2'] && !$rev) $rz .= '<a href="'.$fdir.$d['fulltextfile2'].'">'.
                                translate_to('conference_fulltext',$d['language'])."</a>";
       // DOC файл с пълен текст - не се показва при преглеждане за рецензиране
       if($d['fulltextfile'] && $acb && !$rev) $rz .= ' &nbsp; <a href="'.$fdir.$d['fulltextfile'].'">DOC file</a>';
       $rz .= "</p>\n";
       // PDF файл с анонимен пълен текст - показва се при преглеждане за рецензиране
       if( ($adm || $rev) && $d['fulltextfile4']) $rz .= '<p><a href="'.$fdir.$d['fulltextfile4'].
                                '" target="showFile">'.
                                translate('conference_fulltext4')."</a></p>\n";
       // Лезюме на втори език
       if($d['abstracttextfile']){
          if($abl) $rz .= '<p><a href="'.$fdir.$d['abstracttextfile'].
                                '" target="showFile">'.
                                translate('conference_cabstracttextfile')."</a></p>\n";
//          var_dump($rev); die;
//          if($rev) $rz .= translate_to('conference_abstract_'.$d['ID'],'en');
       }
       $rz .= "</div>\n";
  }
}
return "<div id=\"abstract_book\">\n$rz\n</div>\n";
}

function conference_formatKeyWorts($kw){
$rz = $kw;
$wa = explode(',', $kw);
foreach($wa as $i=>$w){
  $w1 = explode(' ', trim($w));
  $w1[0] = mb_strtoupper(mb_substr($w1[0], 0, 1)).mb_substr($w1[0], 1);
  $wa[$i] = implode(' ', $w1);
}
$rz = implode(', ', $wa);
return $rz;
}

// Показване на едно резюме анонимно, с формуляр за рецензиране

function conference_rev1($uid, $adm){
$aca = stored_value('conference_aRev1Access');
if( ! ($adm || ($aca && isset($_GET['ac']) && ($_GET['ac']==$aca) ) ) )
    return message("Access denied.");
if(count($_POST)) return conference_rev1process();
global $utype, $user_table, $page_hash;
if(!is_numeric($_GET['rev1'])) die("Incorrect rev1 parameter.");
$rz = '<h2>'.translate('conference_rev1title')."</h2>\n";
// Данни за доклада
$d = db_select_1('*', 'proceedings', "`ID`=".$_GET['rev1']." AND `utype`='$utype'");
if(!$d) return message("Incorrect report ID.");
// Данни на влезлия потребител
$ud = db_select_1('*', $user_table, "`ID`=$uid AND `type`='$utype'");
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
$rz .= '<p>'.translate('conference_ctopic').' '.$tp[$d['topic']]."</p>\n";
$rz .= '<p>'.translate('conference_ctitle').' '.$d['title']."</p>\n";
$rz .= '<p>'.translate('conference_cabstract')."</p>\n".$d['abstract']."\n";
$rz .= '<p>'.translate('conference_ckeywords').' '.conference_formatKeyWorts($d['keywords'])."</p>\n";
$rz .= '<h2>'.translate('conference_rev1your')."</h2>\n";
$f = new HTMLForm('conference_rev1form');
$from = ''; $em = '';
if(isset($ud['firstname'])){
  $from = $ud['firstname'].' '.$ud['secondname'].' '.$ud['thirdname'];
  $em = $ud['email'];
}
$f->add_input( new FormInput('', 'url', 'hidden', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) );
$f->add_input( new FormInput(   translate('conference_rev1from'),   'from', 'text', $from) );
$f->add_input( new FormInput(   translate('conference_rev1email'),  'email', 'text', $em) );
$f->add_input( new FormSelect(  translate('conference_nTopic'),     'newtopic', $tp, $d['topic']) );
$f->add_input( new FormTextArea(translate('conference_rev1text'),   'rev1') );
$f->add_input( new FormInput(   translate('conference_rev1approve'),'approved_a', 'checkbox') );
$f->add_input( new FormInput('', '', 'submit', translate('conference_rev1submit') ) );
$rz .= $f->html();
return $rz;
}

// Обработка на данни от рецензиране на резюме

function conference_rev1process(){
global $language, $site_encoding;
$from = $_POST['from'].' <'.$_POST['email'].'>';
$to = $language.'-info@conference.vsu.bg';
$to = 'info@conference.vsu.bg';
$subject = 'Abstract Review';
$body = '<p><a href="'.$_POST['url'].'">'.$_POST['url']."<a></p>\n";
$body .= '<p>Topic: '.$_POST['newtopic']."</p>\n";
$body .= "<p>Review:</p>\n".$_POST['rev1']."\n";
if(isset($_POST['approved_a'])) $body .= "<p>Approve</p>\n";
else $body .= "<p>Do not approve</p>\n";
$hd = 'Content-type: text/html; charset='.$site_encoding."\r\n".
      "From: $from\r\n";
if( mail($to, mb_encode_mimeheader($subject,"UTF-8"), $body, $hd, "-f $from") )
  return message(translate('conference_rev1sent'));
else
  return message(translate('conference_rev1notSent'));
}

// Показване на едно резюме с цел преглеждане от участника или други

function conference_1abstract($uid, $adm){
global $utype, $page_id, $page_hash, $editing;
// Данни за доклада
$pd = db_select_1('*', 'proceedings', '`ID`='.$_GET['proc']);
// Код за достъп
$aca = stored_value('conference_aAbsAccess');
if(!( ($uid==$pd['user_id']) || $adm ||
      (isset($_GET['ac']) && $aca && ($aca==$_GET['ac']))
    )) return 'Access not allowed.';
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
$rz = '<h2>'.$tp[$pd['topic']].'<h2>
<h3>'.mb_strtoupper($pd['title']).'</h3>
';
if(!empty($pd['authors'])&&($pd['authors'][0]!='<'))
   $rz .= '<p>'.$pd['authors']."</p>\n";
else
   $rz .= $pd['authors'];
$rz .= $pd['abstract']."\n";
$rz .= '<p class="keywords"><span>'.translate('conference_ckeywords').'</span> ';
if($pd['keywords']) $rz .= conference_formatKeyWorts($pd['keywords']);
$rz .= "</p>\n";
if(!empty($pd['addresses'])&&($pd['addresses'][0]!='<'))
   $rz .= '<p>'.stripslashes($pd['addresses'])."</p>";
else
   $rz .= stripslashes($pd['addresses']);
if($editing){
   // Линк "Редактиране"
   $editp = stored_value('conference_editpaper');
   $rz .= '<p><a href="'.$editp.'&proc='.$pd['ID'].$page_hash.'">'.
          translate('conference_editpaper')."</a></p>\n";
}
return $rz;
}

// Показване на формуляр за рецензиране на доклад

function conference_rev2($uid, $adm){
global $language;
// Преглед на рецензентите
if($_GET['rev2']=='on') return conference_revList();
global $utype, $user_table, $body_adds, $language, $lock_fields;
$body_adds .= ' onload="CKEDITOR.replace(\'comment1\');'.
                       'CKEDITOR.replace(\'comment2\');'.
                       'CKEDITOR.replace(\'comment3\');"';
$ms = '';
// Обработка на изпратени данни
if(count($_POST)) $ms .= conference_rev2process();
if(!is_numeric($_GET['rev2'])) return message("Incorrect proceeding ID.");
// Данни за доклада
if($_GET['rev2']=='0')
  $d = array('ID'=>0, 'topic'=>0, 'title'=>'Title of the report', 
             'abstract'=>'Hiere you will see the abstract',
             'keywords'=>'A list of keywords.',
             'fulltextfile4'=>'<a href="">Link to the file</a>');
else {
  $d = db_select_1('*', 'proceedings', "`ID`=".$_GET['rev2']." AND `utype`='$utype'");
  if(!$d) return message("Incorrect report ID.");
  $fl = file_link_and_size($d['fulltextfile4']);
  $e1 = conference_file_age($d['fulltextfile4']);
  $e2 = conference_file_age($d['fulltextfile2']);
}
// Рецензентски номер в тази секция на потребителя
$rid = db_table_field('ID', 'reviewers', "`utype`='$utype' AND `user_id`=$uid AND `topic`=".$d['topic'],'',false);
//echo "$rid<br>";
if(!$rid && ($_GET['rev2']>0)) return message(translate('conference_yourNotRev'));
// Данни за рецензията
if($_GET['rev2']=='0')
  $rd = array('ID'=>0, 'date_time_1'=>'', 'date_time_2'=>'', 'rev_id'=>0, 'proc_id'=>0,
              'grade1'=>0, 'grade2'=>0, 'grade3'=>0, 'grade4'=>0, 'grade5'=>0,
              'grade6'=>0, 'grade7'=>0, 'grade8'=>0, 'grade9'=>0, 'grade10'=>0,
              'grade11'=>0, 'grade12'=>0, 'decision'=>'', 
              'comment1'=>'', 'comment2'=>'', 'comment3'=>'', 'locked'=>0);
else
  $rd = db_select_1('*', 'reviewer_work', "`rev_id`=$rid AND `proc_id`=".$_GET['rev2'],false);
if(!$rd) return message("Your are not a reviewer of this article.");
$rz = '<h2>'.translate('conference_rev2title')."</h2>\n";
// Данни на влезлия потребител
$ud = db_select_1('*', $user_table, "`ID`=$uid AND `type`='$utype'");
// Научни направления - масив $tp
eval(translate('conference_topics_'.$utype,false));
$rz .= '<p>'.translate('conference_ctopic').' <b>'.$tp[$d['topic']]."</b></p>\n";
$rz .= '<p>'.translate('conference_ctitle').' <b>'.$d['title']."</b></p>\n";
//if($d['language']=='bg')
{
   $rz .= '<p>'.translate('conference_cabstract')."</p>\n".
   '<div class="abstract">'.$d['abstract']."</div>\n";
   $rz .= '<p class="keywords"><span>'.translate('conference_ckeywords').
          '</span> '.conference_formatKeyWorts($d['keywords'])."</p>\n";
}
$rz .= '<p>'.translate('conference_cfulltextfile4').' '.$fl."</p>\n";
if(!empty($d['revanswer'])){
   $rz .= '<p>'.translate('conference_revanswer')."</p>\n".$d['revanswer'];
}
$rz .= '<h2>'.translate('conference_rev1your')."</h2>\n";
$rz .= message(translate('conference_rev1offline'));
$rz .= '<p>'.uploadfile('revForm_'.$utype.'_'.$d['language'])."</p>\n";
$gr = array( translate_to('conference_notApplicable',$d['language'],false), '1', '2', '3' );
$ds = array(
  translate_to('conference_reject', $d['language'], false),
  translate_to('conference_acceptIf', $d['language'], false),
  translate_to('conference_accept', $d['language'], false)
);
$f = new HTMLForm('conference_rev2form');
foreach($rd as $k=>$v) switch ($k){
case 'ID': case 'date_time_1': case'date_time_2': case 'rev_id': case 'proc_id':
           $f->add_input( new FormInput('', $k, 'hidden', $v) );
           break;
case 'grade1': case 'grade2': case 'grade3': case 'grade4': case 'grade5': case 'grade6':
case 'grade7': case 'grade8': case 'grade9': case 'grade10': case 'grade11': case 'grade12':
           $fi = new FormSelect(translate_to("conference_$k",$d['language']), $k, $gr, $v);
           $fi->values='k';
           if($rd['locked']=="1") $fi->js = ' disabled="disabled"';
           $f->add_input( $fi );
           break;
case 'decision':
           $fi = new FormSelect(translate_to("conference_$k",$d['language']), $k, $ds, $v);
           $fi->values='k';
           if($rd['locked']=="1") $fi->js = ' disabled="disabled"';
           $f->add_input( $fi );
           break;
case 'again': case 'final':
           $fi = new FormInput(translate_to("conference_$k",$d['language']), $k, 'checkbox', 1);
           if($v) $fi->checked = 'checked';
           if(($k!='final') && ($rd['locked']=="1")) $fi->js = ' disabled="disabled"';
           $f->add_input( $fi );
           break;
case 'comment1': case 'comment2': case 'comment3':
           $fi = new FormTextArea(translate_to("conference_$k",$d['language']), $k, 100, 10, $v);
           $fi->ckbutton = '';
           if(($k!='comment3') && ($rd['locked']=="1")) $fi->js = ' disabled="disabled"';
           $f->add_input( $fi );
           break;
case 'locked':
           break;
default:
  $f->add_input( new FormInput(translate_to("conference_$k",$d['language']), $k, 'text', $v) );
}
$f->add_input( new FormInput('', '', 'submit', translate_to('conference_rev1submit',$d['language']) ) );
$rz .= translate('conference_gradeMeaning').$f->html();
return $ms.$rz;
}

function conference_file_age($f){
global $fdir;
$fn = $_SERVER['DOCUMENT_ROOT'].$fdir.$f;
if(!file_exists($fn)) return '0000-01-01 00:00:00';
$rz = gmdate("Y-m-d H:i:s", filemtime($fn));
return $rz;
}

// Запазване на изпратена рецензия

function conference_rev2process(){
//die(print_r($_POST,true));
$_POST['date_time_2'] = 'NOW()';
if(!isset($_POST['again'])) $_POST['again'] = 0;
if(db_update_record($_POST, 'reviewer_work')) return message(translate('dataSaved'));
}

// Показване списък на рецензентите

function conference_revList(){
global $utype, $pedit, $adm_pth, $editing, $page_title; 
$page_title = encode('Резензенти - списък');
// Тематични направления - масив $tp
eval(translate('conference_topics_'.$utype,false));

// Брой одобрени резюмета на български
//$tb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `language`='bg'",0);
// Брой одобрени резюмета на английски
//$te = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `language`='en'",0);

$apr = ' AND `approved_a`=1 AND `fulltextfile2`>\' \'';
$apr = '';

// Брой одобрени пълни текстове на доклади на български
$tb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' $apr AND `language`='bg'",0);
// Брой одобрени пълни текстове на доклади на английски
$te = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' $apr AND `language`='en'",0);

// Брой на всички рецензенти
$rt = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype'");
// Брой на рецензенти на български
$rb = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `languages` LIKE '%bg%'");
// Брой на резензенти на английски
$re = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `languages` LIKE '%en%'");

$rz = '<script>
function confirmRevDel(e){
if(confirm("Confirm that you want to delete reviewer")) document.location = e;
}
</script>
<h2>'.translate('conference_reviewers').conference_revCounts(intval($tb)+intval($te),$rt,$tb,$rb,$te,$re).'</h2>
<p><a href="'.unset_self_query_var('rev2').'" target="partList">'.translate('conference_partList')."</a></p>\n";
// За WHERE на rev_id-тата на рецензентите от текущата година
$rids = '';
$da = db_select_m('ID', 'reviewers', "`utype`='$utype' ORDER BY `ID` ASC");
foreach($da as $d) $rids .= '`rev_id`='.$d['ID'].' OR ';
$rids = substr($rids, 0, -4);
// Общ брой назначени рецензии
$trc = db_table_field('COUNT(*)', 'reviewer_work', $rids);
// Брой актуализирани рецензии
$arc = db_table_field('COUNT(*)', 'reviewer_work', "($rids) AND (`date_time_2`>`date_time_1`)",0,false);
if($trc>0) $rz .= "<p>".translate('conference_revPercent').
                  " $arc/$trc ".number_format($arc/$trc*100,1)."%</p>\n";
// За всяко научно направление
foreach($tp as $i=>$t){
  // Данни на рецензентите по научно направление с номер $i
  $q = '';
  if($editing) $q = 'a.ID as rid, ';
  $dt = db_select_m($q.'b.ID, b.position, b.firstname, b.secondname, b.thirdname, a.languages, a.confirmed',
      '`reviewers` AS a RIGHT JOIN `users` AS b ON a.user_id=b.ID',
      "a.utype='$utype' AND a.topic=$i ORDER BY b.thirdname ASC");

    foreach($dt as $j=>$d){
       if($editing){
          $dt[$j]['rid']= '<a href="'.$adm_pth.'delete_record.php?t=reviewers&r='.$d['rid'].
                       '" style="color:red;" title="Delete reviewer" onclick="confirmRevDel(this);return false;">'.$d['rid'].'</a> ';
          $dt[$j]['ID'] = '<a href="'.$pedit.'&uid='.$d['ID'].'" target="userData" title="Edit user\'s record">*</a> '.
                       '<a href="'.current_pth(__FILE__).'make_reviewer.php?uid='.$d['ID'].
                       '" target="makeRev" title="Edit reviewer\'s record">'.$d['ID'].'</a>';
       }
       $uids = db_select_m('ID', 'reviewers', "`utype`='$utype' AND `user_id`=".$d['ID'], false);
       $q = '';
       foreach($uids as $id){
         if($q) $q .= ' OR ';
         $q .= '`rev_id`='.$id['ID'];
       }
       $c1 = db_table_field('COUNT(*)', 'reviewer_work', "($q)  AND `decision`>='0'",0,false);
       $c2 = db_table_field('COUNT(*)', 'reviewer_work', "$q",0,false);
       $cl = 'black';
       if($c1==$c2) $cl = 'green';
       if($c1==0) $cl = 'red';
       if(($c1>0) && ($c1<$c2)) $cl = 'orange';
       if(in_edit_mode()) 
          $c1 = '<a href="/index.php?pid=79&uid='.$d['ID'].'" target="_blank">'.$c1.'</a>';
       $dt[$j]['count'] = "<span style=\"color:$cl;\">$c1 / $c2</spam>";
    }
//      die(print_r($dt,true));
  // Брой доклади на български
  $cb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `topic`=$i AND `language`='bg'");
  // Брой доклади на английски
  $ce = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `topic`=$i AND `language`='en'");
  // Брой рецензенти на български
  $rb = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `topic`=$i AND `languages` LIKE '%bg%'");
  // Брой рецензенти на английски
  $re = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `topic`=$i AND `languages` LIKE '%en%'");
  $rz .= "<h3>$t".conference_revCounts($cb+$ce,count($dt),$cb,$rb,$ce,$re)."</h3>\n".
         view_table($dt);
}
return $rz;
}

// Помощна функция показваща съотношението брой доклади / брой рецензенти

function conference_revCounts($tp,$rt,$tb,$rb,$te,$re){
  return " - $tp/$rt(".( ($rt==0)? "-" : number_format($tp/$rt,1) ).") ".
         "bg:$tb/$rb(".( ($rb==0)? "-" : number_format($tb/$rb,1) ).") ".
         "en:$te/$re(".( ($re==0)? "-" : number_format($te/$re,1) ).")";
}

// Показване на рецензиите на доклад или рецензент

function conference_procRevs(){
global $utype, $adm_pth, $editing, $year, $page_title;
// Проверки за коретност на изпратените с $_GET параметри
if(empty($_GET['proc']) || !is_numeric($_GET['proc'])){
   if(empty($_GET['uid']) || !is_numeric($_GET['uid']))
      return message('Missing or incorrect "proc" or "uid" parameter.');
   // Ако е изпратен с $_GET['uid'] ид. номер на потребител - рецензент
   $us = db_select_m('ID,utype', 'reviewers', "`user_id`=".$_GET['uid']." AND `utype`='$utype'");
   $q = '';
   foreach($us as $u) $q .= ' OR `rev_id`='.$u['ID'];
   $q = substr($q,4);
//   $page_title .= translate('conference_revsOfRev',false);
//   die($page_title);
}
// Ако е изпратен с $_GET['proc'] ид. номер на доклад
else{
   $q = "`proc_id`=".$_GET['proc'];
}
// Данни за рецензиите на доклада
$rws = db_select_m('*', 'reviewer_work', $q,false);
//die(print_r(substr($q,4), true));
$rz = '';
$cp = array(
//'ID'=>'',
//'date_time_2'=>'',
//'rev_id'=>'',
'proc_id'=>translate('conference_proc_id',false),
'title'=>translate('usermenu_newpagetitle',false),
'topic'=>translate('conference_ctopic',false),
'date_time_1'=>translate('conference_rev2dt1',false),
'dl'=>translate('usermenu_rev2dl',false),
'grade1'=>translate('conference_grade1',false),
'grade2'=>translate('conference_grade2',false),
'grade3'=>translate('conference_grade3',false),
'grade4'=>translate('conference_grade4',false),
'grade5'=>translate('conference_grade5',false),
'grade6'=>translate('conference_grade6',false),
'grade7'=>translate('conference_grade7',false),
'grade8'=>translate('conference_grade8',false),
'grade9'=>translate('conference_grade9',false),
'grade10'=>translate('conference_grade10',false),
'grade11'=>translate('conference_grade11',false),
'grade12'=>translate('conference_grade12',false),
'decision'=>translate('conference_decision',false),
'comment1'=>translate('conference_comment1',false),
'comment2'=>translate('conference_comment2',false),
'comment3'=>translate('conference_comment3',false),
'again'=>translate('conference_again',false),
);
$ds = array(
  '<span style="color:red">'.translate('conference_reject', false).'</span>',
  '<span style="color:darkorange">'.translate('conference_acceptIf', false).'</span>',
  '<span style="color:green">'.translate('conference_accept', false).'</span>'
);
// С всяка рецензия
foreach($rws as $i => $rw){ //die(print_r($rw, true));
  // Данни за доклада
  $pd = db_select_1('title,topic,fulltextfile4', 'proceedings', "`ID`=".$rw['proc_id']);
  $fn = stored_value('conference_files_'.$utype).$pd['fulltextfile4']; 
  $rw['title'] = '<a href="'.$fn.'" target="file">'
                    .$pd['title'].'</a>';
  $rw['topic'] = conference_htopic($pd['topic']);
  $rw['proc_id'] .= ' / '.$year;
  $rw['dl'] = encode('7 дни');
  if($rw['decision']!='') $rw['decision'] = $ds[$rw['decision']];
  for($j=1; $j<13; $j++) 
      if(isset($rw["grade$j"]) && ($rw["grade$j"]==0)) 
         $rw["grade$j"] = translate('conference_notApplicable',false);
  $rz .= '<h2>'.encode('ФОРМА<br>за рецензия на статия от Международната научна конференция на ВСУ').
         "</h2>\n";
  if($editing){
     $rz .= '<p>';
     $lks = current_pth(mod_path('conference')).'lock_review.php?ID='.$rw['ID'].'&';
     if($rw['locked']){ 
        $rz .= '<span style="color:red">LOCKED<span>';
        $rz .= ' <a href="'.$lks.'locked=0">unlock</a>';
     }
     else $rz .= ' <a href="'.$lks.'locked=1">lock</a>';
     $rz .= '</p>';
     
  }
  if($editing) $rz .= '<a href="'.$adm_pth.'edit_record.php?t=reviewer_work&r='.$rw['ID'].'" target="_blank">*</a> ';
  $rz .= view_record($rw,$cp).
         '<p>'.encode('Дата на предаване на рецензията: ');
  if($rw['date_time_2']>$rw['date_time_1']) $rz .= $rw['date_time_2'];
  else $rz .= '<span style="color:red">'.encode("НЕ е предадена").'</span>';
  $ft = filemtime($_SERVER['DOCUMENT_ROOT'].$fn);
  $rz .= '<br>'.encode('Дата на файла: ').date("Y-m-d H:m:s",$ft);
  $rz .= "</p>\n";
  $rz .= "<p>&nbsp;</p>\n<p>".encode('Рецензент: ').$rw['rev_id'];
  if($editing){ 
     $uid = db_table_field('user_id','reviewers', "`ID`=".$rw['rev_id']);
     $ud = db_select_1('position,firstname,thirdname','users',"`ID`=$uid");
     $rz .= ' '.implode(' ',$ud).' '; 
  }
  $rz .= " ..................................</p>\n";
}
return $rz;
}

// Показване пред участника на решението на рецензентите за доклада с данни $d

function conference_viewRev2rezult($d, &$locked){
if(!isset($d['ID'])) return '';
global $adm_pth, $editing, $editing;
// Данни от рецензиите
$rw = db_select_m('*', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision` IS NOT NULL');
$rz = '';
$ds = array(
  '<span style="color:red">'.translate('conference_reject', false).'</span>',
  '<span style="color:darkorange">'.translate('conference_acceptIf', false).'</span>',
  '<span style="color:green">'.translate('conference_accept', false).'</span>'
);
$rzr = '';
foreach($rw as $i=>$w){
  $rzr .= '<h4>'.translate('conference_rev2Dec').($i+1)."</h4>\n";
  $rzr .= '<p>'.translate('conference_decision').' '.$ds[$w['decision']]."</p>\n";
  if($w['comment1'])
     $rzr .= '<h5>'.strip_tags(translate('conference_comment1'))."</h5>\n".
            '<p>'.$w['comment1']."</p>\n";
  if($w['comment2'])
     $rzr .= '<h5>'.strip_tags(translate('conference_comment2'))."</h5>\n".
            '<p>'.$w['comment2']."</p>\n";
  if($w['comment3'])
     $rzr .= '<h5>'.strip_tags(translate('conference_comment3'))."</h5>\n".
            '<p>'.$w['comment3']."</p>\n";
  if($editing) $rzr .= '<a href="'.$adm_pth.'edit_record.php?t=reviewer_work&r='.$w['ID']."\">*</a>\n";
  $locked += $w['locked'];
//  var_dump($w);
}
//var_dump($locked);
$rz .= $rzr;
return $rz;
}

// Помощна функция от https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match

function str_replace_first($search, $replace, $subject)
{
    $search = '/'.preg_quote($search, '/').'/';
    return preg_replace($search, $replace, $subject, 1);
}

// Визуализиране като списък на научните направления

function conference_topics($ut){
global $utype, $language, $adm_pth, $editing;
if(empty($ut)) $ut = 'vsu2021';
// Научни направления - масив $tp
eval(translate('conference_topics_'.$ut, false));
$rz = "<div id=\"ctopics\"><h2>".translate('conference_topics_heading_'.$ut)."</h2>\n";
foreach($tp as $i=>$t){
$t1 = str_replace_first('. ', '.</span> ', $t);
$rz .= '<p><span>'.$t1."</p>\n";
}
if($editing){
  $id = db_table_field('ID', 'content', "`name`='conference_topics_$utype' AND `language`='$language'");
  $rz .= '<a href="'.$adm_pth.'edit_record.php?t=content&r='.$id.'">*</a>';
}
$rz .= "\n</div>\n";
return $rz;
}

// Програма на конференцията

function conference_program(){
global $utype;
eval(translate('conference_topics_'.$utype, false));
// Данни за докладите, които ще бъдат изнесени
$da = db_select_m('*', 'proceedings', "`utype`='$utipe'");
}

?>
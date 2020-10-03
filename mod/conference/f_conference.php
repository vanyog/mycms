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
include_once($idir.'mod/usermenu/f_usermenu.php');
include_once($idir.'mod/uploadfile/f_uploadfile.php');

global $user_table, $utype, $fdir, $day1, $day2, $day3, $day4, $adm_pth, $page_header, $proccount, $plogin, $pedit;

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
// Име на събитие и име на график от таблица 'schedules' на срока за качване на резюмета
$day1 = explode(',', stored_value('conference_day1event','schedule_event_2,schedule_1'));
// Име на събитие и име на график от таблица 'schedules' на срока за качване на пълния текст на докладите
$day2 = explode(',', stored_value('conference_day2event','schedule_event_4,schedule_1'));
// Име на събитие и име на график от таблица 'schedules' на срока за обявяване на приетите резюмета
$day3 = explode(',', stored_value('conference_day3event'));
if(count($day3)<>2) die("'conference_day3event' not set");
// Име на събитие и име на график от таблица 'schedules' на срока за обявяване на приетите пълни текстове
$day4 = explode(',', stored_value('conference_day4event'));
//if(count($day4)<>2) die("'conference_day4event' not set");
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

// Главна функция на модула
// Параметърът $a може да се състои от две части, отделени с |
// При различни стойности на първата част на параметъра $a функцията връща:
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

// Втората част на параметъра $a, след знака | съдържа други параметри

function conference($a = ''){

global $user_table, $can_manage, $utype, $day1, $day2, $proccount, $pth, $plogin, $pedit;

// Номер на влезлия потребител
$uid = userreg_id($utype);

// Дали потребителят е администратор
$adm = isset($can_manage['conference']) && $can_manage['conference'];

$aa = explode('|', $a);
if(!isset($aa[1])) $aa[1] = '';

if(isset($_GET['rev1'])) return conference_rev1($uid, $adm);
if(isset($_GET['rev2'])) return conference_rev2($uid, $adm);

switch ($aa[0]){
case ''            : break;
case 'admin'       : return conference_admin($uid);
case 'edit'        : return conference_edit($uid);
case 'stats'       : return conference_stats();
case 'abstract_t'  : return conference_abstract_titles($aa[1]);
case 'abstracts'   : return conference_abstract_book($uid, $adm);
case 'participants': return conference_participants();
case 'review'      : return '<p class="message">'."Missing report ID.</p>\n";
case 'procrevs'    : return conference_procRevs();
default            : return '<p class="message">'."Unknown parameter value '$a' in 'conference() function.</p>\n";
}

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

$rz = '';
// Данни за докладите на потребителя
$pd = db_select_m('*', 'proceedings', "`user_id`=$uid AND `utype`='$utype'");
// Съобщение за превишаване броя
if(count($pd)>$proccount) $rz .= '<p class="message">'.translate('conference_manyreps')."</p>\n";

// Ако потребителят е потвърден рецензент
$rz .= conference_userRevList($uid);

$rz .= '<h2>'.translate('conference_mydata').'</h2>
'.view_record($d, $cp);
$rz .= '<p><a href="'.$pedit.'">'.translate('conference_mypersonal')."</a>";
if(!count($pd)) $rz .= ' &nbsp; <a href="'.$pth.'mod/userreg/delete_me.php" onclick="confDelUser(this);return false;">'.
                        translate('conference_deleteuser');
$rz .= "</a></p>\n";

$d1 = ''; $did = ''; $d2 = ''; $di2 = '';
if (count($pd))   { $d1 = conference_trprec($pd[0]); $did = '&amp;proc='.$d1['ID']; }
if (count($pd)>1) { $d2 = conference_trprec($pd[1]); $di2 = '&amp;proc='.$d2['ID']; }
$cp = array(
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

// Показване данните за първи доклад
$rz .= '<h2>'.translate('conference_mypaper').'</h2>
<h3>'.translate('conference_1paper')."</h3>\n";
// Дали e период за редактиране
$et = schedules_in_event($day1[1],$day1[0]) || $adm;
if (!$et && !count($pd)) return $rz.'<p class="message">'.translate('conference_noabs1')."</p>\n";
// Дали е период за качване на доклади
$ut = schedules_in_event($day2[1],$day2[0]) || $adm;
// Адрес на страницата за редактиране на резюме и качване на доклад от участник
$edp = stored_value('conference_editpaper', '/index.php?pid=1068');
// Линк "Редактиране"
if ($ut) $rz .= '<p><a href="'.$edp.$did.'">'.translate('conference_editpaper').'</a>';
// Линк "Изтриване"
if (count($pd)>0) $rz .= ' &nbsp; <a href="'.current_pth(__FILE__).'delete_paper.php?a=1'.$did.
                         '" onclick="confDelPaper(this);return false;">'.
                         translate('conference_deletepaper').'</a>';
$rz .= "</p>\n";
$rz .= view_record($d1, $cp).'
<p>&nbsp;</p>
'.translate('conference_feenote').'
<p>&nbsp;</p>
';

// Ако има въведен един доклад - показване данните и за втори доклад
if (!$et && (count($pd)<$proccount))
  return $rz.'<h3>'.translate('conference_2paper').'</h3>'.
         '<p class="message">'.translate('conference_noabs1').'</p>';
//if ((count($pd)>0) && $di2) {
if (count($pd)>0) {
  $rz .= '<h3>'.translate('conference_2paper').'</h3>';
  if ($ut){
     $rz .= '<p><a href="'.$edp.$di2.'">'.translate('conference_editpaper')."</a>\n".
            ' &nbsp; <a href="'.current_pth(__FILE__).'delete_paper.php?a=1'.$di2.
            '" onclick="confDelPaper(this);return false;">';
     if($di2) $rz .= translate('conference_deletepaper').'</a></p>';
  }
  $rz .= view_record($d2, $cp);
}
$rz .= '
<p>&nbsp;</p>';

return $rz;
}

// Списък заглавия на доклади, които трябва да рецензира, потребител с номер

function conference_userRevList($uid, $url=false){
global $utype, $fdir;
$rz = '';
$rv = db_select_m('*', 'reviewers', "`utype`='$utype' AND `user_id`=$uid AND `confirmed`=1 ORDER BY `topic` ASC");
if(count($rv)){
  if(!$url) $rz .= '<h2>'.translate('conference_reviewing')."</h2>\n";
  $rvp = stored_value('conference_reviewpage');
  foreach($rv as $r){
    $rp = db_select_m('proc_id', 'reviewer_work', '`rev_id`='.$r['ID']);
    foreach($rp as $p){
      if($url){
         $rz .= mb_strtoupper( db_table_field('title', 'proceedings', '`ID`='.$p['proc_id']) )."\n".
         $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$fdir.
         db_table_field('fulltextfile4', 'proceedings', '`ID`='.$p['proc_id'])."\n";
      }
      else
         $rz .= '<p><a href="'.$rvp.'&rev2='.$p['proc_id'].'" target="_blank">'.
                mb_strtoupper( db_table_field('title', 'proceedings', '`ID`='.$p['proc_id']) )."</a></p>\n";
    }
  }
//  die(print_r($rp,true));
}
return $rz;
}

//
// Изписване с думи на формата и тематичното направление, линкове към файловете с пълния текст
// и др. промени над данните преди показването им

function conference_trprec($d){
global $utype;
// Форми на докладите
eval(translate('conference_forms',false));
// Тематични направления
eval(translate('conference_topics_'.$utype,false));
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

function file_link_and_size($fn){
global $fdir;
if (!$fn) return '';
// Абсолютен път към файла
$af = $_SERVER['DOCUMENT_ROOT'].$fdir.$fn;
// Големина на файла
if (file_exists($af)) $sz = filesize($af);
else return "$fn - <span class=\"message\">".translate('conference_filenotexists')."</span>";
if (!is_local()) $fl = rawurlencode($fn);
else $fl = $fn;
$gh = '';
$fe = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
if(in_edit_mode() && in_array($fe,array('doc','docx')) && !is_local())
  $gh = 'https://docs.google.com/gview?url='.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
return "<a href=\"$gh$fdir$fl\">$fn</a> - $sz bytes";
}

//
// Форма за редактиране на данните за доклад

function conference_edit($uid){
global $body_adds, $utype, $can_manage, $day1, $day2, $debug_mode, $fdir, $user_table;
usermenu(true);
//if(!empty($debug_mode)) print_r($GLOBALS);
// Дали се редактира от администратор
$adm = isset($can_manage['conference']) && ($can_manage['conference']==1);
// Дали сме в период за качване на пълния текст
$ut = schedules_in_event($day2[1], $day2[0]) || $adm;
//if (!$ut) return '<p class="message">'.translate('conference_nofull')."</p>";
if (count($_POST)) return conference_pprocess($uid);
if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['conference_returnpage'] = $_SERVER['HTTP_REFERER'];
else $_SESSION['conference_returnpage'] = stored_value('conference_editpage', '/index.php?pid=1074');
global $languages, $language;
// Начални стойности на полетата
$d = array(
 'user_id'=>$uid,
 'form'=>0,
 'topic'=>0,
 'fee'=>0,
 'currency'=>'BGN',
 'approved_a'=>0,
 'approved_f'=>0,
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
// Ако има номер на запис, четене от таблица 'proceedings'.
if (isset($_GET['proc'])){
  $d = db_select_1('*', 'proceedings', "`ID`=".(1*$_GET['proc']) );
  $d['topic']++;
}
if ( !( ($d['user_id']==$uid) || $adm ) )
   return '<p class="message">'.translate('conference_cnnotedit').'</p>';
// Име на участника
$ud = db_select_1('*', $user_table, "`ID`=".$d['user_id']);
$un = $ud['firstname']." ".$ud['secondname']."  ".$ud['thirdname'];
if(empty(trim($un))) $un = $ud['email'];
if($adm) $un = '<a href="'.stored_value('conference_admin','/index.php?pid=1358').'#pof'.$d['user_id']."\">$un</a>";
//die(print_r($ud,true));
$f = new HTMLForm('conference_peform',true,false);
$f->add_input( new FormInput('', 'user_id', 'hidden', $d['user_id']) ); 
// Форми на докладите
eval(translate('conference_forms'));
// Тематични направления
eval(translate('conference_topics_'.$utype,false));
array_unshift($tp,translate('conference_choos',false));
//die(print_r($d,true));
// Ако се редактира от администратор - поле за платена такса и полета за одобряване
if ($adm) {
  $ti = new FormCurrencyInput(encode('Такса:'), 'fee', 'currency', $d['fee'], $d['currency']);
  $f->add_input($ti);
  $ti = new FormInput(encode('Одобрено резюме:'), 'approved_a', 'checkbox');
  if ($d['approved_a']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('Одобрен пълен текст:'), 'approved_f', 'checkbox');
  if ($d['approved_f']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('Ключов доклад:'), 'keylec', 'checkbox');
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
$et = schedules_in_event($day1[1], $day1[0]) || $adm;
// Съставяне на формата за редактиране
$ti = new FormSelect(translate('usermenu_language'), 'language', $languages, $d['language']);
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
$ti = new FormInput(translate('conference_pages'), 'pages', 'text', $d['pages']);
if (!$adm) $ti->js = ' disabled="disabled"';
$f->add_input($ti);
$fl = new FormInput(translate('conference_cabstracttextfile'), 'abstracttextfile', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['abstracttextfile']));
if (!$ft) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile'), 'fulltextfile', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile']));
if (!$ft) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile2'), 'fulltextfile2', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile2']));
if (!$ft) $fl->js = ' disabled="disabled"';
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile3'), 'fulltextfile3', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile3']));
$fl->size = 63;
$f->add_input($fl);
if($adm){
  $fl = new FormInput(translate('conference_cfulltextfile4'), 'fulltextfile4', 'file', $_SERVER['DOCUMENT_ROOT'].
                      $fdir.stripslashes($d['fulltextfile4']));
  $fl->size = 63;
  $f->add_input($fl);
}
$fi = new FormInput('', '', 'button', translate('conference_csubmit') );
$fi->js = ' onclick="checkArticleForm();"';
$f->add_input( $fi );
// Съобщение, че някои полета са неактивни
$ms = '';
if (!$et) $ms = '<p class="message">'.translate('conference_noabs')."</p>";
if (!$ft) $ms = '<p class="message">'.translate('conference_nofull')."</p>";
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

function conference_pprocess($uid){// die(print_r($_FILES,true));
global $language, $can_manage, $utype;
// Данни за доклад
$d = array(
'user_id'=>(1*$_POST['user_id']),
'utype'=>$utype,
'date_time_2'=>'NOW()'
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
if (isset($_POST['pages'])) $d['pages']=(1*$_POST['pages']);
if (isset($_POST['place'])) $d['place']=(1*$_POST['place']);
// Данни за платена такса
$d2 = array(); 
if (isset($_POST['fee'])) $d2['fee']=addslashes($_POST['fee']);
if (isset($_POST['currency'])) $d2['currency']=addslashes($_POST['currency']);
// Качване на второ резюме, ако е изпратено
$r = conference_upload('abstracttextfile',$uid);
$ms = $r[0];
if ($r[1]) $d['abstracttextfile' ] = $r[1];
// Качване на doc файл, ако е изпратен
$r = conference_upload('fulltextfile',$uid);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile' ] = $r[1];
// Качване на pdf файл, ако е изпратен
$r = conference_upload('fulltextfile2',$uid);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile2'] = $r[1];
// Качване на файл с презентация, ако е изпратен
$r = conference_upload('fulltextfile3',$uid);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile3'] = $r[1];
// Качване на файл с анонимен пълен текст, ако е изпратен
//if(isset($_POST['fulltextfile4']))
{ // Проверката се налага, защото този файл се
  // показва във формата и се качва само от администратор.
//  die($_POST['fulltextfile4']);
  $r = conference_upload('fulltextfile4',$uid);
  $ms .= $r[0];
  if ($r[1]) $d['fulltextfile4'] = $r[1];
}
// Ако е изпратен номер на доклад с $_GET['proc']
// се актуализират данните на доклада с този номер
if (isset($_GET['proc'])){
  $d['ID'] = 1*$_GET['proc'];
  db_update_record($d, 'proceedings');
}
// Иначе се вмъкват данни за нов доклад
else{
  $d['date_time_1'] = 'NOW()';
  $i = db_table_field('COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `user_id`=".$d['user_id']);
  if ($i<2) db_insert_1($d, 'proceedings');
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
return '<p class="message">'.$ms.translate('dataSaved').'</p>';
}

//
// Качване на файл от $_POST[$fn]
// Връща двумерен масив с първи елемент - съобщение
// и втори елемент - име на качения файл

function conference_upload($fn,$uid){
global $fdir, $utype;
$fl = $_FILES[$fn];//die(print_r($_FILES,true));
$ms = ''; $n = '';
if (!$fl['error']){
  // Проверка дали друг потребител не е качил файл със същото име
  $n = db_table_field($fn, 'proceedings',
       "`$fn`='".$fl['name']."' AND `user_id`<>$uid AND `utype`='$utype'", '');
//  die($n);
  if ($n) $ms = translate('conference_fileagain')."<br>\n";
  else {
    // Име на качен вече от потребителя стар файл
    $of = db_table_field($fn, 'proceedings', "`user_id`=$uid AND `utype`='$utype'");
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
global $can_manage, $utype, $page_header, $can_visit, $user_table, $proccount, $pedit;
usermenu(true);
$is_editor = isset($can_manage['conference']) && ($can_manage['conference']==1);
if ( empty($can_manage['conference']) )
   return "<p class=\"message\">You have no permission to view this information.</p>";
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
$ud = db_select_m('*', $user_table, "`type`='$utype' AND `username`>''$q");
// Четене данните за доклади
$pd = db_select_m('*', 'proceedings', "`utype`='$utype' ORDER BY `user_id`");
// Масив с трите имена на участниците с индекси id-тата им
$u = array();
// Масив с броя на заявените доклади на всеки потребител с индекси id-тата им
$up = array();
foreach($pd as $d) if (isset($up[$d['user_id']])) $up[$d['user_id']]++; else $up[$d['user_id']]=1;

$rz = '<p><a href="'.set_self_query_var('rev2','on').'">'.encode('Рецензенти')."</a></p>\n";

$rz .= encode('<p>Регистрирани потребители - ').count($ud).encode(', резюмета - ').count($pd).
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
foreach($ud as $d){
  $u[$d['ID']]=$d['firstname'].' '.$d['secondname'].' '.$d['thirdname'];
  $rz .= '<tr>
<td id="us'.$d['ID'].'">
<a href="'.$ep.'&amp;uid='.$d['ID'].'" target="_blank">'.$d['ID'].'</a>';
  if(isset($can_manage['userreg']) && ($can_manage['userreg']==1))
     $rz .= "\n<a href=\"".current_pth(mod_path('userreg')).'login_as.php?uid='.$d['ID'].'">></a>';
  $rz .= "\n".' <a href="'.current_pth(__FILE__).'make_reviewer.php?uid='.$d['ID'].'" title="Make reviewer" target="_blank">R</a>';
  $rz .= '
</td>
<td><a style="cursor:pointer;" title="Click to copy e-mail address." onclick="copyEMail(this);">'.$d['email'].'</a>';
  if (substr($sby, 0, -1)=='date_time_') $rz .= "<br>".$d[$sby];
  $rz .= '</td>
<td>'.$u[$d['ID']].'</a></td>
<td>'.$d['country'].'</td>
<td>'.$d['institution'].'</td>
<td>'.$d['address'].'</td>
<td>'.$d['telephone']."</td>\n";
  $rz .= '<td>';
  if (isset($up[$d['ID']])) $rz .= '<a href="#pof'.$d['ID'].'">'.$up[$d['ID']]."</a>";
  if ($is_editor && (!isset($up[$d['ID']]) || ($up[$d['ID']]<$proccount)) ) $rz .= " <a href=\"$lk?uid=".$d['ID'].'">+</a>';
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
// Форми на докладите
eval(translate('conference_forms',false));
// Тематични направления
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
  $d['form'] =$fs[$d['form']];
  if(isset($tp[$d['topic']])) $d['topic']=$tp[$d['topic']];
  else $d['topic'] = "-?-";
  $d['title']='<strong>'.$d['title'].'</strong>';
  $d['abstracttextfile' ]=file_link_and_size($d['abstracttextfile' ]);
  $d['fulltextfile' ]=file_link_and_size($d['fulltextfile' ]);
  $d['fulltextfile2']=file_link_and_size($d['fulltextfile2']);
  // Адрес на страницата за редактиране на резюме и качване на доклад от участник
  $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
  // Отбелязване със зелена линия отгоре, ако е ключов доклад
  $st = '';
  if($d['keylec']) $st = 'border-right:solid 4px green;';
  $rz .= encode('<p>Доклад <strong>No:'.$d['ID'].'</strong> <a href="'.$edp.'&amp;proc='.$d['ID'].'">Редактиране</a></p>
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
  'fulltextfile2'=>translate('conference_cfulltextfile2')
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
$cf = db_select_m('form,COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>'' GROUP BY `form`" );
// Тематични направления $tp
eval(translate('conference_topics_'.$utype,false));
// Форми на докладите $fs
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

//
// Показване заглавията на приетите резюмета

function conference_abstract_titles($a = ''){
global $utype, $adm_pth, $can_manage, $can_edit, $day3, $page_header, $fdir, $user_table;
if($a){
  $utype = $a;
  $fdir = stored_value('conference_files_'.$utype, $fdir);
}
$crp = current_pth(__FILE__);
$pdf = '<img src="'.$crp.'Download-PDF.png">';
$ppt = '<img src="'.$crp.'Download-PPT.png">';
$page_header .= '<script>
function deleteAbstract(id){
if( confirm("'.encode('Потвърждавате ли изтриване на запис за доклад с ID=').'"+id+"?") )
   document.location = "'.$adm_pth.'delete_record.php?t=proceedings&r="+id;
}
</script>';
usermenu(true);
// Дали потребителят е от екипа на конференцията
$team = !empty($can_manage['conference']) || $can_edit;
// Преглед преди обявената дата
$preview = !empty($_GET['allowtoshow']) && ($_GET['allowtoshow']=='basa-team');
// Томове
$vl = db_select_m('vol', 'proceedings', "`utype`='$utype' GROUP BY `vol`");
// Тематични направления $tp
eval(translate('conference_topics_'.$utype,false));
// Форми на докладите
eval(translate('conference_forms',false));
$rz = '';
$tc = 0; // Общ брой доклади
$docs = 0; // Брой doc файлове с пълен текст на доклад
$pdfs = 0; // Брой pdf файлове с пълен текст на доклад
$rc = 0; // Брой готови доклади
// Дата за обявяване на приетите резюмета
$t3 = db_table_field('date_time_2', 'schedules', "`sch_name`='".$day3[0]."' AND `ev_name`='".$day3[1]."'");
// Настоящия момент
$td = date('Y-m-d H:i:s');
if (("$td"<"$t3") && !$team && !$preview)
    return '<p class="message">'.translate("conference_shoeafter").db2user_date_time($t3)."</p>\n";
// Начин на подреждане
//$order = ' ORDER BY `place` ASC';
//$order = ' ORDER BY  `keylec` DESC, `title` ASC';
//$order = ' ORDER BY  `form` ASC, `title` ASC';
$order = ' ORDER BY `title` ASC';
$olink = ' <a href="'.set_self_query_var('order','date').'">By title</a>';
if(isset($_GET['order']) && ($_GET['order']=='date') ){
  $order = " ORDER BY `date_time_2` DESC";
  $olink = ' <a href="'.unset_self_query_var('order').'">By date</a>';
}
// Имена на автори, които се колекционират с цел статистика
$auth = array();
// Флаг за показване на авторите и номерата на докладите
$s_auth = false;
//$s_auth = isset($_GET['authors']) && ($_GET['authors']='Yes');
// За всеки том
foreach($vl as $vl1) {
if($vl1['vol']=='p'){
  $pc = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `vol`='p' AND `approved_a`=1", 0);
  if(!$pc && !$team) continue;
  $rz .= '<h2>'.translate('conference_posters')."</h2>\n";
}
else if(count($vl)>1) $rz .= '<h2>'.translate('conference_volume').' '.$vl1['vol']."</h2>\n";
// Номера на страници в том на поредния доклад
$pn = array(0=>1, 1=>1, 2=>1, 3=>1);
// За всяко научно направление
for($i = 0; $i<count($tp); $i++){
   $zip_command = 'zip arh_'.($i+1).'.zip ';
   $c = 0; $c2 = 0; $doc = 0; $pdf = 0; // Брой доклади в научнато направление
   $cn = array(0=>0, 1=>0, 2=>0, 3=>0, 'p'=>0);
   $sr = '';  // html код на докладите от научното направление
//   $filter = ' AND `approved_a`';
   $filter = " AND `fulltextfile2`>' '";
   if ( $team || $preview ) $filter = '';
   // За всеки от езиците
//   foreach($ln as $l)
   {
     // Доклади от секцията
     $da = db_select_m('*', 'proceedings',
          "`utype`='$utype'".
          " AND `topic`='$i'".
          " AND `vol`='".$vl1['vol']."'".
          $filter.$order );
     $lr = ''; // html код на докладите на език $l от научното направление
     $c += count($da);
     // Текуща форма
     $fr = -1;
     // За всеки доклад
     foreach($da as $d){
        if(($utype>='vsu2020') && ($d['form']!=$fr)){
//           $lr .= '<h4>'.$fs[$d['form']]."</h4>\n";
           $fr = $d['form'];
        }
        $lk = ''; // Забележки, които се показват в режим редактиране
        $cr = 6; // Число, което се намалява с единица при всяка забележка
        $nm = ''; // Номера пред заглавието
        $st = ' id="pof'.$d['ID'].'"'; // id атрибут на доклада
        $stl = ''; // Стил за предизвикване на оцветяване
        if ( $team ){
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
              if($lk) $lk = '<br><span class="message">'.translate('conference_Missing')."$lk</span>\n";
              else {
                $stl .= ' style="background-color: lightgreen; padding: 0.5em;';
                $rc++;
              }
           }
           $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
           $lk .= '<br><a href="'.$edp.'&proc='.$d['ID'].'#bottom">'.encode('Редактиране')."</a> \n";
           $lk .= encode(' Съобщаване за: ').emailsend('select|&uid='.$d['user_id'].'&proc='.$d['ID'])."\n";
           if($cr<=0)
              $lk .= $cr.' <a href="#" style="font-weight:bold;color:red;" onclick="deleteAbstract('.$d['ID'].');return false;">x</a>';
           if($d['fulltextfile4'])
              $lk .= "<br>".encode('Рецензенти: ')."<a href=\"/index.php?pid=79&proc=".$d['ID'].'" target="_blank">'.
                     db_table_field('COUNT(*)', 'reviewer_work', "`proc_id`=".$d['ID']).
                     "</a>, <a href=\"$crp"."assign_reviewer.php?proc=".$d['ID'].'" target="_blank">'.
                     encode('назначаване')."</a>\n";
              $rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision` IS NOT NULL');
              if($rwc) $lk .= ", ".encode('попълнили').": $rwc";
              $rwc = db_table_field('COUNT(*)', 'reviewer_work', '`proc_id`='.$d['ID'].' AND `decision`>=1');
              if($rwc) $lk .= ", ".encode('одобрили').": $rwc";
           if($d['abstracttextfile'])  $lk .= '<br>Abstract 2: '.file_link_and_size($d['abstracttextfile']);
           if($d['fulltextfile'])  { $lk .= '<br>Doc: '.file_link_and_size($d['fulltextfile']);
              $zip_command .= $d['fulltextfile'].' '; $doc++; }
           if($d['fulltextfile2']) { $lk .= '<br>Pdf: '.file_link_and_size($d['fulltextfile2']); $pdf++; }
           $nm = $d['language'].
                 ', ID:<a href="'.$adm_pth.'/edit_record.php?t=proceedings&r='.$d['ID'].'" target="_blank">'.$d['ID'].
                 "</a>, place:".$d['place'].", form:".$d['form'].", ";
           if($d['approved_a']) conference_add_auth($auth, $d['authors']);
        } // if ( $team )
        if( $team || !empty($d['title']) || $preview ) {
           if($d['keylec']){
             if(!$stl) $stl .= ' style="';
             $stl .= 'border-right:solid 4px green;';
           }
           if($stl) $st .= $stl.'"';
           $lr .= "<p$st>$nm";
           if(!empty($d['approved_a']) || $preview){
             if( $team ) $lr .= "vol:".$d['vol'].", <br>";
             if($d['fulltextfile2']){
               $cn[$d['vol']]++;
//               $lr .= ($i+1)."-".$cn[$d['vol']].". ";
             }
           }
       if($utype!='vsu2020'){
           // PDF с пълния текст
           if($d['fulltextfile2'])
               $lr .= '<a href="/_pdfjs-2.2.228-dist/web/viewer.html?file='.
                       $fdir.'/'.$d['fulltextfile2'].'" title="'.translate('conference_dfull', false).'">'.$pdf.'</a> ';
           // PDF с презентация
           if($d['fulltextfile3'])
               $lr .= '<a href="/_pdfjs-2.2.228-dist/web/viewer.html?file='.
                       $fdir.'/'.$d['fulltextfile3'].'" title="'.translate('conference_prez', false).'">'.$ppt.'</a> ';
       }
           if($s_auth) { $c2++; $lr .= ($i+1)."-".$c2.". "; }
           $lr .= "<ptitle>".mb_strtoupper(stripslashes($d['title']))."</ptitle><br>\n";
           if( $team || ($utype<'vsu2020') || $s_auth)
               $lr .= '<author>'.conference_only_names($d['authors'])."</author>";
           if($d['pages']){
//             $lr .= " &nbsp; &nbsp; ".translate('conference_pg').$pn[$d['vol']];
             $pn[$d['vol']] += $d['pages'];
//             if($d['pages']>1) $lr .= "-".($pn[$d['vol']]-1);
//             if ( $team ) $lr .= ' ('.$d['pages'].')';
           }
           if(($team || (isset($_GET['text']) && ($_GET['text']=='anonimous')))
               && $d['fulltextfile4']) $lk .= '<br>Anonimouse: '.file_link_and_size($d['fulltextfile4']);
           $lr .= "$lk</p>\n";
        }
     } // Край на цикъла по доклади
     $sr .= $lr;
   }
   if( $team || !empty($c) ){
     $rz .= '<h3>'.$tp[$i];
     if ( $team || $s_auth) $rz .= " - $c $doc $pdf";
     $rz .= "</h3>\n";
//     if ( $team ) $rz .= "<p>$zip_command </p>\n";
     if (("$td">="$t3") || $team || $preview ) $rz .= $sr;
     $tc += $c;
     $docs += $doc;
     $pdfs += $pdf;
   }
} // Край на цикъла по научни направления

} // Край на цикъла по томове

if ( $team ) $rz = "<p>".count($auth)." authors, $tc abstracts, $docs doc, $pdfs pdf, $rc ready. Sort: $olink</p>\n".$rz;
return '<div id="conference_abstracts">'."\n".$rz."</div>\n";
}

function conference_only_names($a){
return preg_replace('/\d/', '', strip_tags($a));
}

// Добавяне на разделените със запетаи имена $n в масив $auth
// Ключове на масива са имената, а стойности, броя на изпращанията за добавяне на всяко име

function conference_add_auth(&$auth, $n){
$ns = explode(',', $n);
foreach($ns as $m) if (!isset($auth[trim($m)])) $auth[trim($m)] = 1; else $auth[trim($m)]++;
}

// Преименуване на файловете

function conference_rename_files($d){
global $fdir, $utype;
foreach(array('fulltextfile','fulltextfile2') as $f) if ($d[$f]) {
  $n  = 'proc'.$d['ID'].'.'.strtolower(pathinfo($d[$f], PATHINFO_EXTENSION));
  if($n==$d[$f]) continue;
  $nn = $_SERVER['DOCUMENT_ROOT'].$fdir.$n;
  $on = $_SERVER['DOCUMENT_ROOT'].$fdir.$d[$f];
  $r = file_exists($on) && rename($on, $nn);
  if($r){
    if($d['fee']=='') unset($d['fee']);
    $d[$f] = $n;
    db_update_record($d, 'proceedings');
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

function conference_abstract_book($uid, $adm){
if(isset($_GET['proc']) && is_numeric($_GET['proc'])) return conference_1abstract($uid, $adm);
global $utype, $main_index, $page_id, $day4, $fdir;
// Научни направления $tp
eval(translate('conference_topics_'.$utype,false));
$rz = '';
// Начини за подреждане
//$order = ' ORDER BY  `keylec` DESC, `title` ASC'; // Азбучен ред на заглавията - първи пленарните доклади
$order = ' ORDER BY  `keylec` DESC, `authors` ASC'; // Азбучен ред на малкото име на адтора - първи пленарните доклади
// Начини за филтриране
// $access = ' AND `approved_a`'; // Доклади с одобрени резюмета
$access = ' AND `fulltextfile2`>\' \''; // Доклади с пълен текст в PDF
$filter = '';
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
$acb = stored_value('conference_aBookAccess');
$aca = stored_value('conference_aRev1Access');
if($acb && isset($_GET['ac']) && ($_GET['ac']==$acb)) $adm = true;
// Дата за обявяване на приетите пълни доклади
$t4 = '2020-08-28 23:59:59';
if(is_array($t4)) $t4 = db_table_field('date_time_2', 'schedules', "`sch_name`='".$day4[0]."' AND `ev_name`='".$day4[1]."'");
// Настоящия момент
$td = date('Y-m-d H:i:s');
if($adm){
  $editp = stored_value('conference_editpaper');
  $revp = stored_value('conference_reviewpage');
  if(!$revp) die('"conference_reviewpage" option not set');
//  $access = '';
  $c = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `title` > ' ' AND `topic`>-1$filter");
  $rz .= "<p>Proceeding count: $c";
  $rz .= " &nbsp; Access by <a href=\"$main_index?pid=$page_id&ac=$acb$fp\">Link</a>";
  $rz .= "</p>\n";
}
else if($td<$t4) return translate('conference_contentAfter').db2user_date_time($t4).$rz;
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
        $access.$filter.$order );
  if(count($da)) $rz .= '<h2>'.$tp[$i]."</h2>\n";
  // За всеки доклад
  foreach($da as $d){
       $count++;
       $rz .= '<div class="who">'."\n";
       $rz .= '<h3 id="pof'.$d['ID'].'">'.($i+1)."-$count. ".mb_strtoupper(stripslashes($d['title']))."</h3>\n";
       if(!empty($d['authors'])&&($d['authors'][0]!='<'))
          $rz .= '<p>'.$d['authors']."</p>\n";
       else
          $rz .= $d['authors'];
       $rz .= $d['abstract']."\n";
       if($d['keywords'])
          $rz .= '<p class="keywords"><span>'.translate_to('conference_ckeywords',$d['language']).'</span> '.
                 conference_formatKeyWorts($d['keywords'])."</p>\n";
          if(!empty($d['addresses'])&&($d['addresses'][0]!='<'))
             $rz .= '<p>'.stripslashes($d['addresses'])."</p>";
          else
             $rz .= stripslashes($d['addresses']);
       if($adm){
         // Линк "Редактиране"
         $rz .= '<p><a href="'.$editp.'&proc='.$d['ID'].'">'.translate('conference_editpaper')."</a></p>\n";
         if (!$d['approved_a'])
            // Линк "Рецензиране на резюмето"
            $rz .= '<p><a href="'.$revp.'&rev1='.$d['ID'].'&ac='.$aca.'">'.translate('conference_rev1')."</a></p>\n";
         else ;
            // Линк "Съобщаване прието резюме"
//            $rz .= '<p><a href="/index.php?pid=52&uid='.$d['user_id'].'&tid=7&proc='.$d['ID'].'" target="_blank">'.
//                   translate('conference_messAppr')."</a></p>\n";
         // Линк "Рецензиране на доклада"
//         if ($d['approved_a'] && $d['fulltextfile2'])
//            $rz .= '<p><a href="'.$revp.'&rev2='.$d['ID'].'&ac='.$aca.'">'.translate('conference_rev2')."</a></p>\n";
       }
       if($d['fulltextfile2']) $rz .= '<p><a href="'.$fdir.$d['fulltextfile2'].
                                      '">'.translate_to('conference_fulltext',$d['language'])."</a></p>\n";
       if( ($d['language']=='bg') && ($d['topic']<8) ) $rz .= translate_to('conference_abstract_'.$d['ID'],'en');
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
    return '<p class="message">'."Access denied.</p>\n";
if(count($_POST)) return conference_rev1process();
global $utype, $user_table;
if(!is_numeric($_GET['rev1'])) die("Incorrect rev1 parameter.");
$rz = '<h2>'.translate('conference_rev1title')."</h2>\n";
// Данни за доклада
$d = db_select_1('*', 'proceedings', "`ID`=".$_GET['rev1']." AND `utype`='$utype'");
if(!$d) return '<p class="message">'."Incorrect report ID.</p>\n";
// Данни на влезлия потребител
$ud = db_select_1('*', $user_table, "`ID`=$uid AND `type`='$utype'");
//if(!$ud) return '<p class="message">'."Incorrect loggedin user.</p>\n";
// Тематични направления
eval(translate('conference_topics_'.$utype,false));
$rz .= '<p>'.translate('conference_ctopic').' '.$tp[$d['topic']]."</p>\n";
$rz .= '<p>'.translate('conference_ctitle').' '.$d['title']."</p>\n";
$rz .= '<p>'.translate('conference_cabstract')."</p>\n".$d['abstract']."\n";
$rz .= '<p>'.translate('conference_ckeywords').' '.$d['keywords']."</p>\n";
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
  return '<p class="message">'.translate('conference_rev1sent'). "</p>\n";
else
  return '<p class="message">'.translate('conference_rev1notSent'). "</p>\n";
}

// Показване на едно резюме с цел преглеждане от участника или други

function conference_1abstract($uid, $adm){
global $utype;
// Данни за доклада
$pd = db_select_1('*', 'proceedings', '`ID`='.$_GET['proc']);
// Код за достъп
$aca = stored_value('conference_aAbsAccess');
if(!( ($uid==$pd['user_id']) || $adm ||
      (isset($_GET['ac']) && $aca && ($aca==$_GET['ac']))
    )) return 'Access not allowed.';
// Тематични направления
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
if($pd['keywords']) $rz .= $pd['keywords'];
$rz .= "</p>\n";
if(!empty($pd['addresses'])&&($pd['addresses'][0]!='<'))
   $rz .= '<p>'.stripslashes($pd['addresses'])."</p>";
else
   $rz .= stripslashes($pd['addresses']);
return $rz;
}

// Показване на формуляр за рецензиране на доклад

function conference_rev2($uid, $adm){
// Преглед на рецензентите
if($_GET['rev2']=='on') return conference_revList();
global $utype, $user_table, $body_adds, $language;
$body_adds .= ' onload="CKEDITOR.replace(\'comment1\');'.
                        'CKEDITOR.replace(\'comment2\');'.
                        'CKEDITOR.replace(\'comment3\');"';
$ms = '';
// Обработка на изпратени данни
if(count($_POST)) $ms .= conference_rev2process();
if(!is_numeric($_GET['rev2'])) return '<p class="message">'."Incorrect proceeding ID.</p>\n";
// Данни за доклада
if($_GET['rev2']=='0')
  $d = array('ID'=>0, 'topic'=>0, 'title'=>'Title of the report', 'abstract'=>'Hiere you will see the abstract',
             'keywords'=>'A list of keywords.', 'fulltextfile4'=>'<a href="">Link to the file</a>');
else {
  $d = db_select_1('*', 'proceedings', "`ID`=".$_GET['rev2']." AND `utype`='$utype'");
  if(!$d) return '<p class="message">'."Incorrect report ID.</p>\n";
  $d['fulltextfile4'] = file_link_and_size($d['fulltextfile4']);
}
// Рецензентски номер в тази секция на потребителя
$rid = db_table_field('ID', 'reviewers', "`utype`='$utype' AND `user_id`=$uid AND `topic`=".$d['topic'],'',false);
//echo "$rid<br>";
if(!$rid && ($_GET['rev2']>0)) return '<p class="message">'."Your are not a reviewer of this conference topic.</p>\n";
// Данни за рецензията
if($_GET['rev2']=='0')
  $rd = array('ID'=>0, 'date_time_1'=>'', 'date_time_2'=>'', 'rev_id'=>0, 'proc_id'=>0,
              'grade1'=>0, 'grade2'=>0, 'grade3'=>0, 'grade4'=>0, 'grade5'=>0,
              'grade6'=>0, 'grade7'=>0, 'grade8'=>0, 'grade9'=>0, 'grade10'=>0,
              'grade11'=>0, 'grade12'=>0, 'decision'=>'', 'comment1'=>'', 'comment2'=>'', 'comment3'=>'');
else
  $rd = db_select_1('*', 'reviewer_work', "`rev_id`=$rid AND `proc_id`=".$_GET['rev2'],false);
if(!$rd) return '<p class="message">'."Your are not a reviewer of this article.</p>\n";
$rz = '<h2>'.translate('conference_rev2title')."</h2>\n";
// Данни на влезлия потребител
$ud = db_select_1('*', $user_table, "`ID`=$uid AND `type`='$utype'");
//if(!$ud) return '<p class="message">'."Incorrect loggedin user.</p>\n";
// Тематични направления
eval(translate('conference_topics_'.$utype,false));
$rz .= '<p>'.translate('conference_ctopic').' '.$tp[$d['topic']]."</p>\n";
$rz .= '<p>'.translate('conference_ctitle').' '.$d['title']."</p>\n";
//$rz .= '<p>'.translate('conference_cabstract')."</p>\n".$d['abstract']."\n";
//$rz .= '<p>'.translate('conference_ckeywords').' '.$d['keywords']."</p>\n";
$rz .= '<p>'.translate('conference_cfulltextfile4').' '.$d['fulltextfile4']."</p>\n";
$rz .= '<h2>'.translate('conference_rev1your')."</h2>\n";
$rz .= '<p>'.uploadfile('revForm_'.$utype)."</p>\n";
$gr = array( translate('conference_notApplicable',false), '1', '2', '3' );
$ds = array(
  translate('conference_reject', false),
  translate('conference_acceptIf', false),
  translate('conference_accept', false)
);
$f = new HTMLForm('conference_rev2form');
foreach($rd as $k=>$v) switch ($k){
case 'ID': case 'date_time_1': case'date_time_2': case 'rev_id': case 'proc_id':
           $f->add_input( new FormInput('', $k, 'hidden', $v) );
           break;
case 'grade1': case 'grade2': case 'grade3': case 'grade4': case 'grade5': case 'grade6':
case 'grade7': case 'grade8': case 'grade9': case 'grade10': case 'grade11': case 'grade12':
           $fi = new FormSelect(translate("onference_$k"), $k, $gr, $v);
           $fi->values='k';
           $f->add_input( $fi );
           break;
case 'decision':
           $fi = new FormSelect(translate("onference_$k"), $k, $ds, $v);
           $fi->values='k';
           $f->add_input( $fi );
           break;
case 'comment1': case 'comment2': case 'comment3':
           $fi = new FormTextArea(translate("onference_$k"), $k, 100, 10, $v);
           $fi->ckbutton = '';
           $f->add_input( $fi );
           break;
default:
  $f->add_input( new FormInput(translate("onference_$k"), $k, 'text', $v) );
}
$f->add_input( new FormInput('', '', 'submit', translate('conference_rev1submit') ) );

$rz .= $f->html();
return $ms.$rz;
}

// Запазване на изпратена рецензия

function conference_rev2process(){
//die(print_r($_POST,true));
$_POST['date_time_2'] = 'NOW()';
if(db_update_record($_POST, 'reviewer_work')) return '<p class="message">'.translate('dataSaved')."</p>\n";
}

// Показване списък на рецензентите

function conference_revList(){
global $utype, $pedit, $adm_pth;
// Тематични направления
eval(translate('conference_topics_'.$utype,false));

// Брой одобрени резюмета на български
//$tb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `language`='bg'",0);
// Брой одобрени резюмета на английски
//$te = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `language`='en'",0);

// Брой одобрени пълни текстове на доклади на български
$tb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `fulltextfile2`>' ' AND `language`='bg'",0);
// Брой одобрени пълни текстове на доклади на английски
$te = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `approved_a`=1 AND `fulltextfile2`>' ' AND `language`='en'",0);

// Брой на всички рецензенти
$rt = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype'");
// Брой на рецензенти на български
$rb = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `languages` LIKE '%bg%'");
// Брой на английски
$re = db_table_field('COUNT(*)', 'reviewers', "`utype`='$utype' AND `languages` LIKE '%en%'");

$rz = '<script>
function confirmRevDel(e){
if(confirm("Confirm that you want to delete reviewer")) document.location = e;
}
</script>
<h2>'.translate('conference_reviewers').conference_revCounts(intval($tb)+intval($te),$rt,$tb,$rb,$te,$re).'</h2>
<p><a href="'.unset_self_query_var('rev2').'">'.translate('conference_partList')."</a></p>\n";
foreach($tp as $i=>$t){
  // Данни на рецензентите по направление с номер $i
  $q = '';
  if(in_edit_mode()) $q = 'a.ID as rid, ';
  $dt = db_select_m($q.'b.ID, b.position, b.firstname, b.secondname, b.thirdname, a.languages, a.confirmed',
      '`reviewers` AS a RIGHT JOIN `users` AS b ON a.user_id=b.ID',
      "a.utype='$utype' AND a.topic=$i ORDER BY b.thirdname ASC");

    foreach($dt as $j=>$d){
       if(in_edit_mode()){
          $dt[$j]['rid']= '<a href="'.$adm_pth.'delete_record.php?t=reviewers&r='.$d['rid'].
                       '" style="color:red;" title="Delete reviewer" onclick="confirmRevDel(this);return false;">'.$d['rid'].'</a> ';
          $dt[$j]['ID'] = '<a href="'.$pedit.'&uid='.$d['ID'].'" target="_blank" title="Edit user\'s record">*</a> '.
                       '<a href="'.current_pth(__FILE__).'make_reviewer.php?uid='.$d['ID'].
                       '" target="_blank" title="Edit reviewer\'s record">'.$d['ID'].'</a>';
       }
       $uids = db_select_m('ID', 'reviewers', "`utype`='$utype' AND `user_id`=".$d['ID'], false);
       $q = '';
       foreach($uids as $id){
         if($q) $q .= ' OR ';
         $q .= '`rev_id`='.$id['ID'];
       }
       $dt[$j]['count'] = db_table_field('COUNT(*)', 'reviewer_work', "$q");
    }
  // Брой доклади на български
  $cb = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `topic`=$i AND `language`='bg'");
  // Брой доклади на английски
  $ce = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `topic`=$i AND `language`='en'");
  // Брой рецензенти на вългарски
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

// Показване на рецензиите на доклад

function conference_procRevs(){
global $utype, $adm_pth;
if(empty($_GET['proc']) || !is_numeric($_GET['proc'])) return '<p class="message">Missing or incorrect "proc" parameter.</p>'."\n";
$rws = db_select_m('*', 'reviewer_work', "`proc_id`=".$_GET['proc'],false);
$rz = '';
foreach($rws as $rw){
  $rz .= view_record($rw);
  if(in_edit_mode()) $rz .= '<a href="'.$adm_pth.'edit_record.php?t=reviewer_work&r='.$rw['ID'].'" target="_blank">*</a> ';
}
return $rz;
}


?>

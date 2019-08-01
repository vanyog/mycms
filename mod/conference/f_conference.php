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

// ����� �� ������ �����������

include_once($idir.'lib/f_mod_path.php');
include_once(mod_path('userreg'));
include_once(mod_path('schedules'));
include_once($idir.'lib/f_view_record.php');
include_once($idir.'lib/f_db_insert_1.php');
include_once($idir.'lib/f_db_update_where.php');
include_once($idir.'lib/f_view_table.php');
include_once($idir.'mod/usermenu/f_usermenu.php');

global $user_table, $utype, $fdir, $day1, $day2, $day3, $adm_pth, $page_header, $proccount, $plogin, $pedit;

// ������� � ����� �� �������������
$user_table = stored_value('user_table', 'users');
// ��� �� �������������
$utype = stored_value('conference_usertype', 'vsu2014');
// ����� �� ���������� �� �������
$plogin = stored_value('userreg_login_'.$utype);
// ����� �� ���������� �� ����������� �� ������� �����
$pedit = str_replace('&user2=login', '&user2=edit', $plogin);

// ���������� �� ������� �� �������
$fdir = stored_value('conference_files', '/conference/2014/files/');
// ��� �� ������� � ��� �� ������ �� ������� 'schedules' �� ����� �� ������� �� ��������
$day1 = explode(',', stored_value('conference_day1event','schedule_event_2,schedule_1'));
// ��� �� ������� � ��� �� ������ �� ������� 'schedules' �� ����� �� ������� �� ������ ����� �� ���������
$day2 = explode(',', stored_value('conference_day2event','schedule_event_4,schedule_1'));
// ��� �� ������� � ��� �� ������ �� ������� 'schedules' �� ����� �� ��������� �� �������� ��������
$day3 = explode(',', stored_value('conference_day3event','schedule_event_91,schedule_89'));
// ���� ��������� �������
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

// ������ ������� �� ������
// ��� �������� ��������� �� ���������� $a �������:
// '' - ����� �������� �� ����������� � �������� ����� ����� � ����� �� ��������� �������
// 'admin' - �������� �� �������������� ��� ������ �� ������ ��������� � ������� �������
// 'edit' - ����� �� ����������� ������� �� ������
// 'stats' - ������ ����������
// 'abstracts' - ������ ��������
// 'participants' - ���������� �� ����������� �� �����������

function conference($a = ''){

global $user_table, $can_manage, $utype, $day1, $day2, $proccount, $pth, $plogin, $pedit;

// ����� �� ������� ����������
$uid = userreg_id($utype);

// ���� ������������ � �������������
$adm = isset($can_manage['conference']) && $can_manage['conference'];

switch ($a){
case ''            : break;
case 'admin'       : return conference_admin($uid);
case 'edit'        : return conference_edit($uid);
case 'stats'       : return conference_stats();
case 'abstract_t'  : return conference_abstract_titles();
case 'abstracts'   : return conference_abstract_book();
case 'participants': return conference_participants();
default            : return '<p class="message">'."Unknown parameter value '$a' in 'conference() function.</p>";
}

// ����� ����� �� �����������
$d = db_select_1('*', $user_table, "`ID`=$uid");
$cp = array(
'username'=>translate('user_username'),
'email'=>translate('user_email'),
'firstname'=>translate('user_firstname'),
'secondname'=>translate('user_secondname'),
'thirdname'=>translate('user_thirdname'),
'country'=>translate('user_country'),
'institution'=>translate('user_institution'),
'address'=>translate('user_address'),
'telephone'=>translate('user_telephone')
);

$rz = '';
// ����� �� ��������� �� �����������
$pd = db_select_m('*', 'proceedings', "`user_id`=$uid AND `utype`='$utype'");
// ��������� �� ����������� ����
if(count($pd)>$proccount) $rz .= '<p class="message">'.translate('conference_manyreps')."</p>\n";

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
'fulltextfile'=>translate('conference_cfulltextfile'),
'fulltextfile2'=>translate('conference_cfulltextfile2')
);

// ��������� ������� �� ����� ������
$rz .= '<h2>'.translate('conference_mypaper').'</h2>
<h3>'.translate('conference_1paper')."</h3>\n";
// ���� e ������ �� �����������
$et = schedules_in_event($day1[0],$day1[1]) || $adm;
if (!$et && !count($pd)) return $rz.'<p class="message">'.translate('conference_noabs1')."</p>\n";
// ���� � ������ �� ������� �� �������
$ut = schedules_in_event($day2[0],$day2[1]) || $adm;
// ����� �� ���������� �� ����������� �� ������ � ������� �� ������ �� ��������
$edp = stored_value('conference_editpaper', '/index.php?pid=1068');
if ($ut) $rz .= '<p><a href="'.$edp.$did.'">'.translate('conference_editpaper').'</a>';
if (count($pd)>0) $rz .= ' &nbsp; <a href="'.current_pth(__FILE__).'delete_paper.php?a=1'.$did.
                         '" onclick="confDelPaper(this);return false;">'.
                         translate('conference_deletepaper').'</a>';
$rz .= "</p>\n";
$rz .= view_record($d1, $cp).'
<p>&nbsp;</p>
'.translate('conference_feenote').'
<p>&nbsp;</p>
';

// ��� ��� ������� ���� ������ - ��������� ������� � �� ����� ������
if (!$et && (count($pd)<$proccount)) return $rz.'<p class="message">'.translate('conference_noabs1').'</p>';
if ((count($pd)>0) && $di2) {
  $rz .= '<h3>'.translate('conference_2paper').'</h3>';
  if ($ut) $rz .= '<p><a href="'.$edp.$di2.'">'.translate('conference_editpaper')."</a>\n".
                  ' &nbsp; <a href="'.current_pth(__FILE__).'delete_paper.php?a=1'.$di2.
                  '" onclick="confDelPaper(this);return false;">'.
                  translate('conference_deletepaper').'</a></p>';
  $rz .= view_record($d2, $cp);
}
$rz .= '
<p>&nbsp;</p>';

return $rz;
}

//
// ��������� � ���� �� ������� � ����������� �����������, ������� ��� ��������� � ������ �����
// � ��. ������� ��� ������� ����� ����������� ��

function conference_trprec($d){
// ����� �� ���������
eval(translate('conference_forms'));
// ��������� �����������
eval(translate('conference_topics'));
$d['form'] = $fs[$d['form']];
$d['topic'] = $tp[$d['topic']];
$d['title'] = '<strong>'.$d['title'].'</strong>';
$d['fulltextfile' ] = file_link_and_size($d['fulltextfile' ]);
$d['fulltextfile2'] = file_link_and_size($d['fulltextfile2']);
return $d;
}

//
// ����������� � �������� �� �����

function file_link_and_size($fn){
global $fdir;
if (!$fn) return '';
// ��������� ��� ��� �����
$af = $_SERVER['DOCUMENT_ROOT'].$fdir.$fn;
// �������� �� �����
if (file_exists($af)) $sz = filesize($af); else return "$fn - <span class=\"message\">".translate('conference_filenotexists')."</span>";
if (!is_local()) $fl = rawurlencode($fn); else $fl = $fn;
return "<a href=\"$fdir$fl\">$fn</a> - $sz bytes";
}

//
// ����� �� ����������� �� ������� �� ������

function conference_edit($uid){
global $body_adds, $can_manage, $day1, $day2, $debug_mode, $fdir, $user_table;
usermenu(true);
//if(!empty($debug_mode)) print_r($GLOBALS);
// ���� �� ��������� �� �������������
$adm = isset($can_manage['conference']) && ($can_manage['conference']==1);
// ���� ��� � ������ �� ������� �� ������ �����
$ut = schedules_in_event($day2[0], $day2[1]) || $adm;
if (!$ut) return '<p class="message">'.translate('conference_nofull')."</p>";
if (count($_POST)) return conference_pprocess($uid);
if (isset($_SERVER['HTTP_REFERER'])) $_SESSION['conference_returnpage'] = $_SERVER['HTTP_REFERER'];
else $_SESSION['conference_returnpage'] = stored_value('conference_editpage', '/index.php?pid=1074');
global $languages, $language;
// ������� ��������� �� ��������
$d = array(
 'user_id'=>$uid,
 'form'=>0,
 'topic'=>0,
 'fee'=>0,
 'currency'=>'BGN',
 'approved_a'=>0,
 'approved_f'=>0,
 'language'=>$language,
 'title'=>'',
 'authors'=>'',
 'addresses'=>'',
 'keywords'=>'',
 'abstract'=>'',
 'fulltextfile'=>'',
 'fulltextfile2'=>'',
 'fulltextfile3'=>'',
 'vol'=>1,
 'pages'=>0,
 'place'=>0
);
// ��� ��� ����� �� �����, ������ �� ������� 'proceedings'.
if (isset($_GET['proc'])){
  $d = db_select_1('*', 'proceedings', "`ID`=".(1*$_GET['proc']) );
}
if ( !( ($d['user_id']==$uid) || $adm ) )
   return '<p class="message">'.translate('conference_cnnotedit').'</p>';
// ��� �� ���������
$ud = db_select_1('*', $user_table, "`ID`=".$d['user_id']);
$un = $ud['firstname']." ".$ud['secondname']."  ".$ud['thirdname'];
if($adm) $un = '<a href="'.stored_value('conference_admin','/index.php?pid=1358').'#pof'.$d['user_id']."\">$un</a>";
//die(print_r($ud,true));
$f = new HTMLForm('conference_peform',true,false);
$f->add_input( new FormInput('', 'user_id', 'hidden', $d['user_id']) ); 
// ����� �� ���������
eval(translate('conference_forms'));
// ��������� �����������
eval(translate('conference_topics'));
// ��� �� ��������� �� ������������� - ���� �� ������� ����� � ������ �� ����������
if ($adm) {
  $ti = new FormCurrencyInput(encode('�����:'), 'fee', 'currency', $d['fee'], $d['currency']);
  $f->add_input($ti);
  $ti = new FormInput(encode('�������� ������:'), 'approved_a', 'checkbox');
  if ($d['approved_a']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('������� ����� �����:'), 'approved_f', 'checkbox');
  if ($d['approved_f']) $ti->checked = ' checked';
  $f->add_input($ti);
  $ti = new FormInput(encode('���:'), 'vol', 'text', $d['vol']);
  $f->add_input($ti);
  $ti = new FormInput(encode('�����:'), 'place', 'text', $d['place']);
  $f->add_input($ti);
}
// ���� ��� � ������ �� �����������
$et = schedules_in_event($day1[0], $day1[1]) || $adm;
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
$fi->ckbutton = '';
$f->add_input($fi);
$fi = new FormTextArea( translate('conference_caddresses'), 'addresses', 63, 5, stripslashes($d['addresses']) );
if (!$et) $fi->js = ' disabled="disabled"';
$fi->ckbutton = '';
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
$ti = new FormInput(encode('���� ��������:'), 'pages', 'text', $d['pages']);
$f->add_input($ti);
$fl = new FormInput(translate('conference_cfulltextfile'), 'fulltextfile', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile']));
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile2'), 'fulltextfile2', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile2']));
$fl->size = 63;
$f->add_input($fl);
$fl = new FormInput(translate('conference_cfulltextfile3'), 'fulltextfile3', 'file', $_SERVER['DOCUMENT_ROOT'].$fdir.stripslashes($d['fulltextfile3']));
$fl->size = 63;
$f->add_input($fl);
$f->add_input( new FormInput('', '', 'submit', translate('conference_csubmit') ) );
// ���������, �� ����� ������ �� ���������
$ms = '';
if (!$et) $ms = '<p class="message">'.translate('conference_noabs')."</p>";
return $ms."\n<p>$un</p>\n".$f->html().'
<p>&nbsp;</p>';
}

//
// ����������� �� ���������� ����� �� ������

function conference_pprocess($uid){
global $language, $can_manage, $utype;
// ����� �� ������
$d = array(
'user_id'=>(1*$_POST['user_id']),
'utype'=>$utype,
'date_time_2'=>'NOW()'
);
if (isset($_POST['approved_a'])) $d['approved_a']=1; else $d['approved_a']=0;
if (isset($_POST['approved_f'])) $d['approved_f']=1; else $d['approved_f']=0;
if (isset($_POST['language'])) $d['language']=addslashes($_POST['language']);
if (isset($_POST['form'])) $d['form']=addslashes($_POST['form']);
if (isset($_POST['topic'])) $d['topic']=addslashes($_POST['topic']);
if (isset($_POST['title'])) $d['title']=addslashes($_POST['title']);
if (isset($_POST['authors'])) $d['authors']=addslashes($_POST['authors']);
if (isset($_POST['addresses'])) $d['addresses']=addslashes($_POST['addresses']);
if (isset($_POST['keywords'])) $d['keywords']=addslashes($_POST['keywords']);
if (isset($_POST['abstract'])) $d['abstract']=addslashes($_POST['abstract']);
if (isset($_POST['vol'])) $d['vol']=addslashes($_POST['vol']);
if (isset($_POST['pages'])) $d['pages']=(1*$_POST['pages']);
if (isset($_POST['place'])) $d['place']=(1*$_POST['place']);
// ����� �� ������� �����
$d2 = array(); 
if (isset($_POST['fee'])) $d2['fee']=addslashes($_POST['fee']);
if (isset($_POST['currency'])) $d2['currency']=addslashes($_POST['currency']);
// ������� �� doc ����, ��� � ��������
$r = conference_upload('fulltextfile',$uid);
$ms = $r[0];
if ($r[1]) $d['fulltextfile' ] = $r[1];
// ������� �� pdf ����, ��� � ��������
$r = conference_upload('fulltextfile2',$uid);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile2'] = $r[1];
// ������� �� ���� � �����������, ��� � ��������
$r = conference_upload('fulltextfile3',$uid);
$ms .= $r[0];
if ($r[1]) $d['fulltextfile3'] = $r[1];
// ��� � �������� ����� �� ������ � $_GET['proc']
// �� ������������ ������� �� ������� � ���� �����
if (isset($_GET['proc'])){
  $d['ID'] = 1*$_GET['proc'];
  db_update_record($d, 'proceedings');
}
// ����� �� ������� ����� �� ��� ������
else{
  $d['date_time_1'] = 'NOW()';
  $i = db_table_field('COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `user_id`=".$d['user_id']);
  if ($i<2) db_insert_1($d, 'proceedings');
  else $ms = translate('conference_toomany')."<br>\n";
}
// ��� �� ��������� ����� �� ������� �����
// ������� �� ������� �� ������ ������� �� ���������
if ((count($d2)==2) && $d2['fee']) db_update_where($d2, 'proceedings', "`utype`='$utype' AND `user_id`=".$d['user_id']);
// ��� ����������� �� ������������� �� ������� ���������� �� ����� � �������� �������
if (isset($can_manage['conference']) && $can_manage['conference']){
   header('Location: '.$_SESSION['conference_returnpage'].'#pof'.$d['ID']);
   die;
}
return '<p class="message">'.$ms.translate('dataSaved').'</p>';
}

//
// ������� �� ���� �� $_POST[$fn]
// ����� �������� ����� � ����� ������� - ���������
// � ����� ������� - ��� �� ������� ����

function conference_upload($fn,$uid){
global $fdir, $utype;
$fl = $_FILES[$fn];
$ms = ''; $n = '';
if (!$fl['error']){
  // �������� ���� ���� ���������� �� � ����� ���� ��� ������ ���
  $n = db_table_field($fn, 'proceedings',
       "`$fn`='".$fl['name']."' AND `user_id`<>$uid AND `utype`='$utype'", '');
  if ($n) $ms = translate('conference_fileagain')."<br>\n";
  else {
    // ��� �� ����� ���� �� ����������� ���� ����
    $of = db_table_field($fn, 'proceedings', "`user_id`=$uid AND `utype`='$utype'");
//    echo "$of<br>".$fl['name'];
    // ��� ����� �� ������ ���� � �������� �� ����� �� �����, ������� ���� �� �������
    if ( $of && ($of!=$fl['name']) ){
       $ofn = $_SERVER['DOCUMENT_ROOT'].$fdir.$of;
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
// �������������� �� �������������� �����������

function conference_admin($uid){
global $can_manage, $utype, $page_header, $can_visit, $user_table, $proccount, $pedit;
usermenu(true);
$is_editor = isset($can_manage['conference']) && ($can_manage['conference']==1);
if ( empty($can_manage['conference']) )
   return "<p class=\"message\">You have no permission to view this information.</p>";
// ��������� �� ���������
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
// Javascript, ����� �� ��������� ��� �������� �� ������ �� ���������
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
// ������ ����� �� �������������
$ud = db_select_m('*', $user_table, "`type`='$utype' AND `username`>''$q");
// ������ ������� �� �������
$pd = db_select_m('*', 'proceedings', "`utype`='$utype' ORDER BY `user_id`");
// ����� � ����� ����� �� ����������� � ������� id-���� ��
$u = array();
// ����� � ���� �� ��������� ������� �� ����� ���������� � ������� id-���� ��
$up = array();
foreach($pd as $d) if (isset($up[$d['user_id']])) $up[$d['user_id']]++; else $up[$d['user_id']]=1;

$rz = encode('<p>������������ ����������� - ').count($ud).encode(', �������� - ').count($pd).
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
<th>'.encode('����<br>�������').'</th>
</tr>
';
// ����� �� ������� �� ��������� �� ��� ������
$lk = current_pth(__FILE__).'new_proceeding.php';
// ����� �� ���������� �� ����������� ������� �� ��������
$ep = stored_value('userreg_edit_'.$utype, $pedit);
foreach($ud as $d){
  $u[$d['ID']]=$d['firstname'].' '.$d['secondname'].' '.$d['thirdname'];
  $rz .= '<tr>
<td id="us'.$d['ID'].'">
<a href="'.$ep.'&amp;uid='.$d['ID'].'">'.$d['ID'].'</a>';
  if(isset($can_manage['userreg']) && ($can_manage['userreg']==1))
     $rz .= "\n<a href=\"".current_pth(mod_path('userreg')).'login_as.php?uid='.$d['ID'].'">></a>';
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
<textarea id="textForClpibd"></textarea>';
// ����� �� ���������
eval(translate('conference_forms'));
// ��������� �����������
eval(translate('conference_topics'));
$rz .= encode('<h2>������� ������� - ').count($pd).'</h2>
';
foreach($pd as $d){
  $rz .= '<a name="pof'.$d['user_id'].'"></a>'."\n";
  $rz .= '<p><a href="'.'#us'.$d['user_id'].encode('">����� �� ���������</a>')."</p>\n";
  if (isset($u[$d['user_id']])&& trim($u[$d['user_id']])) $d['user_id']=$u[$d['user_id']];
  else {
    $e = db_table_field('email', $user_table, '`ID`='.$d['user_id']);
    $d['user_id']='<a href="meilto:'.$e.'">'.$e.encode('</a> - ���� ����� � �������');
  }
  $d['fee'] = $d['fee'].' '.$d['currency'];
  $d['form'] =$fs[$d['form']];
  $d['topic']=$tp[$d['topic']];
  $d['title']='<strong>'.$d['title'].'</strong>';
  $d['fulltextfile' ]=file_link_and_size($d['fulltextfile' ]);
  $d['fulltextfile2']=file_link_and_size($d['fulltextfile2']);
  // ����� �� ���������� �� ����������� �� ������ � ������� �� ������ �� ��������
  $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
  $rz .= encode('<p>������ <strong>No:'.$d['ID'].'</strong> <a href="'.$edp.'&amp;proc='.$d['ID'].'">�����������</a></p>
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
  'fulltextfile'=>translate('conference_cfulltextfile'),
  'fulltextfile2'=>translate('conference_cfulltextfile2')
  ));
}
return $rz.'<p>&nbsp;</p>
';
}

//
// ��������� �� ���������� �� �������������

function conference_stats(){
global $countries, $utype, $user_table;
// ���� ������������
$cp = db_table_field('COUNT(`ID`)', $user_table, "`type`='$utype' AND `username`>''" );
// ��������� �� �������
$ca = db_select_m('country,COUNT(`ID`)', $user_table, "`type`='$utype' AND `username`>'' GROUP BY `country`" );
// ���� �������
$cr = db_table_field('COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>''" );
// �� �����������
$cc = db_select_m('topic,COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>'' GROUP BY `topic`" );
// �� �����
$cf = db_select_m('form,COUNT(`ID`)', 'proceedings', "`utype`='$utype' AND `abstract`>'' GROUP BY `form`" );
// ��������� ����������� $tp
eval(translate('conference_topics'));
// ����� �� ��������� $fs
eval(translate('conference_forms'));
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
<p>'.translate('conference_proccount').': '.$cr.'</p>
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
// ��������� ���������� �� �������� ��������

function conference_abstract_titles(){
global $utype, $adm_pth, $can_manage, $can_edit, $day3, $page_header, $fdir, $user_table;
$pdf = '<img src="'.current_pth(__FILE__).'Download-PDF.png">';
$ppt = '<img src="'.current_pth(__FILE__).'Download-PPT.png">';
$page_header .= '<script>
function deleteAbstract(id){
if( confirm("'.encode('������������� �� ��������� �� ����� �� ������ � ID=').'"+id+"?") )
   document.location = "'.$adm_pth.'delete_record.php?t=proceedings&r="+id;
}
</script>';
usermenu(true);
// ���� ������������ � �� ����� �� �������������
$team = !empty($can_manage['conference']) || $can_edit;
// ������
$vl = db_select_m('vol', 'proceedings', "`utype`='$utype' GROUP BY `vol`");// die(print_r($vl,true));
// ��������� ����������� $tp
eval(translate('conference_topics'));
$rz = '';
$tc = 0; // ��� ���� �������
$rc = 0; // ���� ������ �������
// ���� �� ��������� �� �������� ��������
$t3 = db_table_field('date_time_2', 'schedules', "`sch_name`='".$day3[1]."' AND `ev_name`='".$day3[0]."'");
// ��������� ������
$td = date('Y-m-d h:m:s'); //die("<br>$td<br>$t3<br>".print_r($can_manage,false));
if (("$td"<"$t3") && !$team )
    return '<p class="message">'.translate("conference_shoeafter").db2user_date_time($t3)."</p>\n";
// ����� �� ����������
$order = ' ORDER BY `place` ASC';
$olink = ' <a href="'.set_self_query_var('order','date').'">By title</a>';
if(isset($_GET['order']) && ($_GET['order']=='date') ){
  $order = " ORDER BY `date_time_2` DESC";
  $olink = ' <a href="'.unset_self_query_var('order').'">By date</a>';
}
// ����� �� ��������
$auth = array();
// �� ����� ���
foreach($vl as $vl1) {
if($vl1['vol']=='p'){
  $pc = db_table_field('COUNT(*)', 'proceedings', "`utype`='$utype' AND `vol`='p' AND `approved_a`=1", 0);
  if(!$pc && !$team) continue;
  $rz .= '<h2>'.translate('conference_posters')."</h2>\n";
}
else $rz .= '<h2>Volume '.$vl1['vol']."</h2>\n";

// ������ �� �������� � ��� �� �������� ������
$pn = array(0=>1, 1=>1, 2=>1, 3=>1);
// �� ����� ������ �����������
for($i = 0; $i<count($tp); $i++){
   $c = 0; // ���� ������� � �������� �����������
   $cn = array(0=>0, 1=>0, 2=>0, 3=>0, 'p'=>0);
   $sr = '';
   // �����, �� ����� ��� ������� � �������� ������
   $ln = db_select_m('language', 'proceedings',
         "`utype`='$utype' AND `topic`=$i AND `vol`='".$vl1['vol']."' GROUP BY `language` ORDER BY `language` DESC");// die(print_r($ln,true));
   $aproved = ' AND `approved_a`';
   if ( $team ) $aproved = '';
   // �� ����� �� �������
   foreach($ln as $l){
     // ������� �� �������� �� ���������� ����
     $da = db_select_m('*', 'proceedings',
          "`utype`='$utype'".
          " AND `topic`='$i'".
          " AND `vol`='".$vl1['vol']."'".
          " AND `language`='".$l['language']."'".
          $aproved.$order );
     $lr = '';
     if ( $team ) $lr .= '<p>'.$l['language'].' - '.count($da)."</p>\n";
     $c += count($da);
     // �� ����� ������
     foreach($da as $d){
        $lk = ''; $cr = 6; $nm = '';
        $st = ' id="pof'.$d['ID'].'"';
        if ( $team ){
//           if(isset($_GET['rename']) && ($_GET['rename']=='on')) $d = conference_rename_files($d);
           if ($d['form']==4){ // ����� �� ������� "��������"
              // ��� �� ���������
              $ud = db_select_1('*', $user_table, "`ID`=".$d['user_id']);
              $un = $ud['firstname']." ".$ud['secondname']."  ".$ud['thirdname'];
              $lk = "$un - ".encode('��������');
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
                                             { $lk .= encode('��������� ����� �����, '); $cr--; }
              if(empty($d['keywords']))      { $lk .= translate('conference_noKeyWords'); $cr--; }
              if($lk) $lk = '<br><span class="message">'.translate('conference_Missing')."$lk</span>\n";
              else {
                $st .= ' style="background-color: lightgreen; padding: 0.5em;"';
                $rc++;
              }
           }
           $edp = stored_value('conference_editpaper', '/index.php?pid=1068');
           $lk .= '<br><a href="'.$edp.'&amp;proc='.$d['ID'].'#bottom">'.encode('�����������').'</a> ';
           if($cr<=0)
              $lk .= $cr.' <a href="#" style="font-weight:bold;color:red;" onclick="deleteAbstract('.$d['ID'].');return false;">x</a>';
           $lk .= '<br>'.file_link_and_size($d['fulltextfile']).
                  '<br>'.file_link_and_size($d['fulltextfile2']);
           $nm = $d['ID']."-".$d['place']." ";
           if($d['approved_a']) conference_add_auth($auth, $d['authors']);
        }
        if( $team || !empty($d['title'])) {
           $lr .= "<p$st>$nm";
           if(!empty($d['approved_a'])){
             if( $team ) $lr .= $d['vol']."-";
             if($d['fulltextfile2']){
               $cn[$d['vol']]++;
               $lr .= ($i+1)."-".$cn[$d['vol']].". ";
             }
           }
           if($d['fulltextfile2'])
               $lr .= '<a href="'.$fdir.$d['fulltextfile2'].'" title="'.translate('conference_dfull', false).'">'.$pdf.'</a> ';
           if($d['fulltextfile3'])
               $lr .= '<a href="'.$fdir.$d['fulltextfile3'].'" title="'.translate('conference_prez', false).'">'.$ppt.'</a> ';
           $lr .= "<span>".stripslashes($d['title'])."</span><br>\n";
           $lr .= '<em>'.$d['authors']."</em>";
           if($d['pages']){
             $lr .= " &nbsp; &nbsp; ".translate('conference_pg').$pn[$d['vol']];
             $pn[$d['vol']] += $d['pages'];
             if($d['pages']>1) $lr .= "-".($pn[$d['vol']]-1);
             if ( $team ) $lr .= ' ('.$d['pages'].')';
           }
           $lr .= "$lk</p>\n";
        }
     }
     $sr .= $lr;
   }
   if( $team || !empty($c) ){
     $rz .= '<h3>'.$tp[$i];
     if ( $team ) $rz .= " - $c";
     $rz .= "</h3>\n";
     if (("$td">"$t3") || $team ) $rz .= $sr;
     $tc += $c;
   }
} // ���� �� ������ �� ������ �����������

} // ���� �� ������ �� ������

if ( $team ) $rz = "<p>$rc / $tc, Sort: $olink, Authors: ".count($auth)."</p>\n".$rz;
return '<div id="conference_abstracts">'."\n".$rz."</div>\n";
}

// �������� �� ����������� ��� ������� ����� $n � ����� $auth
// ������� �� ������ �� �������, � ���������, ���� �� ������������ �� �������� �� ����� ���

function conference_add_auth(&$auth, $n){
$ns = explode(',', $n);
foreach($ns as $m) if (!isset($auth[trim($m)])) $auth[trim($m)] = 1; else $auth[trim($m)]++;
}

// ������������ �� ���������

function conference_rename_files($d){
global $fdir;
foreach(array('fulltextfile','fulltextfile2') as $f) if ($d[$f]) {
  $n  = 'prf'.$d['ID'].'.'.strtolower(pathinfo($d[$f], PATHINFO_EXTENSION));
  if($n==$d[$f]) continue;
  $nn = $_SERVER['DOCUMENT_ROOT'].$fdir.$n;
  $on = $_SERVER['DOCUMENT_ROOT'].$fdir.$d[$f];
  if(!file_exists($on)) continue;
  $d[$f] = $n;
  if(!rename($on, $nn)) die("Can't rename $on");
  else
    db_update_record($d, 'proceedings');
}
return $d;
}

// ���� �� �������� �� �������

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

// ���������� �� ������� ��������

function conference_abstract_book(){
global $utype;
// ������ ����������� $tp
eval(translate('conference_topics'));
$rz = '';
$order = ' ORDER BY `title` ASC';
// �� ����� ������ �����������
for($i = 0; $i<count($tp); $i++){
  $rz .= '<h2>'.$tp[$i]."</h2>\n";
  $inf = translate($utype.'_sec_'.$i.'_info');
  if($inf!=$utype.'_sec_'.$i.'_info') $rz .= $inf;
  // �����, �� ����� ��� ������� � �������� ������
  $ln = db_select_m('language', 'proceedings',
        "`utype`='$utype' AND `topic`=$i GROUP BY `language` ORDER BY `language` DESC");
  // �� ����� �� �������
  foreach($ln as $l){
     // ������� �� �������� �� ���������� ����
     $da = db_select_m('*', 'proceedings',
        "`utype`='$utype'".
        " AND `topic`='$i'".
        " AND `language`='".$l['language']."'".
        " AND `approved_f`".$order );
     // �� ����� ������
     foreach($da as $d){
       $rz .= '<h3>'.stripslashes($d['title'])."</h3>\n";
       $rz .= '<p class="anames">'.$d['authors']."</p>";
       $rz .= $d['abstract']."\n";
       if($d['keywords'])
          $rz .= '<p class="keywords"><span>'.translate('conference_ckeywords').'</span> '.$d['keywords']."</p>";
     }
  }
}
return "<div id=\"abstract_book\">\n$rz\n</div>\n";
}

?>
<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2020  Vanyo Georgiev <info@vanyog.com>

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

// ����������� �� ��������� �� ������ � ����� $_GET['proc']

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'mod/usermenu/f_usermenu.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_db_insert_or_1.php');

// �������� �� ������� �� ������� ����������
usermenu(true);

// ��� ���� ����� �� ����� conference - ����
if(empty($can_manage['conference'])) die("Not permitted for current user");

$ms = '';
if(count($_POST)) $ms = process();

if(!is_numeric($_GET['proc'])) die("Incorrect parameter");

// ��� �� �������������
$utype = stored_value('conference_usertype', 'basa2019');

// ����� �� �������
$p = db_select_1('*', 'proceedings', "`ID`=".$_GET['proc']);

// ����� �� ������
$au = preg_replace('/\d/', '', strip_tags($p['authors']) );
$au = preg_split('/\,+\s*/', $au);

if(!$p) die('Proceeding do not exist');
if($p['utype']!=$utype) die('Incorrect user type');

// ����� �� ������� reviewers
$da = db_select_m('b.position, b.firstname, b.secondname, b.thirdname, a.ID',
                  'reviewers AS a RIGHT JOIN users AS b ON a.user_id=b.ID',
                  "utype='$utype' AND ".
                  "a.confirmed=1 AND ".
                  "topic=".$p['topic']." AND ".
                  "languages LIKE '%".$p['language']."%'");

// ����� ����������, ����� �� �� �������� �� ��������
$rs = array();
// ���� ������� �� ������������
$rc = array();
foreach($da as $i=>$d){
   if( (strpos($p['authors'], $d['firstname'])!==false) ||
       (!empty($d['secondname']) && (strpos($p['authors'], $d['secondname'])!==false)) ||
       (strpos($p['authors'], $d['thirdname'])!==false)
   ) continue;
   else {
     $rc[$d['ID']] = db_table_field('COUNT(*)', 'reviewer_work', "`rev_id`=".$d['ID']);
     $rs[$d['ID']] = $d['position'].' '.$d['firstname'].' '.$d['thirdname']." - ".$rc[$d['ID']];
   }
}
// ��������� �� ���� ��������� ��������
asort($rc);
$r2 = array();
foreach($rc as $i=>$v) $r2[$i] = $rs[$i];

$ms = '';
if(!count($r2)) $ms .= '<p class="message">'.translate('conference_noReviewers')."</p>\n";

//die(print_r($rc,true));

$page_title = encode('����������� �� ���������');

$page_content = "<h1>$page_title</h1>\n
<h2>".encode('�� ������').":</h2>
<p>".$p['title']."</p>";

// �������� �� ��������
$rd = db_select_1('*', 'reviewer_work', '`proc_id`='.$_GET['proc']);
if(!$rd){
  $rd['date_time_1']='NOW()';
  $rd['date_time_2']='NOW()';
  $rd['proc_id']=$_GET['proc'];
  $rd['rev_id']=array_key_first($r2);
}

// ����� �� ���������
$f = new HTMLForm('assign_rewiewer');
$f->add_input( new FormInput( '', 'date_time_1', 'hidden', $rd['date_time_1']) );
$f->add_input( new FormInput( '', 'date_time_2', 'hidden', $rd['date_time_2']) );
$f->add_input( new FormInput( '', 'proc_id', 'hidden', $p['ID']) );
$fi = new FormSelect( translate('conference_reviewer'), 'rev_id', $r2 );
$fi->values='k';
$f->add_input( $fi );
$f->add_input( new FormInput( '', '', 'submit', encode('���������') ) ) ;

$page_content .= $ms.$f->html();

include_once($idir.'lib/build_page.php');
// ��������� �� ��������� �����

function process(){
$r = db_insert_or_1($_POST, 'reviewer_work', "`rev_id`=".$_POST['rev_id']." AND `proc_id`='".$_POST['proc_id']."'", 'b', true);
if( !($r===false) )
    return '<p class="message">'.encode('������� �� �������� �������')."</p>\n";
else
    return '<p class="message">'.encode('�������� ������. �������� �� �������������� �� �����.')."</p>\n";
}

?>
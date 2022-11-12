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

// Правене на потребител с номер $_GET['uid'] рецензент на текущата конференция

$idir = dirname(dirname(__DIR__)).'/';
$ddir = $idir;

include_once($idir.'mod/usermenu/f_usermenu.php');
include_once($idir.'lib/f_message.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_db_insert_or_1.php');

// Проверка на правата на влезлия потребител
if(!isset($page_id)) $page_id = 4;
usermenu(true);

// Ако няма право за модул conference - край
if(empty($can_manage['conference'])) die("Not permitted for current user");

// Обработка на изпратени данни
$ms = '';
if(count($_POST)) $ms = process();

if(!is_numeric($_GET['uid'])) die("Incorrect parameter");

// Тип на потребителите
$utype = stored_value('conference_usertype', 'basa2019');

// Данни за потребителя
$u = db_select_1('*', 'users', "`ID`=".$_GET['uid']);

if(!$u) die('User do not exist');
if($u['type']!=$utype) die('Incorrect user type');

// Данни от таблица reviewers
$d = db_select_m('*', 'reviewers',
                 "`user_id`=".$u['ID']." AND `utype`='$utype' ORDER BY `topic` ASC");// die(print_r($d, true));
if(!count($d)){
  $d1['date_time_1']='NOW()';
  $d1['date_time_2']='NOW()';
  $d1['utype']=$utype;
  $d1['user_id']=$_GET['uid'];
  $d1['topic']=0;
  $d1['languages']='';
  $d1['confirmed']=1;
}
else $d1 = end($d);



// Тематични направления
eval(translate('conference_topics_'.$utype, false));


// Форма за попълване
$f = new HTMLForm('make_rewiewer');
$f->add_input( new FormInput( '', 'date_time_1', 'hidden', $d1['date_time_1']) );
$f->add_input( new FormInput( '', 'date_time_2', 'hidden', $d1['date_time_2']) );
$f->add_input( new FormInput( '', 'utype', 'hidden', $utype) );
$f->add_input( new FormInput( '', 'user_id', 'hidden', $d1['user_id']) );
$fi = new FormSelect( translate('conference_ctopic'), 'topic', $tp, $d1['topic'] );
$fi->values='k';
$f->add_input( $fi );
$f->add_input( new FormSelect( translate('conference_languages'), 'languages', 
                               array('bg,en','bg','en')) );
$fi = new FormInput( translate('conference_Rconfirmed'), 'confirmed', 'checkbox');
if($d1['confirmed']) $fi->js = ' checked';
$f->add_input( $fi );
$f->add_input( new FormInput( '', '', 'submit', encode('Запазване') ) ) ;

$page_header = '<link href="/_style.css" rel="stylesheet" type="text/css">
<style>body { padding:1em; }
</style>';

$page_title = translate('conference_makeReviewer');

$page_content = "<h1>$page_title</h1>".
'<p><a href="/index.php?pid=4&rev2=on" target="allRevs">'.encode('Рецензенти')."</a></p>
<p>".$u['position'].' '.$u['firstname'].' '.$u['secondname'].' '.$u['thirdname'].' '."</p>\n".
"<p>".translate('user_institution')." ".$u['institution']."</p>\n";
if(count($d)){
  $page_content .= '<p>';
  foreach($d as $r) $page_content .= 'ID:'.$r['ID'].' &nbsp; '.($tp[$r['topic']])."<br>\n";
  $page_content .= '</p>';
}
if($ms) $page_content .= message($ms);
$page_content .= $f->html();

include_once($idir.'lib/build_page.php');

// Обработка на изпратени данни

function process(){
if(isset($_POST['confirmed']) && ($_POST['confirmed']=='on')) $_POST['confirmed']=1; else $_POST['confirmed']=0;
$r = db_insert_or_1($_POST, 'reviewers', 
                    "`user_id`=".$_POST['user_id'].
                    " AND `utype`='".$_POST['utype']."'".
                    " AND `topic`='".$_POST['topic']."'", 'b', false);
if( !($r===false) )
    return encode('Данните са запазени успешно');
else
    return encode('Възникна грешка. Съобщете на администратора на сайта.');
}

?>
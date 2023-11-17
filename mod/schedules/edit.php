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

// Редактиране датите на събитие

error_reporting(E_ALL); ini_set('display_errors',1);

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_edit_record_form.php');

if (!isset($_GET['id'])) die("No event id.");

// Ако са изпратени данни с $_POST
if (count($_POST)) process_schdata();

// Номер на записа
$i = 1*$_GET['id'];

$d = db_select_1('*','schedules',"`ID`=$i");

$n = array(
'ID'=>$i,
'sch_name'=>translate('schedules_schname'),
'date_time_1'=>translate('schedules_datetime1'),
'date_time_2'=>translate('schedules_datetime2')
);

if(!session_id()) session_start();

$page_content = '<h1>'.translate('schedules_editevent').'</h1>
<h2>'.translate('schedules_name').': "'.translate($d['sch_name'],false).'"</h2>
<h3>'.translate('schedules_event_name').': "'.translate($d['ev_name'],false).'" ('.$d['ev_name'].')</h3>
'.edit_record_form($n, 'schedules');

if(isset($_SESSION['http_referer']))$page_content .= '
<p><a href="'.$_SESSION['http_referer'].'">'.translate('schedules_cancel').'</a></p>';

include_once($idir.'lib/build_page.php');

// Обработване на изпратените с $_POST данни

function process_schdata(){
$cp = array(
'ID'=>1*$_POST['ID'],
'sch_name'   =>addslashes($_POST['sch_name']),
'date_time_1'=>addslashes($_POST['date_time_1']),
'date_time_2'=>addslashes($_POST['date_time_2'])
);
process_record($cp, 'schedules');
session_start();
if (isset($_SESSION['http_referer'])){
  header('Location: '.$_SESSION['http_referer']);
}
}

?>

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


// Създаване на нови потребители от типа, който има влезлия потребител

$black_list = array(
'krashi@dir.bg',
'sharus@mail.prosoft.bg',
'boryana.alipieva@telelink.com',
'bulkon_rialisteit@abv.bg',
'florans91@abv.bg',
'harbich@dir.bg',
'herkulan@abv.bg',
'hertnerbulgaria@yahoo.de',
'hidro_bg63@abv.bg',
'hydroizomat@online.bg',
'iapistroi2006eood@abv.bg',
'ng_technology@abv.bg',
'office@strabag.com',
'simeonova@abv.bg',
'sofia@dundeeprecious.com',
'tancrit@abv.bg',
'techno_eng_tm@abv.bg',
'tedistroi@abv.bg',
'tektonika_5_bg@abv.bg',
'tes@mbox.contact.bg',
'transtroy2001@mail.bg',
'ultrastroy@abv.bg',
'viktor.kuzmanov@kanal.bg',
'xelliosselectric@abv.bg',
'bureu_vassilev_ltd@abv.bg',
'konstb@dir.bg',
'martatp@abv.bg',
'bobimacheva@abv.bg',
'magureanu.cornelia@bmt.utcluj.ro',
'robert.ballok@dst.utcluj.ro',
'al.mangus@dot.ca.gov',
'alexandra.stan@mecon.utcluj.ro',
'angel_ashikov@abv.bg',
'bonic@iao.ru',
'disips@abv.bg',
'eva.dvorakova@fsv.cvut.cz',
'filip.rehor@fsv.cvut.cz',
'iakimov@netissat.bg',
'iakimov_i@mail.bg',
'irb_irb@abv.bg',
'jana.drienovska@stuba.sk',
'jozef.havran@stuba.sk',
'lubos.snirc@stuba.sk',
'm_a_r_i_q@gbg.bg',
'molinet_zs@abv.bg',
'olteanr@ce.tuiasi.ro',
'pradovanova@nbu.bg',
'radi.ganev@abv.bg',
'toni_vladimirova@abv.bg',
'trifan.hulpus@cif.utcluj.ro',
'vicky_st@abv.bg',
'vtingeva@gbg.bg');

$idir = dirname(dirname(dirname(__FILE__))).'/';
$ddir = $idir;

include($idir.'lib/translation.php');
include_once($idir.'lib/o_form.php');
include_once($idir.'lib/f_rand_string.php');
include_once($idir.'mod/user/f_user.php');
include_once($idir.'lib/f_db_insert_m.php');

if(!session_id()) session_start();

// Ако в сесията няма данни за влязъл потребител - съобщение, че трябва да се влезе
if (!isset($_SESSION['user_username']) || !isset($_SESSION['user_password']) )
   $page_content = '<p class="message">'.translate('userreg_nouserlogedin').'</p>';
else {
  // Таблица с данни за потребители
  $user_table = stored_value('user_table', 'users');
  // Номер на влезлия потребител
  $id = db_table_field('ID', $user_table,
        "`username`='".$_SESSION['user_username']."' AND `password`='".$_SESSION['user_password']."'");
  // Ако номера на влезлия потребител не е валиден - съобщение, че трябва да се влезе
  if (!$id) $page_content = '<p class="message">'.translate('userreg_mustlogin').'</p>';
  else { //die($id);
    // Проверка дали влезлият потребител има право да създава нови потребители
    $p = db_table_field('yes_no','permissions',
           "`user_id`=$id AND ((`type`='module' AND `object`='userreg') OR `type`='all')");
    if (!$p) $page_content = '<p class="message">'.translate('userreg_nopermission').'</p>';
    else {
      // Тип на влезлия потребител
      $t = db_table_field('type', $user_table, "`ID`=$id");
      // Съобщение за резултата от обработката на изпратени с $_POST данни
      $ms = userreg_processnew($t);
      if ($ms) $ms = '<p class="message">'.$ms.'</p>';
      // Форма за създаване на нов потребител
      $f = new HTMLForm('newuserreg_form');
      $f->add_input( new FormInput('','type','hidden',$t) );
      $i = new FormTextArea(translate('user_emails'),'emails');
      $i->ckbutton = '';
      $f->add_input( $i );
      $f->add_input( new FormInput('','','submit', translate('userreg_create')) );
      $page_content = '<h1>'.translate('userreg_bnew')."</h1>\n".
                      '<p>User type: '.$t."</p>\n".
                      translate('userreg_bdescr').
                      $ms.$f->html();
    }
  }
}

if(!isset($page_header)) $page_header = '';

$page_header .= '<style>
th { vertical-align:top; }
</style>
';

include($idir.'lib/build_page.php');

//
// Обработка на изпратени с $_POST данни

function userreg_processnew($t){
// Връщан резултат - съобщение относно обработката
$rz = '';
// Ако не са изпратени данни - празен низ
if (!count($_POST)) return $rz;
global $user_table, $id, $black_list;
// Разделяне на имейлите
$es = explode("\n", $_POST['emails']);
if(count($es)==1) $es = explode(',', $_POST['emails']);
$d = array();
foreach($es as $e){
  // Проверка дали вече няма потребител с имейл $e
  $e1 = trim($e);
  $i = db_table_field('ID', $user_table, "`type`='$t' AND `email`='$e1'");
  // Данни за нов потребител
  if (!$i && ($e1>' ') && !in_array(strtolower($e1), $black_list)){
     $d[] = array(
            'creator_id'=>$id,
            'type'=>addslashes($_POST['type']),
            'date_time_0'=>'NOW()',
            'date_time_1'=>'NOW()',
            'username'=>$e1,
            'email'=>$e1
     );
  }
}
return db_insert_m($d, $user_table, true);
}

?>

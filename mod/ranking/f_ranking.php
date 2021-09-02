<?php
// Copyright: Vanyo Georgiev info@vanyog.com

// ������� ������� ��� �������� �� ���������� ��:
// ����� �� ���������, ����� �� ���������� ������������, ������� �� ����������

//include_once('f_db_table_field.php');

// ������� ��� WHERE ������ �� SQL �������� �� ������ �� �������� ��������������� ��������
// �� �� �� �� �������� � ������ ��������.
$rank_addtowhile1 = '';

// ������� ��� WHERE ������ �� SQL �������� �� ������ �� ���-����� ���������� ��������
// �� �� �� �� �������� � ������ � �������� ��������������� ��������.
$rank_addtowhile2 = '';

function ranking(){
$limit = stored_value('ranking_limit', "3"); // ���� �������� �� ����� ���, ����� �� �������

$z1 = "\n".'<tr><td class="n">'.translate('ranking_new').'</td>
<td>'.new_page_links($limit)."</td></tr>\n";

$z2 = "\n".'<tr><td class="n">'.translate('ranking_updated').'</td>
<td>'.updated_page_links($limit)."</td></tr>\n";

$z3 = "\n".'<tr><td class="n">'.translate('ranking_min_visited').'</td>
<td>'.min_visited_page_links($limit)."</td></tr>\n";

return '<div id="page_ranking">
<h2>'.translate('ranking_title').'</h2>
<table>
'.$z2.$z1.$z3.'
</table>

</div>';
}

// ���-������ ��������

function new_page_links($limit){
global $rank_addtowhile1, $rank_addtowhile2;
$h = in_edit_mode() ? 1 : '`hidden`=0';
$d = db_select_m('*','pages',"$h ORDER BY `ID` DESC LIMIT 0,".$limit);
// ��������� �� $rank_addtowhile1 � $rank_addtowhile2
$rank_addtowhile1 = '';
$rank_addtowhile2 = '';
foreach($d as $d1){
  if ($rank_addtowhile1) $rank_addtowhile1 .= ' OR ';
  $rank_addtowhile1 .= "`name`='".$d1['content']."'";
  if ($rank_addtowhile2) $rank_addtowhile2 .= ' OR ';
  $rank_addtowhile2 .= "`ID`='".$d1['ID']."'";
}
if ($rank_addtowhile1) $rank_addtowhile1 = " AND NOT($rank_addtowhile1)";
if ($rank_addtowhile2) $rank_addtowhile2 = " AND NOT($rank_addtowhile2)";
return page_links($d);
}

function page_links($d){
$rz = '';
foreach($d as $p) $rz .= page_link($p);
return $rz;
}

function page_link($p){
$a = '';
if($p['ID']==$GLOBALS['page_id']) $a = translate('sitemap_currentpage');
return '<a href="index.php?pid='.$p['ID'].'">'.
       strip_tags(translate($p['title']), "").'</a>'.$a."<br>\n";
}

// ���-����� ���������� ��������

function updated_page_links($limit){
global $language, $rank_addtowhile1;
$i = 0; $j = 0; $rz = '';
$max = db_table_field('COUNT(*)','content','1'); // ��� ���� �� �������� �� ������� $tn_prefix.'content'
do {
  do {
    // ������� �� $i-��� ���-����� ������� ������ �� ������� $tn_prefix.'content', ����� � �� ������� ���� $language
    $cd = db_select_m('*','content',"`language`='$language'$rank_addtowhile1 ORDER BY `date_time_2` DESC LIMIT $i,1");
    if (count($cd)) $cd = $cd[0];
    
    // ��� �� � ������� ������ - ���� �� ���������
    if (!count($cd)) return $rz; 

    // ������� �� ������ �� ����������, ����� ���������� � ���� ����� (��� ��� ������)
    $h = in_edit_mode() ? '' : '`hidden`=0 AND ';
    $pd = db_select_m('*','pages',"$h`content`='".$cd['name']."'");
    if (count($pd)) $pd = $pd[0];

    $i++;
   
  } while ( !count($pd) && ($i<$max) );
  $j++;
  $rz .= page_link($pd);
} while (($j<$limit) && ($i<$max));
return $rz;
}

// ���-���������� ��������

function max_visited_page_links($limit){
$h = in_edit_mode() ? 1 : '`hidden`=0';
$d = db_select_m('*','pages',"$h ORDER BY `tcount` DESC LIMIT 0,".$limit);
return page_links($d);
}

// ���-����� ���������� ��������

function min_visited_page_links($limit){
global $rank_addtowhile2;
$h = in_edit_mode() ? 1 : '`hidden`=0';
$d = db_select_m('*','pages',"$h $rank_addtowhile2 ORDER BY `tcount` ASC LIMIT 0,".$limit);
//die($rank_addtowhile2);
return page_links($d);
}
?>

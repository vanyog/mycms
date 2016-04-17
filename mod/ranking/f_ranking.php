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
$limit = 3; // ���� �������� �� ����� ���, ����� �� �������
return '<div id="page_ranking">
<h2>'.translate('ranking_title').'</h2>
<table>

<tr><td class="n">'.translate('ranking_updated').'</td>
<td>'.updated_page_links($limit).'</td></tr>

<tr><td class="n">'.translate('ranking_new').'</td>
<td>'.new_page_links($limit).'</td></tr>

<tr><td class="n">'.translate('ranking_max_visidet').'</td>
<td>'.max_visited_page_links($limit).'</td></tr>

<tr><td class="n">'.translate('ranking_min_visited').'</td>
<td>'.min_visited_page_links($limit).'</td></tr>

</table>

</div>';
}

// ���-������ ��������

function new_page_links($limit){
global $rank_addtowhile1, $rank_addtowhile2;
$d = db_select_m('*','pages','`hidden`=0 ORDER BY `ID` DESC LIMIT 0,'.$limit);
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
return '<a href="index.php?pid='.$p['ID'].'">'.translate($p['title']).'</a>'."<br>\n";
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
    $pd = db_select_m('*','pages',"`hidden`=0 AND `content`='".$cd['name']."'");
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
$d = db_select_m('*','pages','`hidden`=0 ORDER BY `tcount` DESC LIMIT 0,'.$limit);
return page_links($d);
}

// ���-����� ���������� ��������

function min_visited_page_links($limit){
global $rank_addtowhile2;
$d = db_select_m('*','pages',"`hidden`=0 $rank_addtowhile2 ORDER BY `tcount` ASC LIMIT 0,".$limit);
//die($rank_addtowhile2);
return page_links($d);
}
?>

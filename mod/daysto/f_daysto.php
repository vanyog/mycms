<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2017  Vanyo Georgiev <info@vanyog.com>

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

// Показва текст, който казва, колко дни, часове, минути и секудни остават от настоящия момент
// до друг, зададен момент.
// Параметърът $a e стринг, разделен с | на три части:
// - моментът, до който се мери времето.
//   Може да бъде във вида 'schedule:XX', където 'XX' е номер на запис от таблица 'schedules' от който се взема
//   времето от поле 'date_time_2'.
// - частта от надписа пред оставащото време
// - частта от надписа след оставащото време

function daysto($a){
$b = explode('|', $a);
$s = explode(':', $b[0]);
if($s[0]=='schedule') $b[0] = db_table_field('date_time_2', 'schedules', "`ID`=".$s[1]);
$dt = strtotime($b[0]);
$rz = '<span id="daysto">aaa</span>
<script>
var daysto = '.$dt.' - Math.round(new Date/1000);
var daysto_span = document.getElementById("daysto");
timeCountdown();
function timeCountdown(){
  var r = "";
  var v = Math.floor(daysto/86400);
  if(v==1) r += v + "'.translate('daysto_day', false).' ";
  if(v>1 ) r += v + "'.translate('daysto_days', false).' ";
  var s = daysto % 86400;
  var v = Math.floor(s/3600);
  if(v==1) r += v + "'.translate('daysto_hour', false).' ";
  if(v>1 ) r += v + "'.translate('daysto_hours', false).' ";
  var s = daysto % 3600;
  var v = Math.floor(s/60);
  if(v==1) r += v + "'.translate('daysto_minute', false).' ";
  if(v>1 ) r += v + "'.translate('daysto_minutes', false).' ";
  var s = daysto % 60;
  var v = Math.floor(s);
  if(v==1) r += v + "'.translate('daysto_second', false).' ";
  if(v>1 ) r += v + "'.translate('daysto_seconds', false).' ";
  daysto_span.innerHTML = "'.$b[1].'" + r + "'.$b[2].'";
  daysto = daysto - 1;
  setTimeout(timeCountdown, 1000);
}
</script>
';
return $rz;
}

?>
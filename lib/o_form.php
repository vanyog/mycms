<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2012  Vanyo Georgiev <info@vanyog.com>

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

//----- HTMLForm ------------

class HTMLForm {

public $name = '';
public $method = 'post';
public $astable = true;
public $action = '';
public $text = '';
private $ins = array();

// $n - name атрибут на формата
// $at - дали да се показва в таблица
// $tx - текст, който се показва между <form> тага и първия елемент на формата

function __construct($n, $at=true, $tx = ''){
$this->name = $n;
$this->astable = $at;
if ($at){ if ($tx) $this->text = "<tr><td colspan=\"2\">$tx</td></tr>"; }
else $this->text = $tx;
}

function add_input($in){
$this->ins[] = $in;
if (!$this->action) $this->action = str_replace('&','&amp;',$_SERVER['REQUEST_URI']);
}

public function html(){
$js = '';
// JavaScript функция, която проверява дали всички текстови полета във формата са попълнени.
// За да се изпълни, на бутона на формата трябва да се присвои ->js = ' onclick="ifNotEmpty_имеНаФорма();"';
$js1 = 'var noEmptyCheck = "";
function ifNotEmpty_'.$this->name.'(){
var f = document.forms["'.$this->name.'"];
if(noEmptyCheck){
   if(confirm(noEmptyCheck)) f.submit();
   return;
}
var l = f.length;
var r = 1;
for(i=0;i<l-1;i++){
  var e = f.elements[i];
  if ((e.type=="text")||(e.type=="textarea")) r = r*e.value.length;
}
if (r) f.submit(); else alert("'.translate_if('fillin_all', 'All fields mut be filled in.').'");
}
';
$rz = "<form enctype=\"multipart/form-data\" name=\"$this->name\" id=\"$this->name\" method=\"$this->method\" action=\"$this->action\">\n";
if ($this->astable) $rz .= "<table>\n";
$rz .= $this->text;
foreach($this->ins as $i){
  $rz .= $i->html($this->astable);
  if (!(strpos($i->js, 'ifNotEmpty_'.$this->name.'()')===false)) $js .= $js1;
}
if ($this->astable) $rz .= "</table>\n";
$rz .= "</form>\n";
if ($js) $rz = "<script type=\"text/javascript\">\n$js1</script>\n$rz";
return $rz;
}

}

function translate_if($a, $b){
if (function_exists('translate')){
  $rz = translate($a, false);
  if($rz==$a){ return $b; }
  else return $rz;
}
else return $b;
}

//----- FormInput ------------

class FormInput {

public $caption = '';
public $name = '';
public $type = '';
public $value = '';
public $checked = '';
public $size = '';
public $id = '';
public $js = '';
public $max_file_size = '50000000';
public $textAfter = '';
public $help = '';

function __construct($c,$n,$t,$v = '',$ta = ''){
$this->caption = $c;
$this->name = $n;
$this->type = $t;
$this->value = "$v";
/*if($t=='checkbox'){
  if($v) $this->checked = 'checked';
  else $this->value = 1;
}*/
$this->textAfter = $ta;
}

public function set_event($e,$js){
$this->js = " $e=\"$js\"";
}

public function html($it){
$dsbl = lock_form_fields();
$rz = '';
if($this->id){
   if (!$it) $rz .= "<label for=\"$this->id\">$this->caption</label> ";
   else $rz .= "<tr><th><label for=\"$this->id\">$this->caption</label> </th><td>";
}
if( ($this->type=='file') && $this->value){
   $p = strrpos($this->value, '/');
   $vl = current_pth($this->value).substr($this->value, $p + 1 );
   if(!empty($GLOBALS['use_viewer'])){
     $_SESSION['can_view_file'][] = $vl;
     $vl = $GLOBALS['pth']."view.php?file=$vl";
   }
   $rz .= "<a href=\"$vl\" target=\"_blank\">$vl</a><br>\n";
}
$rz .= "<input type=\"$this->type\" ";
if ($this->name) $rz .= "name=\"$this->name\"";
if (strlen($this->value)) $rz .= " value=\"$this->value\"";
if ($this->size) $rz .= " size=\"$this->size\"";
if ($this->id) $rz .= " id=\"$this->id\"";
if ($this->js) $rz .= " $this->js";
if ($this->checked) $rz .= " $this->checked";
$rz .= "$dsbl>";
if ($this->type=='file'){
   $rz .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->max_file_size.'">';
}
if ($this->textAfter) $rz .= ' '.$this->textAfter;
if ($this->help) $rz .= "\n<br>".$this->help;
if (!$it) $rz .= "\n";
else $rz .= "</td></tr>\n";
return $rz;
}

}

function lock_form_fields(){
global $lock_fields;
if(isset($lock_fields) && ($lock_fields===true)) return ' disabled';
return '';
}

//----- FormTextArea ------------

class FormTextArea {

public $caption = '';
public $name = '';
public $cols = '';
public $rows = '';
public $text = '';
public $js = '';
public $ckbutton = ''; // Бутон CKEditor
public $size = true;

function __construct($c,$n,$cl=100,$r=10,$t=''){
global $mod_pth, $page_header, $ckpth;
$this->caption = $c;
$this->name = $n;
$this->cols = $cl;
$this->rows = $r;
$this->text = str_replace(chr(60).'!--$$_',chr(60).' !--$$_',$t);
// Домавяне на бутон за зареждане на CKEditor.
$cka = $_SERVER['DOCUMENT_ROOT'].$ckpth.'ckeditor.js';
if (file_exists($cka)){
  $sc = '   <script src="'.$ckpth.'ckeditor.js"></script>'."\n";
  if (strpos($page_header,$sc)===false) $page_header .= $sc;
  $this->ckbutton = '<input type="button" value="CKEditor" onclick="CKEDITOR.replace(\''.$this->name.'\');"><br>';
}
else {
  $sc = '   <script src="//cdn.ckeditor.com/4.5.7/full/ckeditor.js"></script>'."\n";
  if (strpos($page_header,$sc)===false) $page_header .= $sc;
  $this->ckbutton = '<input type="button" value="CKEditor" onclick="CKEDITOR.replace(\''.$this->name.'\');"><br>';
}
}

public function html($it){
$dsbl = lock_form_fields();
$rz = "$this->ckbutton<textarea name=\"$this->name\" id=\"$this->name\" ";
if ($this->size) $rz .=  "cols=\"$this->cols\" rows=\"$this->rows\"";
$rz .= "$this->js$dsbl>$this->text</textarea>";
if (!$it) $rz = "$this->caption $rz<br>\n";
else $rz = "<tr>\n<th>$this->caption</th>\n<td>$rz</td>\n</tr>";
return $rz;
}


}

//----- FormSelect ------------
// Списък
// Надписите от списъка се задават чрез стойностите от масив,
// който може да е обикновен или асоциативен. Ако масивът е асоциативен
// ключовете му се изпалзват за стойности, връщани при избиране от списъка.
// За да не се връщат текстове, а други стоности, трябва да се присвои values='k'.

class FormSelect {

public $caption = '';
public $name = '';
public $options = array();
public $values = 'v';
public $selected = -1;
public $js = '';

function __construct($c, $n, $op, $s = -1){
$this->caption = $c;
$this->name = $n;
$this->options = $op;
$this->selected = $s;
}

public function html($it){
$dsbl = lock_form_fields();
$rz = '';
if ($it) $rz .= "<tr><th>";
$rz .= "$this->caption ";
if ($it) $rz .= "</th><td>\n";
$rz .= "<select";
if ($this->name) $rz .= " name=\"$this->name\"";
$rz .= "$this->js$dsbl>\n";
$i = 0;
foreach($this->options as $k => $v){
  $sl = '';
  if ($this->values=='k'){
     if ($k==$this->selected) $sl = ' selected';
  } else {
     if ($i==$this->selected) $sl = ' selected';
     else if ($v===$this->selected) $sl = ' selected';
  }
  switch($this->values){
  case 'v': $rz .= "<option value=\"$v\"$sl>$v</option>\n"; break;
  case 'k': $rz .= "<option value=\"$k\"$sl>$v</option>\n"; break;
  }
  $i++;
}
$rz .= "</select>";
if ($it) $rz .= "</td>
</tr>";
return $rz;
}


}

//----- FormReCaptcha ------------

class FormReCaptcha{

public $caption = '';
public $public_key = '';
public $js = '';

function __construct($c, $pk =''){
$this->caption = $c;
if (!$pk) $this->public_key = stored_value('recaptcha_pub');
else $this->public_key = $pk;
if(!$this->public_key) die("'recaptcha_pub' setting not found.");
}

public function html($it){
global $page_header, $language;
$page_header .= '<script src="https://www.google.com/recaptcha/api.js?hl='.$language.'"></script>'."\n";
$rz = '';
if ($it) $rz = "<tr>\n<th>";
$rz .= $this->caption;
if ($it) $rz .= "</th>\n<td>"; else $rz .= " ";
if(!is_local()) $rz .= '<div class="g-recaptcha" data-sitekey="'.$this->public_key.'" data-size="compact"></div>';
else $rz .= "<div class=\"g-recaptcha\">reCapcha will be shown here if online</div>";
if ($it) $rz .= "</td>\n<tr>\n"; else $rz .= "<br>\n";
return $rz;
}

} //class FormSelect

//----- FormCurrencyInput ------------

// Валути
global $currency;
$currency = array(
'BGN'=>'BGN Bulgarian Lev',
'EUR'=>'EUR Euro'
);

class FormCurrencyInput{

public $caption = '';
public $name = '';
public $name2 = '';
public $value = '0.00';
public $currency = 'BGN';
public $js = '';
public $cselect = '';

function __construct($c, $n1, $n2, $v = '0.00', $r = 'BGN'){
$this->caption = $c;
$this->name = $n1;
$this->name2 = $n2;
$this->value = $v;
$this->currency = $r;
}

public function html($it){
global $currency;
if ($it) $rz = "<tr>\n<th>";
$rz .= $this->caption;
if ($it) $rz .= "</th>\n<td>"; else $rz .= " ";
$rz .= '<INPUT name="'.$this->name.'" type="text" size="5" value="'.$this->value.'">
<select name="'.$this->name2.'">';
foreach($currency as $k=>$v){
  if ($k==$this->currency) $sl = ' selected'; else $sl = '';
  $rz .= "<option value=\"$k\"$sl>$v\n";
}
$rz .= '</select>';
if ($it) $rz .= "<td>\n<tr>\n"; else $rz .= "<br>\n";
return $rz;
}

} // class FormCurrencyInput

//----- formCountrySelect ------------
// Функция, която връща обект от тип FormSelect за избиране на държава

global $countries;
$countries = array(
'AD' => 'Andorra',
'AE' => 'United Arab Emirates',
'AF' => 'Afghanistan',
'AG' => 'Antigua &amp; Barbuda',
'AI' => 'Anguilla',
'AL' => 'Albania',
'AM' => 'Armenia',
'AN' => 'Netherlands Antilles',
'AO' => 'Angola',
'AQ' => 'Antarctica',
'AR' => 'Argentina',
'AS' => 'American Samoa',
'AT' => 'Austria',
'AU' => 'Australia',
'AW' => 'Aruba',
'AZ' => 'Azerbaijan',
'BA' => 'Bosnia and Herzegovina',
'BB' => 'Barbados',
'BD' => 'Bangladesh',
'BE' => 'Belgium',
'BF' => 'Burkina Faso',
'BG' => 'Bulgaria',
'BH' => 'Bahrain',
'BI' => 'Burundi',
'BJ' => 'Benin',
'BM' => 'Bermuda',
'BN' => 'Brunei Darussalam',
'BO' => 'Bolivia',
'BR' => 'Brazil',
'BS' => 'Bahama',
'BT' => 'Bhutan',
'BU' => 'Burma (no longer exists)',
'BV' => 'Bouvet Island',
'BW' => 'Botswana',
'BY' => 'Belarus',
'BZ' => 'Belize',
'CA' => 'Canada',
'CC' => 'Cocos (Keeling) Islands',
'CF' => 'Central African Republic',
'CG' => 'Congo',
'CH' => 'Switzerland',
'CI' => 'C&#244;te D\'ivoire (Ivory Coast)',
'CK' => 'Cook Iislands',
'CL' => 'Chile',
'CM' => 'Cameroon',
'CN' => 'China',
'CO' => 'Colombia',
'CR' => 'Costa Rica',
'CU' => 'Cuba',
'CV' => 'Cape Verde',
'CX' => 'Christmas Island',
'CY' => 'Cyprus',
'CZ' => 'Czech Republic',
'DE' => 'Germany',
'DJ' => 'Djibouti',
'DK' => 'Denmark',
'DM' => 'Dominica',
'DO' => 'Dominican Republic',
'DZ' => 'Algeria',
'EC' => 'Ecuador',
'EE' => 'Estonia',
'EG' => 'Egypt',
'EH' => 'Western Sahara',
'ER' => 'Eritrea',
'ES' => 'Spain',
'ET' => 'Ethiopia',
'FI' => 'Finland',
'FJ' => 'Fiji',
'FK' => 'Falkland Islands (Malvinas)',
'FM' => 'Micronesia',
'FO' => 'Faroe Islands',
'FR' => 'France',
'FX' => 'France, Metropolitan',
'GA' => 'Gabon',
'GB' => 'Great Britain (United Kingdom)',
'GD' => 'Grenada',
'GE' => 'Georgia',
'GF' => 'French Guiana',
'GH' => 'Ghana',
'GI' => 'Gibraltar',
'GL' => 'Greenland',
'GM' => 'Gambia',
'GN' => 'Guinea',
'GP' => 'Guadeloupe',
'GQ' => 'Equatorial Guinea',
'GR' => 'Greece',
'GS' => 'South Georgia and the South Sandwich Islands',
'GT' => 'Guatemala',
'GU' => 'Guam',
'GW' => 'Guinea-Bissau',
'GY' => 'Guyana',
'HK' => 'Hong Kong',
'HM' => 'Heard &amp; McDonald Islands',
'HN' => 'Honduras',
'HR' => 'Croatia',
'HT' => 'Haiti',
'HU' => 'Hungary',
'ID' => 'Indonesia',
'IE' => 'Ireland',
'IL' => 'Israel',
'IN' => 'India',
'IO' => 'British Indian Ocean Territory',
'IQ' => 'Iraq',
'IR' => 'Islamic Republic of Iran',
'IS' => 'Iceland',
'IT' => 'Italy',
'JM' => 'Jamaica',
'JO' => 'Jordan',
'JP' => 'Japan',
'KE' => 'Kenya',
'KG' => 'Kyrgyzstan',
'KH' => 'Cambodia',
'KI' => 'Kiribati',
'KM' => 'Comoros',
'KN' => 'St. Kitts and Nevis',
'KP' => 'Korea, Democratic People\'s Republic of',
'KR' => 'Korea, Republic of',
'KW' => 'Kuwait',
'KY' => 'Cayman Islands',
'KZ' => 'Kazakhstan',
'LA' => 'Lao People\'s Democratic Republic',
'LB' => 'Lebanon',
'LC' => 'Saint Lucia',
'LI' => 'Liechtenstein',
'LK' => 'Sri Lanka',
'LR' => 'Liberia',
'LS' => 'Lesotho',
'LT' => 'Lithuania',
'LU' => 'Luxembourg',
'LV' => 'Latvia',
'LY' => 'Libyan Arab Jamahiriya',
'MA' => 'Morocco',
'MC' => 'Monaco',
'MD' => 'Moldova, Republic of',
'MG' => 'Madagascar',
'MH' => 'Marshall Islands',
'MK' => 'Macedonia',
'ML' => 'Mali',
'MN' => 'Mongolia',
'MM' => 'Myanmar',
'MO' => 'Macau',
'MP' => 'Northern Mariana Islands',
'MQ' => 'Martinique',
'MR' => 'Mauritania',
'MS' => 'Monserrat',
'MT' => 'Malta',
'MU' => 'Mauritius',
'MV' => 'Maldives',
'MW' => 'Malawi',
'MX' => 'Mexico',
'MY' => 'Malaysia',
'MZ' => 'Mozambique',
'NA' => 'Namibia',
'NC' => 'New Caledonia',
'NE' => 'Niger',
'NF' => 'Norfolk Island',
'NG' => 'Nigeria',
'NI' => 'Nicaragua',
'NL' => 'Netherlands',
'NO' => 'Norway',
'NP' => 'Nepal',
'NR' => 'Nauru',
'NT' => 'Neutral Zone (no longer exists)',
'NU' => 'Niue',
'NZ' => 'New Zealand',
'OM' => 'Oman',
'PA' => 'Panama',
'PE' => 'Peru',
'PF' => 'French Polynesia',
'PG' => 'Papua New Guinea',
'PH' => 'Philippines',
'PK' => 'Pakistan',
'PL' => 'Poland',
'PM' => 'St. Pierre &amp; Miquelon',
'PN' => 'Pitcairn',
'PR' => 'Puerto Rico',
'PT' => 'Portugal',
'PW' => 'Palau',
'PY' => 'Paraguay',
'QA' => 'Qatar',
'RE' => 'R&#233;union',
'RO' => 'Romania',
'RS' => 'Serbia',
'RU' => 'Russian Federation',
'RW' => 'Rwanda',
'SA' => 'Saudi Arabia',
'SB' => 'Solomon Islands',
'SC' => 'Seychelles',
'SD' => 'Sudan',
'SE' => 'Sweden',
'SG' => 'Singapore',
'SH' => 'St. Helena',
'SI' => 'Slovenia',
'SJ' => 'Svalbard &amp; Jan Mayen Islands',
'SK' => 'Slovakia',
'SL' => 'Sierra Leone',
'SM' => 'San Marino',
'SN' => 'Senegal',
'SO' => 'Somalia',
'SR' => 'Suriname',
'ST' => 'Sao Tome &amp; Principe',
'SV' => 'El Salvador',
'SY' => 'Syrian Arab Republic',
'SZ' => 'Swaziland',
'TC' => 'Turks &amp; Caicos Islands',
'TD' => 'Chad',
'TF' => 'French Southern Territories',
'TG' => 'Togo',
'TH' => 'Thailand',
'TJ' => 'Tajikistan',
'TK' => 'Tokelau',
'TM' => 'Turkmenistan',
'TN' => 'Tunisia',
'TO' => 'Tonga',
'TP' => 'East Timor',
'TR' => 'Turkey',
'TT' => 'Trinidad &amp; Tobago',
'TV' => 'Tuvalu',
'TW' => 'Taiwan, Province of China',
'TZ' => 'Tanzania, United Republic of',
'UA' => 'Ukraine',
'UG' => 'Uganda',
'UK' => 'United Kingdom (Great Britain)',
'UM' => 'United States Minor Outlying Islands',
'US' => 'United States of America',
'UY' => 'Uruguay',
'UZ' => 'Uzbekistan',
'VA' => 'Vatican City State (Holy See)',
'VC' => 'St. Vincent &amp; the Grenadines',
'VE' => 'Venezuela',
'VG' => 'British Virgin Islands',
'VI' => 'United States Virgin Islands',
'VN' => 'Viet Nam',
'VU' => 'Vanuatu',
'WF' => 'Wallis &amp; Futuna Islands',
'WS' => 'Samoa',
'YD' => 'Democratic Yemen (no longer exists)',
'YE' => 'Yemen',
'YT' => 'Mayotte',
'YU' => 'Yugoslavia',
'ZA' => 'South Africa',
'ZM' => 'Zambia',
'ZR' => 'Zaire',
'ZW' => 'Zimbabwe'
);
asort($countries);

function formCountrySelect($c,$n,$d){
global $countries;
$k = array_search($d,$countries);
$s = new FormSelect($c,$n,$countries,$k);
$s->values = 'k';
return $s;
}

//
// Функция formCurrencySelect връща обект от тип FormSelect
// представляващ падащ списък за избор на валута
// При необходимост от допълване на списъка, виж http://www.xe.com/iso4217.php

function formCurrencySelect($c,$n,$d = 0){
global $currency;
$k = array_search($d, $currency);
$s = new FormSelect($c, $n, $currency, $k);
$s->values = 'k';
return $s;
}

//
// Клас показващ html код на елемент за съставяне на списък от елементи, които се избират от друг списък
//

class FormChooser{

public $caption = '';
public $name = '';
public $value = '';
public $js = '';

private $l1 = null;
private $l2 = null;

function __construct($c, $n, $sp, $v = ''){
$this->caption = $c;
$this->name = $n;
$this->value = $v;
$c = array();
if ($v){
  $a = explode(',', $v);
  for($i=1; $i<count($a)-1; $i++) $c[$sp[$a[$i]]] = $a[$i];
}
$this->l1 =  new FormSelect('', '', $c);
$this->l1->js = ' multiple="multiple" size="'.count($sp).'" id="formChoices"';
$this->l2 =  new FormSelect('', '', $sp);
$this->l2->js = ' multiple="multiple" size="'.count($sp).'" onclick="chooserClicked();" id="formChooser"';
}

function html($it){
global $page_header;
$page_header .= '<script>
function chooserChosen(t){
var l = document.getElementById("formChoices");
for(var i=0; i<l.length; i++){
  if (t==l.options[i].value) return true;
}
return false;
}
function chooserClicked(){
var l = document.getElementById("formChooser");
var i = l.selectedIndex;
var t = l.options[i].text;
if (!chooserChosen(t)){
  var o = document.createElement("option");
  o.text = t.substring(0,4);
  o.value = t;
  document.getElementById("formChoices").appendChild(o);
}
}
function chooserClear(){
var l = document.getElementById("formChoices");
for(var i=l.length-1; i>=0; i--){
  l.removeChild(l.options[i]);
}
}
function chooserMakeValue(){
var l = document.getElementById("formChoices");
var r = "";
for(var i=0; i<l.length; i++) if (l.options[i].text) r = r + "," + l.options[i].text;
if (r) r = r + ",";
var v = document.getElementById("chooserValue");
v.value = r;
return v;
}
function chooserSubmit(){
var v = chooserMakeValue();
v.form.submit();
}
</script>';
$rz = '';
$dsbl = lock_form_fields();// die("$dsbl");
if ($it) $rz .= '<tr><th>';
$rz .= $this->caption;
if ($it) $rz .= '</th><td>';
$rz .= '<input name="'.$this->name.'" type="hidden" value="'.$this->value.'" id="chooserValue">
<table style="margin-top:0;"><tr><td style="text-align:center; width:auto;">
'.encode('Избрани').'<br>
'.$this->l1->html(false).'</td><td style="text-align:center; width:auto;">
<input type="button" value="'.encode('Изчистване').'" onclick="chooserClear();"'.$dsbl.'><br>
<!--input type="button" value="Нагоре"><br>
<input type="button" value="Надолу"><br-->
</td><td>
'.encode('Възможности за избиране').'<br>
'.$this->l2->html(false).'</td></tr></table>';
if ($it) $rz .= '</td></tr>';
return $rz;
}

}

?>

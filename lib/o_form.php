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
// $tx - текст, който се показва между <form> тага и първи€ елемент на формата

function __construct($n,$at=true,$tx = ''){
$this->name = $n;
$this->astable = $at;
if ($at){ if ($tx) $this->text = "<tr><td colspan=\"2\">$tx</td></tr>"; }
else $this->text = $tx;
}

function add_input($in){
$this->ins[] = $in;
$this->action = $_SERVER['REQUEST_URI'];
}

public function html(){
$js = '';
$js1 = 'function ifNotEmpty_'.$this->name.'(){
var f = document.forms["'.$this->name.'"];
var l = f.length;
var r = 1;
for(i=0;i<l-1;i++) r = r*f.elements[i].value.length;
if (r) f.submit(); else alert("'.translate('fillin_all').'");
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
public $max_file_size = '5000000';
public $textAfter = '';

function __construct($c,$n,$t,$v = '',$ta = ''){
$this->caption = $c;
$this->name = $n;
$this->type = $t;
$this->value = $v;
$this->textAfter = $ta;
}

public function set_event($e,$js){
$this->js = " $e=\"$js\"";
}

public function html($it){
$rz = '';
if (!$it) $rz .= "$this->caption <input type=\"$this->type\" name=\"$this->name\"";
else $rz .= "<tr><th>$this->caption </th><td><input type=\"$this->type\" name=\"$this->name\"";
if ($this->value) $rz .= " value=\"$this->value\"";
if ($this->size) $rz .= " size=\"$this->size\"";
if ($this->id) $rz .= " id=\"$this->id\"";
if ($this->js) $rz .= " $this->js";
$rz .= "$this->checked>";
if ($this->type=='file') $rz .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->max_file_size.'">';
if ($this->textAfter) $rz .= ' '.$this->textAfter;
if (!$it) $rz .= "\n";
else $rz .= "</td></tr>\n";
return $rz;
}

}

//----- FormTextArea ------------

class FormTextArea {

public $caption = '';
public $name = '';
public $cols = '';
public $rows = '';
public $text = '';
public $js = '';

private $ckbutton = '';

function __construct($c,$n,$cl=100,$r=10,$t=''){
global $mod_pth, $page_header;
$this->caption = $c;
$this->name = $n;
$this->cols = $cl;
$this->rows = $r;
$this->text = $t;
// ƒомав€не на бутон за зареждане на CKEditor.
$ckp = stored_value('ckeditor_file',$mod_pth.'ckeditor/ckeditor.js');
$cka = $_SERVER['DOCUMENT_ROOT'].$ckp;
if (file_exists($cka)){
  $page_header .= "   <script type=\"text/javascript\" src=\"$ckp\"></script>\n";
  $this->ckbutton = '<input type="button" value="CKEditor" onclick="CKEDITOR.replace(\''.$this->name.'\');"><br>';
}
}

public function html($it){
$rz = "$this->ckbutton<textarea name=\"$this->name\" id=\"$this->name\" cols=\"$this->cols\" rows=\"$this->rows\">$this->text</textarea>";
if (!$it) $rz = "$this->caption $rz<br>\n";
else $rz = "<tr>\n<th>$this->caption</th>\n<td>$rz</td>\n</tr>";
return $rz;
}


}

//----- FormSelect ------------

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
if (!$it) $rz = "$this->caption <select name=\"$this->name\">";
else $rz = "<tr>
<th>$this->caption</th>
<td><select name=\"$this->name\">\n";
foreach($this->options as $i => $v){
  $sl = '';
  if ($i==$this->selected) $sl = ' selected';
  switch($this->values){
  case 'v': $rz .= "<option value=\"$v\"$sl>$v\n"; break;
  case 'k': $rz .= "<option value=\"$i\"$sl>$v\n"; break;
  }
}
$rz .= "</select>";
if ($it) $rz .= "</td>
</tr>";
return $rz;
}


}

//----- FormSelect ------------

class FormReCaptcha{

public $caption = '';
public $public_key = '';
public $js = '';

function __construct($c,$pk){
$this->caption = $c;
$this->public_key = $pk;
}

public function html($it){
global $language;
$rz = '';
if ($it) $rz = "<tr>\n<th>";
$rz .= $this->caption;
if ($it) $rz .= "</th>\n<td>"; else $rz .= " ";
$rz .= '<script type="text/javascript"
  src="http://www.google.com/recaptcha/api/challenge?k='.$this->public_key.'&amp;hl='.$language.'">
</script>
<noscript>
  <iframe src="http://www.google.com/recaptcha/api/noscript?k='.$this->public_key.'&hl='.$language.'"
    height="300" width="500" frameborder="0"></iframe><br>
  <textarea name="recaptcha_challenge_field" rows="3" cols="40">
  </textarea>
  <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>';
if ($it) $rz .= "</td>\n<tr>\n"; else $rz .= "<br>\n";
return $rz;
}

} //class FormSelect

?>

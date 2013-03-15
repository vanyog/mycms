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
public $action = ''; //$_SERVER['REQUEST_URI'];
private $ins = array();

function __construct($n){
$this->name = $n;
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
if (r) f.submit(); else alert("Please, fill in all boxes");
}
';
$rz = "<form name=\"$this->name\" method=\"$this->method\" action=\"$this->action\">\n";
if ($this->astable) $rz .= "<table>\n"; 
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
public $id = '';
public $js = '';

function __construct($c,$n,$t,$v = ''){
$this->caption = $c;
$this->name = $n;
$this->type = $t;
$this->value = $v;
}

public function set_event($e,$js){
$this->js = " $e=\"$js\"";
}

public function html($it){
if (!$it) $rz = "$this->caption <input type=\"$this->type\" name=\"$this->name\"";
else $rz = "<tr><th>$this->caption </th><td><input type=\"$this->type\" name=\"$this->name\"";
if ($this->value) $rz .= " value=\"$this->value\"";
if ($this->id) $rz .= " id=\"$this->id\"";
if ($this->js) $rz .= " $this->js";
if (!$it) $rz .= ">\n";
else $rz .= "></td></tr>\n";
return $rz;
}

}

?>

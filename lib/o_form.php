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

$f = new HTMLForm('f1');
$i = new FormInput('','proba','button','aaa');
$i->set_event("onclick","alert('aaa')");
$f->add_input($i);
$f->astable = false;
echo $f->html();


//----- HTMLForm ------------

class HTMLForm {

public $name = '';
public $method = 'post';
public $astable = true;
private $ins = array();

function __construct($n){
$this->name = $n;
}

function add_input($in){
$this->ins[] = $in;
}

public function html(){
$rz = "<form name=\"$this->name\" method=\"$this->method\">\n";
if ($this->astable) $rz .= "<table>\n"; 
foreach($this->ins as $i) $rz .= $i->html($this->astable);
if ($this->astable) $rz .= "</table>\n"; 
$rz .= "</form>\n";
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
if (!$it) $rz = "$this->caption <input type=\"$this->type\" name=\"This->name\"";
else $rz = "<tr><td>$this->caption </td><td><input type=\"$this->type\" name=\"$this->name\"";
if ($this->value) $rz .= " value=\"$this->value\"";
if ($this->id) $rz .= " id=\"$this->id\"";
if ($this->js) $rz .= " $this->js";
if (!$it) $rz .= ">\n";
else $rz .= "></td></tr>\n";
return $rz;
}

}

?>

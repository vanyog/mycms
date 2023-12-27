<?php

/*
VanyoG CMS - a simple Content Management System
Copyright (C) 2023  Vanyo Georgiev <info@vanyog.com>

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

// Тази функция проверява дали с GET e изпратена променлива $k със стойност $v
// или има бисквитка с име $k и стойност $v.
// Ако това е така връща истина.

// Ако има стойност $_GET[$k] се установява бисквитка с тази стойност.

function mysetcookie($k,$v){
$rz = false;// var_dump($_COOKIE['cookies_accept']); die();
if(!isset($_COOKIE['cookies_accept']) || ($_COOKIE['cookies_accept']!='yes')){
   if (isset($_GET['cookies_accept']) && ($_GET['cookies_accept']=='yes') ){
       $rz = setcookie('cookies_accept', 'yes', 
                   ['expires'=>time()+60*60*24*30, 'path'=>'/', 'SameSite'=>'Strict'] );
   } 
   else {
       if($_SERVER['PHP_SELF']==$GLOBALS['pth']."index.php"){// die($_SERVER['PHP_SELF']."  aa");
          $GLOBALS['need_cookie']='true';
          return $rz;
       }
       $u = $_SERVER['REQUEST_URI'];
       if(!(strpos($u, '?')===false)) $u .= '&';
       $u .= '?cookies_accept=yes';
       die("<p>To make administration convenient, this site uses cookies. ".
           "Please confirm that you agree to have cookies from this site stored on your device.</p>".
           '<p><a href="'.$u.'">Well I agree</a>.');
   }
}
$rz = setcookie($k, $v, ['expires'=>time()+60*60*24*30, 'path'=>'/', 'SameSite'=>'Strict'] );
return $rz;
}

?>

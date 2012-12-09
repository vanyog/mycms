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

function set_query_var($n,$v){
$r = $_GET;
$r[$n] = $v;
return http_parse_query($r);
}

function http_parse_query( $array = NULL, $convention = '%s' ){
if( count( $array ) == 0 ){
  return '';
} else {
  if( function_exists( 'http_build_query' ) ){
    $query = http_build_query( $array );
  } else {
    $query = '';
    foreach( $array as $key => $value ){
      if( is_array( $value ) ){
        $new_convention = sprintf( $convention, $key ) . '[%s]';
        $query .= http_parse_query( $value, $new_convention );
      } else {
        $key = urlencode( $key );
        $value = urlencode( $value );
        $query .= sprintf( $convention, $key ) . "=$value&";
      }
    }
  }
  return $query;
}
}

?>

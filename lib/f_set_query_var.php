<?php
// Copyright: Vanyo Georgiev info@vanyog.com

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

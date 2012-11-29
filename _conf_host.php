<?php

// Променливи и фуркции, които дават информация за хостинга и администрирането

$web_host = 'yoursite.org'; // Домейн на сайта.

$local_host = 'test'; // Локален домейн на сайта, който не е достъпен през Интернет.
                      // Използва се, когато се създава пробно локално копие на сайта.

$phpmyadmin = 'http://yoursite.org/phpmyadmin'; // Адрес на phpMyAdmin за отдалечения сървър

// Връща истина, ако сайтът се намира на локален сървър.
function is_local(){
global $local_host;
return $local_host==$_SERVER['HTTP_HOST'];
}

include($idir."lib/f_query_or_cookie.php");

// Функцията in_edit_mode() връща истина ако сайтът е в режим на редактиране
// В такъв режим се показват линкове за редактиране на надписи, текстове, менюта и др.
// Сайтът е в режим на редактиране ако:
// - получи бисквитка im=admin
// - получи $_GET['im']=='admin'

function in_edit_mode(){
global $edit_name, $edit_value;
return query_or_cookie($edit_name,$edit_value);
}

?>

<?php

// Този файл е предназначен да се извиква последен с include("build_page.php");
// от други php файлове, за да покаже съставените в тях страници.
// Използва се само за генериране на страниците за администриране от директория $adm_pth (виж conf_paths.php).
// Преди извикването му съдържанието на страницата трябва да е присвоено на променливата $page_content.
//
// Ако е необподимо се присвояват стойности и на:
//
// $page_title - заглавието на страницата
// $page_header - допълнителни тагове, които се вмъкнат между <head></head>
// $body_adds - допълнителни атрибути на <body> тага

// Брояч на презарежданията на страниците
//include("count-visits.php");

$idir = dirname(dirname(__FILE__)).'/';

include_once($idir.'conf_paths.php');

if (!isset($page_title)) $page_title = '';
if (!isset($page_header)) $page_header = '';
if (!isset($body_adds)) $body_adds = '';

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
   <title>'.$page_title.'</title>
   <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
   <link href="'.$pth.'style.css" rel="stylesheet" type="text/css">
   '.$page_header.'
</head>

<body'.$body_adds.'>
'.$page_content.
//visit_count().  // Брояч на презарежданията на страниците
'
</body>
</html>
';

?>

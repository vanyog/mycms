<?php
/*
MyCMS - a simple Content Management System
Copyright (C) 2013  Vanyo Georgiev <info@vanyog.com>

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

// Помощен скрипт към translate_content.php ', чрез който се получава превод от Google Cloud

error_reporting(E_ALL); ini_set('display_errors',1);

include_once('conf_manage.php');
include_once($idir.'lib/translation.php');
include_once($idir.'lib/f_stored_value.php');

$gak = stored_value('GoogleTranslateAPIkey','');

if(!$gak) die("No GoogleTranslateAPIkey is set in options table.");
if(empty($_GET['text'])) die("No text is send for translation.");
if(empty($_GET['lang'])) die("To which language to translate?");
if(!array_key_exists($_GET['lang'],$languages)) die("Language is not in the site langage list.");

require $idir.'_google-cloud-translate/vendor/autoload.php';

use Google\Cloud\Translate\V2\TranslateClient;

$translate = new TranslateClient([
    'key' => $gak
]);

$result = $translate->translate($_GET['text'], [
    'target' => $_GET['lang']
]);

die($result['text']);
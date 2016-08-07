<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
define("NEWS_SEARCH", true);

$config['NS_ALLOW_ANON'] = 1;
$config['NS_MAX_S_TEXT'] = 50;
$config['NS_MIN_S_TEXT'] = 3;
$config['NS_RESULT_LIMIT'] = 10;
$config['NS_TAGS_SUPPORT'] = 1;
$config['NS_TAGS_SZ_LIMIT'] = 256;
//$config[''] = ;
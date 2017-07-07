<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
define("NEWS_SEARCH", true);

$cfg['NS_ALLOW_ANON'] = 1;
$cfg['NS_MAX_S_TEXT'] = 50;
$cfg['NS_MIN_S_TEXT'] = 3;
$cfg['NS_RESULT_LIMIT'] = 10;
$cfg['NS_TAGS_SUPPORT'] = 1;
$cfg['NS_TAGS_SZ_LIMIT'] = 256;
//$cfg[''] = ;
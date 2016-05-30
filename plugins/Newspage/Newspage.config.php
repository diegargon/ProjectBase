<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

define("NEWSPAGE_DEBUG", true);
if('ACL') {
$config['NEWS_HIDDE_PREVIEW_BY_ACL'] = 0;
}

$config['LAYOUT_SWITCH'] = 1;
$config['NEWS_BODY_STYLES'] = 2;
$config['NEWS_SUBMIT_ANON'] = 1;
$config['NEWS_SUBMIT_REGISTERED'] = 1;
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
$config['NEWS_TITLE_MAX_LENGHT'] = 130;
$config['NEWS_TITLE_MIN_LENGHT'] = 10;
$config['NEWS_LEAD_MAX_LENGHT'] = 600;
$config['NEWS_LEAD_MIN_LENGHT'] = 30;
$config['NEWS_TEXT_MAX_LENGHT'] = 3000;
$config['NEWS_TEXT_MIN_LENGHT'] = 200;
$config['NEWS_MEDIA_MAX_LENGHT'] = 200;
$config['NEWS_MEDIA_MIN_LENGHT'] = 10;
$config['NEWS_MODERATION'] = 1;
$config['NEWS_NUM_LIST_MOD'] = 100;
$config['NEWS_MAIN_MEDIA_REQUIRED'] = 1;
$config['NEWS_SELECTED_FRONTPAGE'] = 1;
//$config['NEWS_SUBMIT_URL'] = 1;
//$config['NEWS_PRIMARY_SOURCE'] = 1;
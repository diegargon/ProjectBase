<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

define("NEWSPAGE_DEBUG", true);
if ('ACL') {
    $config['NEWS_HIDDE_PREVIEW_BY_ACL'] = 0;
}
$config['LAYOUT_SWITCH'] = 1;
$config['NEWS_BODY_STYLES'] = 3;
$config['NEWS_SUBMIT_ANON'] = 1;
$config['NEWS_SUBMIT_REGISTERED'] = 1;
$config['NEWS_TITLE_MAX_LENGHT'] = 130;
$config['NEWS_TITLE_MIN_LENGHT'] = 10;
$config['NEWS_LEAD_MAX_LENGHT'] = 600;
$config['NEWS_LEAD_MIN_LENGHT'] = 30;
$config['NEWS_TEXT_MAX_LENGHT'] = 20000; //utf8 "text" about 21000
$config['NEWS_TEXT_MIN_LENGHT'] = 200;
$config['NEWS_MODERATION'] = 1;
$config['NEWS_NUM_LIST_MOD'] = 100;
$config['NEWS_SELECTED_FRONTPAGE'] = 1;
$config['NEWS_ACL_PREVIEW_CHECK'] = 0;
$config['NEWS_SOURCE'] = 1;
$config['NEWS_RELATED'] = 1;
$config['NEWS_ANON_TRANSLATE'] = 1;
$config['NEWS_TRANSLATE_REGISTERED'] = 1;
$config['NEWS_TRANSLATOR_CAN_EDIT'] = 1;
$config['NEWS_AUTHOR_CAN_EDIT'] = 1;
$config['NEWS_LINK_MAX_LENGHT'] = 200;
$config['NEWS_LINK_MIN_LENGHT'] = 10;
$config['NEWS_MULTIPLE_PAGES'] = 1;
$config['NEWS_PAGER_MAX'] = 4; //Pair >= 4
$config['NEWS_STATS'] = 1;
$config['NEWS_ADVANCED_STATS'] = 1;
$config['NEWS_META_OPENGRAPH'] = 1;
$config['NEWS_PORTAL_COLS'] = 3;
$config['NEWS_PORTAL_FEATURED'] = 1;
$config['NEWS_PORTAL_COL1_CONTENT'] = "frontpage";
$config['NEWS_PORTAL_COL1_CONTENT_CATS'] = "1,2";
$config['NEWS_PORTAL_COL1_CONTENT_LIMIT'] = 10;
$config['NEWS_PORTAL_COL2_CONTENT'] = "frontpage";
$config['NEWS_PORTAL_COL2_CONTENT_CATS'] = "2,1";
$config['NEWS_PORTAL_COL2_CONTENT_LIMIT'] = 10;
$config['NEWS_PORTAL_COL3_CONTENT'] = "backpage_h";
$config['NEWS_PORTAL_COL3_CONTENT_CATS'] = "0";
$config['NEWS_PORTAL_COL3_CONTENT_LIMIT'] = 10;
$config['NEWS_PARSER_ALLOW_IMG'] = 1;
$config['NEWS_PARSER_ALLOW_URL'] = 1;
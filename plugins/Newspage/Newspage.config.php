<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

define("NEWSPAGE_DEBUG", true);
defined('ACL') ? $config['NEWS_HIDDE_PREVIEW_BY_ACL'] = 0 : false;

$config['LAYOUT_SWITCH'] = 1;
$config['NEWS_SUBMIT_ANON'] = 1;
$config['NEWS_SUBMIT_REGISTERED'] = 1;
$config['NEWS_TITLE_MAX_LENGHT'] = 130;
$config['NEWS_TITLE_MIN_LENGHT'] = 10;
$config['NEWS_LEAD_MAX_LENGHT'] = 300;
$config['NEWS_LEAD_MIN_LENGHT'] = 40;
$config['NEWS_TEXT_MAX_LENGHT'] = 20000; //utf8 "text" about 21000
$config['NEWS_TEXT_MIN_LENGHT'] = 200;
$config['NEWS_MODERATION'] = 0;
$config['NEWS_NUM_LIST_MOD'] = 100;
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
//BEGIN PORTAL CONFIG
$config['NEWS_PORTAL_STYLES'] = 3;
$config['NEWS_PORTAL_COLS'] = 3;
$config['NEWS_PORTAL_FEATURED'] = 1;
$config['NEWS_PORTAL_FEATURED_LIMIT'] = 4;
$config['NEWS_PORTAL_COL1_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 9,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$config['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 5,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$config['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 2,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$config['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 0,
    "frontpage" => 0,
    "cathead" => 1,
    "headlines" => 1,
    "limit" => 10,
);
$config['NEWS_PORTAL_COL3_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 0,
    "frontpage" => 0,
    "cathead" => 1,
    "limit" => 10,
);
//END PORTAL CONFIG
//BEGIN SECTION CONFIG
$config['NEWS_SECTION_STYLES'] = 1;
$config['NEWS_SECTION_COLS'] = 3;
$config['NEWS_SECTION_FEATURED'] = 1;
//END SECTION CONFIG
$config['NEWS_PARSER_ALLOW_IMG'] = 1;
$config['NEWS_PARSER_ALLOW_URL'] = 1;
$config['NEWS_BREADCRUMB'] = 1;
$config['NEWS_BREADCRUMB_SEPARATOR'] = "";
$config['NEWS_BACKPAGE_SECTION'] = 0;
$config['NEWS_FB_APPID'] = "1481492868545684";
$config['NEWS_PAGE_SIDENEWS'] = 1;

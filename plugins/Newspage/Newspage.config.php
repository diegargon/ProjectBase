<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

define("NEWSPAGE_DEBUG", true);
defined('ACL') ? $cfg['NEWS_HIDDE_PREVIEW_BY_ACL'] = 0 : false;

$cfg['LAYOUT_SWITCH'] = 1;
$cfg['NEWS_SUBMIT_ANON'] = 1;
$cfg['NEWS_SUBMIT_REGISTERED'] = 1;
$cfg['NEWS_TITLE_MAX_LENGHT'] = 130;
$cfg['NEWS_TITLE_MIN_LENGHT'] = 10;
$cfg['NEWS_LEAD_MAX_LENGHT'] = 300;
$cfg['NEWS_LEAD_MIN_LENGHT'] = 40;
$cfg['NEWS_TEXT_MAX_LENGHT'] = 20000; //utf8 "text" about 21000
$cfg['NEWS_TEXT_MIN_LENGHT'] = 200;
$cfg['NEWS_MODERATION'] = 0;
$cfg['NEWS_NUM_LIST_MOD'] = 100;
$cfg['NEWS_ACL_PREVIEW_CHECK'] = 0;
$cfg['NEWS_SOURCE'] = 1;
$cfg['NEWS_RELATED'] = 1;
$cfg['NEWS_ANON_TRANSLATE'] = 1;
$cfg['NEWS_TRANSLATE_REGISTERED'] = 1;
$cfg['NEWS_TRANSLATOR_CAN_EDIT'] = 1;
$cfg['NEWS_AUTHOR_CAN_EDIT'] = 1;
$cfg['NEWS_LINK_MAX_LENGHT'] = 200;
$cfg['NEWS_LINK_MIN_LENGHT'] = 10;
$cfg['NEWS_MULTIPLE_PAGES'] = 1;
$cfg['NEWS_PAGER_MAX'] = 4; //Pair >= 4
$cfg['NEWS_STATS'] = 1;
$cfg['NEWS_ADVANCED_STATS'] = 1;
$cfg['NEWS_META_OPENGRAPH'] = 1;
//BEGIN PORTAL CONFIG
$cfg['NEWS_PORTAL_STYLES'] = 3;
$cfg['NEWS_PORTAL_COLS'] = 3;
$cfg['NEWS_PORTAL_FEATURED'] = 1;
$cfg['NEWS_PORTAL_FEATURED_LIMIT'] = 4;
$cfg['NEWS_PORTAL_COL1_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 9,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$cfg['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 5,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$cfg['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 2,
    "frontpage" => 1,
    "cathead" => 1,
    "excl_portal_featured" => 1,
    "limit" => 10,
);
$cfg['NEWS_PORTAL_COL2_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 0,
    "frontpage" => 0,
    "cathead" => 1,
    "headlines" => 1,
    "limit" => 10,
);
$cfg['NEWS_PORTAL_COL3_CONTENT'][] = array(
    "func" => "get_news",
    "category" => 0,
    "frontpage" => 0,
    "cathead" => 1,
    "limit" => 10,
);
//END PORTAL CONFIG
//BEGIN SECTION CONFIG
$cfg['NEWS_SECTION_STYLES'] = 1;
$cfg['NEWS_SECTION_COLS'] = 3;
$cfg['NEWS_SECTION_FEATURED'] = 1;
//END SECTION CONFIG
$cfg['NEWS_PARSER_ALLOW_IMG'] = 1;
$cfg['NEWS_PARSER_ALLOW_URL'] = 1;
$cfg['NEWS_BREADCRUMB'] = 1;
$cfg['NEWS_BREADCRUMB_SEPARATOR'] = "";
$cfg['NEWS_BACKPAGE_SECTION'] = 0;
$cfg['NEWS_FB_APPID'] = "1481492868545684";
$cfg['NEWS_PAGE_SIDENEWS'] = 1;
$cfg['NEWS_CAT_SEPARATOR'] = ".";
$cfg['NEWS_MENU_BACK_SYMBOL'] = "[<<]";

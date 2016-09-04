<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
require_once 'includes/news_portal.php';
require_once 'includes/news_section.inc.php';

do_action("section_page_begin");
do_action("common_web_structure");

$tpl->addto_tplvar("ADD_HEADER_END", cat_menu());

if (empty($category_name = S_GET_TEXT_UTF8("section")) || preg_match("/\s+/", $category_name)) {
    return news_error_msg("L_NEWS_E_SEC_NOEXISTS");
}
if (!$category = getCatIDbyName($category_name)) {
    return news_error_msg("L_NEWS_E_SEC_NOEXISTS");
}
if (defined('MULTILANG')) {
    $lang_id = $ml->iso_to_id($config['WEB_LANG']);
} else {
    $lang_id = $config['WEB_LANG_ID'];
}

$section_data['featured'] = get_news(array("category" => $category, "featured" => 1, "limit" => 1, "get_childs" => 1));
$section_data['col1_articles'] = get_news(array("category" => $category, "frontpage" => 1, "limit" => 10, "excl_firstcat_featured" => 1, "get_childs" => 1));
$section_data['col2_articles'] = get_news(array("category" => $category, "frontpage" => 0, "limit" => 10, "excl_firstcat_featured" => 1, "get_childs" => 1));

$news_section_layout = "news_section_style1"; //TODO MULTIPLE

$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "$news_section_layout", $section_data));

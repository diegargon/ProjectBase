<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;
require_once 'includes/news_portal.php';

do_action("section_page_begin");
do_action("common_web_structure");

if (empty($category_list = S_GET_TEXT_UTF8("section")) || preg_match("/\s+/", $category_list)) {
    return news_error_msg("L_NEWS_E_SEC_NOEXISTS");
}
if (!$category = $ctgs->getCatIDbyName_path("Newspage", $category_list)) {
    return news_error_msg("L_NEWS_E_SEC_NOEXISTS");
}
if (defined('MULTILANG')) {
    $lang_id = $ml->getSessionLangId();
} else {
    $lang_id = $config['WEB_LANG_ID'];
}

//HEAD MOD
$config['PAGE_TITLE'] = $config['WEB_NAME'] . ": " . $category_list;
$config['PAGE_DESC'] = $config['WEB_NAME'] . ": " . $category_list;
//END HEAD MOD

$section_data['featured'] = get_news( [ "category" => $category, "featured" => 1, "limit" => 1, "get_childs" => 1]);
$section_data['col1_articles'] = get_news( ["category" => $category, "frontpage" => 1, "limit" => 10, "excl_firstcat_featured" => 1, "get_childs" => 1]);
$section_data['col2_articles'] = get_news( ["category" => $category, "frontpage" => 0, "limit" => 10, "excl_firstcat_featured" => 1, "get_childs" => 1]);

$news_section_layout = "news_section_style1"; //TODO MULTIPLE

$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "$news_section_layout", $section_data));

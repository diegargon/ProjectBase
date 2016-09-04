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
/*
  if (defined('MULTILANG')) { //TODO CHECK if we have lang_id in url
  $lang_id = $ml->iso_to_id($config['WEB_LANG']);
  } else {
  $lang_id = $config['WEB_LANG_ID'];
  }

  $cat_id = getCatIDbyName($category);
  $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id", "cid" => "$cat_id"), "LIMIT 1");
  $cat_actual = $db->fetch($query);
 */

$section_data['col1_articles'] = $section_data['col2_articles'] = $section_data['featured'] = "";

//TODO: Feature if cat its father not get most recent of actual and childs
$section_data['featured'] = get_news(array("category" => $category, "featured" => 1, "limit" => 1), "ORDER BY featured_date DESC");

//TODO: modify get_news  for get "father" => $category and when category its father(0) get childs news
$section_data['col1_articles'] = get_news(array("category" => $category, "frontpage" => 1, "limit" => 10, "excl_firstcat_featured" => 1));
$section_data['col2_articles'] = get_news(array("category" => $category, "frontpage" => 0, "limit" => 10, "excl_firstcat_featured" => 1));
$news_section_layout = "news_section_style1"; //TODO MULTIPLE

$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "$news_section_layout", $section_data));

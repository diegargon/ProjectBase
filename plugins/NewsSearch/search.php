<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("NewsSearch");
require_once 'plugins/NewsSearch/includes/NewsSearchPage.inc.php';

if (!empty($_POST['searchText'])) {
    $searchText = S_POST_TEXT_UTF8("searchText", $config['NS_MAX_S_TEXT'], $config['NS_MIN_S_TEXT']);

    if (empty($searchText)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $searchText = $db->escape_strip($searchText);
    $where_ary['lang'] = $config['WEB_LANG'];
    $config['NEWS_MODERATION'] ? $where_ary['moderation'] = 0 : null;

    $query = $db->search("news", "title lead text", $searchText, $where_ary, " LIMIT {$config['NS_RESULT_LIMIT']} ");

    NS_build_result_page($query);
}

if (!empty($_GET["searchTag"])) {
    $searchTag = S_GET_TEXT_UTF8("searchTag", $config['NS_TAGS_SZ_LIMIT'], $config['NS_MIN_S_TEXT']);

    if (empty($searchTag)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $searchTag = $db->escape_strip($searchTag);
    $where_ary['lang'] = $config['WEB_LANG'];
    $config['NEWS_MODERATION'] ? $where_ary['moderation'] = 0 : null;
    $query = $db->search("news", "tags", $searchTag, $where_ary, " LIMIT {$config['NS_RESULT_LIMIT']} ");
    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
}
<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

plugin_start("NewsSearch");
require_once 'plugins/NewsSearch/includes/NewsSearchPage.inc.php';

if (!empty($_POST['searchText'])) {
    $searchText = S_POST_TEXT_UTF8("searchText", $config['NS_MAX_S_TEXT'], $config['NS_MIN_S_TEXT']);
    if (empty($searchText)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $searchText = $db->escape_strip($searchText);
    $query = $db->search("news", "title lead text", $searchText, array("lang" => $config['WEB_LANG']), " LIMIT {$config['NS_RESULT_LIMIT']} ");

    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
}

if (!empty($_GET["searchTag"])) {
    $searchTag = S_GET_TEXT_UTF8("searchTag", $config['NS_TAGS_SZ_LIMIT'], $config['NS_MIN_S_TEXT']);

    if (empty($searchTag)) {
        $msg['MSG'] = "L_NS_SEARCH_ERROR";
        NS_msgbox($msg);
    }
    $searchTag = $db->escape_strip($searchTag);
    $query = $db->search("news", "tags", $searchTag, array("lang" => $config['WEB_LANG']), " LIMIT {$config['NS_RESULT_LIMIT']} ");
    if ($query) {
        NS_build_result_page($query);
    } else {
        return false;
    }
}
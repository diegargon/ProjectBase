<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsSearch_init() { 
    global $tpl, $config;
    print_debug("NewsSearch initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsSearch");

    if ($config['L_NS_ALLOW_ANON'] == 0) {
        return false;
    }
    $tpl->getCSS_filePath("NewsSearch");
    register_action("nav_element", "NS_basicSearchbox", 5);
    register_action("news_index_begin", "NS_Searchbox_Capture");
    register_action("news_page_begin", "NS_Searchbox_Capture");
}

function NS_basicSearchbox($data = null) {
    global $tpl;
    return $search_box = $tpl->getTPL_file("NewsSearch", "NewsSearchBarbox", $data);
}

function NS_Searchbox_Capture() {
    global $config, $db, $tpl;

    if (!empty($_POST['searchText'])) {
        $searchText = S_POST_TEXT_UTF8("searchText", $config['L_NS_MAX_S_TEXT'], $config['L_NS_MIN_S_TEXT']);
        if (empty($searchText)) {
            $msg['MSG'] = "L_NS_SEARCH_ERROR";
            NS_msgbox($msg);
        }
        $searchText = $db->escape_strip($searchText);
        $query = $db->search("news", "title lead text", $searchText, array("lang" => $config['WEB_LANG']), " LIMIT {$config['L_NS_RESULT_LIMIT']} " );

        if(!$query) { return false; }
        $content = "";

        if( ($num_rows = $db->num_rows($query)) > 0) {
            $counter = 0;
            do_action("common_web_structure");
            while ($result = $db->fetch($query)) {
                $counter == 0 ? $result['TPL_FIRST'] = 1 : false;
                $counter == ($num_rows -1 )? $result['TPL_LAST'] = 1 : false;
                $counter++;
                if ($config['FRIENDLY_URL']) {
                    $friendly_title = news_friendly_title($result['title']);
                    $result['url'] = "/{$result['lang']}/news/{$result['nid']}/{$result['page']}/$friendly_title";
                } else {
                    $result['url'] = "/newspage.php?nid={$result['nid']}&lang={$result['lang']}&page={$result['page']}";
                }
                $content .= $tpl->getTPL_file("NewsSearch", "NewsSearch-results", $result);
            }
            $tpl->addto_tplvar("ADD_TO_BODY", $content);
            NS_finalice_page();
        } else {
            $msg['MSG'] = "L_NS_NORESULT";
            NS_msgbox($msg);
        }
    }
}
function NS_finalice_page() {
    global $tpl;

    $tpl->build_page();
    do_action("finalize");
    exit();
}

function NS_msgbox($msg) {
    do_action("common_web_structure");
    $msg['title'] = "L_NS_SEARCH";
    $msg['MSG'] = $msg['MSG'];
    do_action("message_box", $msg);
    NS_finalice_page();
}
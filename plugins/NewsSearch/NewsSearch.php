<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsSearch_init() { 
    global $tpl, $config;
    print_debug("NewsSearch initiated", "PLUGIN_LOAD");

    includePluginFiles("NewsSearch");

    if ($config['NS_ALLOW_ANON'] == 0) {
        return false;
    }
    $tpl->getCSS_filePath("NewsSearch");
    register_action("nav_element", "NS_basicSearchbox", 5);
    register_action("news_index_begin", "NS_Search_Capture");
    register_action("news_page_begin", "NS_Search_Capture");

    /* TAGS */
    if ($config['NS_TAGS_SUPPORT']) {
        register_action("news_new_form_add", "NS_tag_add_form");
        register_action("news_mod_submit_insert", "NS_news_mod_insert");
        register_action("news_show_page", "NS_news_tag_show_page");
        register_action("news_edit_form_add", "NS_tags_edit_form_add");
        register_action("news_edit_mod_set", "NS_news_edit_set_tag");
    }
}

function NS_basicSearchbox($data = null) {
    global $tpl;
    return $search_box = $tpl->getTPL_file("NewsSearch", "NewsSearchBarbox", $data);
}

function NS_Search_Capture() {
    global $config, $db;

    if (!empty($_POST['searchText'])) {
        $searchText = S_POST_TEXT_UTF8("searchText", $config['NS_MAX_S_TEXT'], $config['NS_MIN_S_TEXT']);
        if (empty($searchText)) {
            $msg['MSG'] = "L_NS_SEARCH_ERROR";
            NS_msgbox($msg);
        }
        $searchText = $db->escape_strip($searchText);
        $query = $db->search("news", "title lead text", $searchText, array("lang" => $config['WEB_LANG']), " LIMIT {$config['NS_RESULT_LIMIT']} " );

        if(!$query) {
            return false ;
        } else {
            NS_build_result_page($query);
        }
    }

    if(!empty($_GET["searchTag"])) {
        $searchTag = S_GET_TEXT_UTF8("searchTag", $config['NS_TAGS_SZ_LIMIT'], $config['NS_MIN_S_TEXT']);

        if (empty($searchTag)) {
            $msg['MSG'] = "L_NS_SEARCH_ERROR";
            NS_msgbox($msg);
        }
        $searchTag = $db->escape_strip($searchTag);
        $query = $db->search("news", "tags", $searchTag, array("lang" => $config['WEB_LANG']), " LIMIT {$config['NS_RESULT_LIMIT']} " );

        if(!$query) {
            return false ;
        } else {
            NS_build_result_page($query);
        }        
    }
}

function NS_build_result_page($query) {
    global $db, $config, $tpl;

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
                $result['url'] = "/app.php?module=Newspage&page=news&nid={$result['nid']}&lang={$result['lang']}&npage={$result['page']}";
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

function NS_tags_option($tags = null) {
    global $LANGDATA, $config;

    $content = "<label for='news_tags'>{$LANGDATA['L_NS_TAGS']}</label>";
    $content .= "<input  value='$tags' maxlength='{$config['NS_TAGS_SZ_LIMIT']}' id='news_tags' class='news_tags' name='news_tags' type='text' placeholder='{$LANGDATA['L_NS_TAGS_PLACEHOLDER']}' />";
    return $content;
}

function NS_tag_add_form() {
    global $config, $tpl;
    $config['NS_TAGS_SUPPORT'] ? $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OPTION", NS_tags_option()) : false;
}

function NS_news_mod_insert(& $insert_ary) {
    global $db;

    $tags = $db->escape_strip(S_POST_TEXT_UTF8("news_tags"));
    !empty($tags) ? $insert_ary['tags'] = $tags : false;
}

function NS_news_tag_show_page(& $news_row) {
    global $LANGDATA, $tpl, $config;

    if (!empty($news_row['tags'])) {
        $config['PAGE_KEYWORDS'] = $news_row['tags'];
        $exploted_tags = explode(",", $news_row['tags']);
        $tag_data = "<div class='tags'> <p>". $LANGDATA['L_NS_TAGS'] . ": ";
        foreach ($exploted_tags as $tag) {
            $tag = preg_replace('/\s+/', '', $tag);
            if($config['FRIENDLY_URL']) {
                $tag_data .= "<a href='searchTag/$tag'>$tag</a> ";
            } else {
                $tag_data .= "<a href='app.php?module=Newspage&page=news&searchTag=$tag'>$tag</a> ";
            }
        }
        $tag_data .= "</p></div>";
        $tpl->addto_tplvar("ADD_TO_NEWSSHOW_BOTTOM", $tag_data);
    } else {
        $config['PAGE_KEYWORDS'] = $news_row['title'];
    }
}

function NS_tags_edit_form_add ($news_data) {
    global $tpl;

    $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OPTION", NS_tags_option($news_data['tags']));
}
function NS_news_edit_set_tag(& $set_ary) {
    global $db;

    $tags = $db->escape_strip(S_POST_TEXT_UTF8("news_tags"));
    !empty($tags) ? $set_ary['tags'] = $tags : false;
}
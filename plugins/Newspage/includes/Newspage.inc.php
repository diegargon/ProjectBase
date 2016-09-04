<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_format_source($link) {
    if ($link['type'] == 'source') {
        $url = parse_url($link['link']);
        $domain = $url['host'];
        $result = "<a rel='nofollow' target='_blank' href='{$link['link']}'>$domain</a>";
    } else {
        return false;
    }
    return $result;
}

function get_news_byId($nid, $lang_id, $page = null) {
    global $config, $acl_auth, $db;

    empty($page) ? $page = 1 : false;

    $where_ary = array("nid" => "$nid", "lang_id" => "$lang_id", "page" => "$page");

    $query = $db->select_all("news", $where_ary, "LIMIT 1");

    if ($db->num_rows($query) <= 0) {
        $query = $db->select_all("news", array("nid" => $nid, "page" => $page), "LIMIT 1");
        return $db->num_rows($query) > 0 ? news_error_msg("L_NEWS_WARN_NOLANG") : news_error_msg("L_NEWS_DELETE_NOEXISTS");
    }
    $news_row = $db->fetch($query);

    if ('ACL' && !empty($news_row['acl']) && !$acl_auth->acl_ask($news_row['acl'])) {
        return news_error_msg("L_E_NOACCESS");
    }
    $db->free($query);

    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin")) {
        return news_error_msg("L_NEWS_ERROR_WAITINGMOD");
    }

    return $news_row;
}

function get_news_source_byID($nid) {
    global $db;

    $query = $db->select_all("links", array("source_id" => "$nid", "type" => "source"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        $source_link = $db->fetch($query);
    }
    $db->free($query);

    return $source_link;
}

function news_menu_submit_news() {
    global $LANGDATA, $config;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($config['FRIENDLY_URL']) {
        $data .= "{$config['WEB_LANG']}/submitnews";
    } else {
        $data .= "{$config['CON_FILE']}?module=Newspage&page=submitnews&lang={$config['WEB_LANG']}";
    }
    $data .= "'>" . $LANGDATA['L_SEND_NEWS'] . "</a>";
    $data .= "</li>";

    return $data;
}

function news_check_display_submit() {
    global $config, $acl_auth, $sm;
    $user = $sm->getSessionUser();

    if ((!empty($user) && $config['NEWS_SUBMIT_REGISTERED']) || (empty($user) && $config['NEWS_SUBMIT_ANON'] )) {
        return true;
    }
    if (defined('ACL') && ( $acl_auth->acl_ask("news_submit||admin_all") )) {
        return true;
    } else if (!defined('ACL') && !empty($user) && $user['isAdmin']) {
        return true;
    }

    return false;
}

function news_get_related($nid) {
    global $db;

    $query = $db->select_all("links", array("source_id" => $nid, "plugin" => "Newspage", "type" => "related"));
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        while ($relate_row = $db->fetch($query)) {
            $related[] = $relate_row;
        }
    }

    return $related;
}

function news_friendly_title($title) {
    //FIX: better way for clean all those character?
    $friendly_filter = array('"', '\'', '?', '$', ',', '.', '‘', '’', ':', ';', '[', ']', '{', '}', '*', '!', '¡', '¿', '+', '<', '>', '#', '@', '|', '~', '%', '&', '(', ')', '=', '`', '´', '/', 'º', 'ª', '\\');
    $friendly = str_replace(' ', "-", $title);
    $friendly = str_replace($friendly_filter, "", $friendly);

    return $friendly;
}

function news_error_msg($error) {
    $msgbox['MSG'] = $error;
    do_action("message_box", $msgbox);
    return false;
}

function cat_menu() {
    global $tpl;

    $menu_data['cat_list'] = get_fathers_cat_list();
    !empty($_GET['section']) ? $menu_data['cat_sub_list'] = get_childs_cat_list() : null;
    
    return $tpl->getTPL_file("Newspage", "news_cat_menu", $menu_data);
}

function get_fathers_cat_list() {
    global $db, $ml, $config, $LANGDATA;

    $cat_list = "";

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($config['WEB_LANG']);
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }

    $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id", "father" => 0));
    while ($cat = $db->fetch($query)) {
        $cat_list .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/{$cat['name']}'>{$cat['name']}</a></li>";
    }
    return $cat_list;
}

function get_childs_cat_list() {
    global $db, $ml, $config, $LANGDATA;

    $cat = $_GET['section']; //TODO FILTER
    $cat_list = "";

    if (defined('MULTILANG')) { //TODO CHECK if we have lang_id in url
        $lang_id = $ml->iso_to_id($config['WEB_LANG']);
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }

    $cat_id = getCatIDbyName($cat);

    $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id", "father" => "$cat_id"));
    if ($db->num_rows($query) > 0) {
        while ($cat = $db->fetch($query)) {
            $cat_list .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/{$cat['name']}'>{$cat['name']}</a></li>";
        }
    } else {
        $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id", "cid" => "$cat_id"), "LIMIT 1");
        $cat_actual = $db->fetch($query);

        $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id", "father" => "{$cat_actual['father']}"));
        while ($cat = $db->fetch($query)) {
            if ($cat['father'] == 0) {
                break;
            } else {
                $cat_list .= "<li><a href='/{$config['WEB_LANG']}/{$LANGDATA['L_NEWS_SECTION']}/{$cat['name']}'>{$cat['name']}</a></li>";
            }
        }
    }
    return !empty($cat_list) ? $cat_list : false;
}

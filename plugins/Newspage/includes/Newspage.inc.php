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
    if ($user && defined('ACL') && ( $acl_auth->acl_ask("news_submit||admin_all") )) {
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

function news_cat_menu() {
    global $tpl, $ctgs, $config, $LANGDATA;
    $cat_path = S_GET_TEXT_UTF8("section");

    $menu_data = $ctgs->root_cats("Newspage");
    if ($config['NEWS_BACKPAGE_SECTION']) {
        if ($config['FRIENDLY_URL']) {
            $url = "/{$config['WEB_LANG']}";
        } else {
            $url = "";
        }
        $menu_data .= "<li><a href='$url'>" . $LANGDATA['L_NEWS_BACKPAGE'] . "</a></li>";
    }
    !empty($cat_path) ? $submenu_data = $ctgs->childs_of_cat("Newspage", $cat_path) : null;

    $tpl->addto_tplvar("SECTIONS_MENU", $menu_data);
    isset($submenu_data) ? $tpl->addto_tplvar("SECTIONS_SUBMENU", $submenu_data) : null;
}

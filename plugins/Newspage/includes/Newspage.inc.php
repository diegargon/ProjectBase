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
    global $cfg, $acl_auth, $db;
    empty($page) ? $page = 1 : false;

    $where_ary = ["nid" => "$nid", "lang_id" => "$lang_id", "page" => "$page"];

    $query = $db->select_all("news", $where_ary, "LIMIT 1");

    if ($db->num_rows($query) <= 0) {
        $query = $db->select_all("news", ["nid" => $nid, "page" => $page], "LIMIT 1");
        return $db->num_rows($query) > 0 ? news_error_msg("L_NEWS_WARN_NOLANG") : news_error_msg("L_NEWS_DELETE_NOEXISTS");
    }
    $news_row = $db->fetch($query);

    if ('ACL' && !empty($news_row['acl']) && !$acl_auth->acl_ask($news_row['acl'])) {
        return news_error_msg("L_E_NOACCESS");
    }
    $db->free($query);

    if ($cfg['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin")) {
        return news_error_msg("L_NEWS_ERROR_WAITINGMOD");
    }

    return $news_row;
}

function get_news_source_byID($nid) {
    global $db;

    $query = $db->select_all("links", ["source_id" => "$nid", "type" => "source"], "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        $source_link = $db->fetch($query);
    }
    $db->free($query);

    return $source_link;
}

function news_menu_submit_news() {
    global $LNG, $cfg;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($cfg['FRIENDLY_URL']) {
        $data .= "{$cfg['WEB_LANG']}/submitnews";
    } else {
        $data .= "{$cfg['CON_FILE']}?module=Newspage&page=submitnews&lang={$cfg['WEB_LANG']}";
    }
    $data .= "'>" . $LNG['L_CREATE_NEWS'] . "</a>";
    $data .= "</li>";

    return $data;
}

function news_check_display_submit() {
    global $cfg, $acl_auth, $sm;
    $user = $sm->getSessionUser();

    if ((!empty($user) && $cfg['NEWS_SUBMIT_REGISTERED']) || (empty($user) && $cfg['NEWS_SUBMIT_ANON'] )) {
        return true;
    }
    if ($user && defined('ACL') && ( $acl_auth->acl_ask("news_submit||admin_all") )) {
        return true;
    } else if (!defined('ACL') && !empty($user) && $user['isAdmin']) {
        return true;
    }

    return false;
}

function news_display_submit() {
    news_check_display_submit() ? register_action("header_menu_element", "news_menu_submit_news") : null;
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
    $friendly_filter = ['"', '\'', '?', '$', ',', '.', '‘', '’', ':', ';', '[', ']', '{', '}', '*', '!', '¡', '¿', '+', '<', '>', '#', '@', '|', '~', '%', '&', '(', ')', '=', '`', '´', '/', 'º', 'ª', '\\'];
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
    global $tpl, $ctgs, $cfg, $LNG;
    $cat_path = S_GET_TEXT_UTF8("section");

    $menu_cats = $ctgs->root_cats("Newspage");
    $menu_data = null;
    $submenu_data = null;

    if ($menu_cats != false) {
        foreach ($menu_cats as $menucat) {
            $cat_display_name = preg_replace('/\_/', ' ', $menucat['name']);
            $menu_data .= "<li><a href='/{$cfg['WEB_LANG']}/{$LNG['L_NEWS_SECTION']}/{$menucat['name']}'>$cat_display_name</a></li>";
        }
    }
    if ($cfg['NEWS_BACKPAGE_SECTION']) {
        if ($cfg['FRIENDLY_URL']) {
            $url = "/{$cfg['WEB_LANG']}";
        } else {
            $url = "";
        }
        $menu_data .= "<li><a href='$url'>" . $LNG['L_NEWS_BACKPAGE'] . "</a></li>";
    }

    $cats_explode = explode($cfg['NEWS_CAT_SEPARATOR'], $cat_path);
    if (count($cats_explode) > 1) {
        array_pop($cats_explode);
        $f_cats = implode($cfg['NEWS_CAT_SEPARATOR'], $cats_explode);
        $submenu_data .= "<li><a href='/{$cfg['WEB_LANG']}/{$LNG['L_NEWS_SECTION']}/$f_cats'>{$cfg['NEWS_MENU_BACK_SYMBOL']}</a></li>";
    }

    $cat_id = $ctgs->getCatIDbyName_path("Newspage", $cat_path);
    $childcats = $ctgs->childcats("Newspage", $cat_path);
    if (!empty($childcats)) {
        foreach ($childcats as $childcat) {
            if ($childcat['father'] == $cat_id) {
                $cat_display_name = preg_replace('/\_/', ' ', $childcat['name']);
                $submenu_data .= "<li><a href='/{$cfg['WEB_LANG']}/{$LNG['L_NEWS_SECTION']}/$cat_path.{$childcat['name']}'>$cat_display_name</a></li>";
            }
        }
    }

    $tpl->addto_tplvar("SECTIONS_NAV", $menu_data);
    isset($submenu_data) ? $tpl->addto_tplvar("SECTIONS_SUBMENU", $submenu_data) : null;
}

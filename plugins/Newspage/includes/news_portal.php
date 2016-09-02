<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_layout_select() {
    global $config;

    if (empty($_POST['news_switch']) || $_POST['news_switch'] > $config['NEWS_PORTAL_STYLES']) {
        $news_switch = 1;
    } else {
        $news_switch = S_POST_INT("news_switch", 1);
    }

    return $news_switch;
}

function news_layout_switcher() {
    global $tpl;

    $switcher_tpl = "<li class='nav_left'><form action='#' method='post'>";
    $switcher_tpl .= "<input type='submit'  value='' class='button_switch' />";
    $switcher_tpl .= "<input type='hidden' value=" . $tpl->gettpl_value("news_nSwitch") . " name='news_switch'/>";
    $switcher_tpl .= "</form></li>";

    return $switcher_tpl;
}

function get_news($news_select) {
    global $config, $db, $tpl, $ml, $LANGDATA;
    $content = "";

    !isset($news_select['limit']) ? $news_select['limit'] = 0 : null;
    !isset($news_select['featured']) ? $news_select['featured'] = 0 : null;
    !isset($news_select['headlines']) ? $news_select['headlines'] = 0 : null;
    !isset($news_select['category']) ? $news_select['category'] = 0 : null;
    !isset($news_select['cathead']) ? $news_select['cathead'] = 0 : null;
    !isset($news_select['excl_first_featured']) ? $news_select['excl_first_featured'] = 0 : null;
    !isset($news_select['excl_firstcat_featured']) ? $news_select['excl_firstcat_featured'] = 0 : null;

    $where_ary = array(
        "page" => 1,
    );

    if (defined('MULTILANG')) {
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $config['WEB_LANG']) {
                $lang_id = $site_lang['lang_id'];
                $where_ary['lang_id'] = $lang_id;
                break;
            }
        }
    }

    if ($news_select['excl_first_featured']) {
        $featured_ary = array(
            "featured" => 1,
            "page" => 1,
            "lang_id" => "$lang_id",
        );
        $featured_query = $db->select_all("news", $featured_ary, "ORDER BY featured_date DESC LIMIT 1");
        $featured_news = $db->fetch($featured_query);
        $where_ary['nid'] = array("value" => $featured_news['nid'], "operator" => "<>");
    }

    if ($news_select['excl_firstcat_featured'] && !empty($news_select['category'])) {
        $featured_ary = array(
            "featured" => 1,
            "page" => 1,
            "lang_id" => "$lang_id",
        );
        $featured_ary['category'] = $news_select['category'];
        $featured_query = $db->select_all("news", $featured_ary, "ORDER BY featured_date DESC LIMIT 1");
        $featured_news = $db->fetch($featured_query);
        $where_ary['nid'] = array("value" => $featured_news['nid'], "operator" => "<>");
    }

    $config['NEWS_MODERATION'] == 1 ? $where_ary['moderation'] = 0 : null;

    $news_select['category'] ? $where_ary['category'] = $news_select['category'] : null;
    $news_select['featured'] ? $where_ary['featured'] = 1 : null;
    isset($news_select['frontpage']) ? $where_ary['frontpage'] = $news_select['frontpage'] : null;
    $news_select['featured'] ? $q_extra = " ORDER BY featured_date DESC" : $q_extra = " ORDER BY date DESC";
    $news_select['limit'] > 0 ? $q_extra .= " LIMIT {$news_select['limit']}" : null;

    $query = $db->select_all("news", $where_ary, $q_extra);
    if ($db->num_rows($query) <= 0) {
        return false;
    }

    if ($news_select['cathead']) {
        $catname = null;
        if (defined('MULTILANG') && !empty($news_select['category'])) {
            $catname = "<h2>";
            !empty($news_select['featured']) ? $catname .= $LANGDATA['L_NEWS_FEATURED'] . ": " : null;
            $catname .= get_category_name($news_select['category'], $lang_id) . "</h2>";
        } else if ($news_select['category']) {
            $catname = "<h2>";
            $news_select['featured'] ? $catname .= $LANGDATA['L_NEWS_FEATURED'] . ": " : null;
            $catname .= get_category_name($news_select['category']) . "</h2>";
        }

        if (!$news_select['category'] && ( isset($news_select['frontpage']) && $news_select['frontpage'] == 0) && !$news_select['featured']) {
            $catname = "<h2>" . $LANGDATA['L_NEWS_BACKPAGE'] . "</h2>";
        } else if (!$news_select['category'] && !isset($news_select['frontpage']) && !$news_select['featured']) {
            $catname = "<h2>" . $LANGDATA['L_NEWS_FRONTPAGE'] . "</h2>";
        } else if (!$news_select['category'] && $news_select['featured']) {
            $catname = "<h2 class='featured_category'>{$LANGDATA['L_NEWS_FEATURED']}</h2>";
        }
        $content .= $catname;
    }

    $save_img_selector = $config['IMG_SELECTOR'];
    !$news_select['featured'] ? $config['IMG_SELECTOR'] = "thumbs" : null; //no thumb for featured image
    while ($news_row = $db->fetch($query)) {
        if (($news_data = fetch_news_data($news_row)) != false) {
            $news_select['headlines'] ? $news_data['headlines'] = 1 : null;
            if ($news_select['featured']) {
                do_action("news_featured_mod", $news_data);
                $content .= $tpl->getTPL_file("Newspage", "news_featured", $news_data);
            } else {
                do_action("news_get_news_mod", $news_data);
                $content .= $tpl->getTPL_file("Newspage", "news_preview", $news_data);
            }
        }
    }
    $db->free($query);
    $config['IMG_SELECTOR'] = $save_img_selector;

    return $content;
}

function news_determine_main_image($news) {
    $news_body = $news['text'];
    $match_regex = "/\[(img|localimg).*\](.*)\[\/(img|localimg)\]/";
    $match = false;
    preg_match($match_regex, $news_body, $match);

    return !empty($match[0]) ? $match[0] : false;
}

function fetch_news_data($row) {
    global $config, $acl_auth;

    if ($config['NEWS_ACL_PREVIEW_CHECK'] && defined('ACL') &&
            !empty($acl_auth) && !empty($row['acl']) && !$acl_auth->acl_ask($row['acl'])) {
        return false;
    }
    $news['nid'] = $row['nid'];
    $news['title'] = $row['title'];
    $news['lead'] = $row['lead'];
    $news['date'] = format_date($row['date']);
    $news['alt_title'] = htmlspecialchars($row['title']);

    if ($config['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($row['title']);
        $news['url'] = "/" . $config['WEB_LANG'] . "/news/{$row['nid']}/{$row['page']}/$friendly_title";
    } else {
        $news['url'] = "/{$config['CON_FILE']}?module=Newspage&page=news&nid={$row['nid']}&lang=" . $config['WEB_LANG'] . "&npage={$row['page']}";
    }
    $mainimage = news_determine_main_image($row);
    if (!empty($mainimage)) {
        require_once 'parser.class.php';
        !isset($news_parser) ? $news_parser = new parse_text : null;
        $news['mainimage'] = $news_parser->parse($mainimage);
    }

    return $news;
}

function get_category_name($cid, $lang_id = null) {
    global $db;

    $where_ary['cid'] = $cid;
    defined('MULTILANG') && $lang_id != null ? $where_ary['lang_id'] = $lang_id : null;

    $query = $db->select_all("categories", $where_ary, "LIMIT 1");
    $category = $db->fetch($query);
    $db->free($query);

    return $category['name'];
}

function news_portal_content() {
    global $config;
    
    $portal_content = [];

    $config['NEWS_PORTAL_FEATURED'] ? $portal_content['featured'] = get_news(array("featured" => 1, "limit" => 1, "cathead" => 1)) : null;

    if ($config['NEWS_PORTAL_COLS'] >= 1) {
        $portal_content['col1_articles'] = news_getPortalColLayout($config['NEWS_PORTAL_COL1_CONTENT']);
    }
    if ($config['NEWS_PORTAL_COLS'] >= 2) {
        $portal_content['col2_articles'] = news_getPortalColLayout($config['NEWS_PORTAL_COL2_CONTENT']);
    }
    if ($config['NEWS_PORTAL_COLS'] >= 3) {
        $portal_content['col3_articles'] = news_getPortalColLayout($config['NEWS_PORTAL_COL3_CONTENT']);
    }

    return $portal_content;
}

function news_getPortalColLayout($columnConfigs) {
    $content = "";

    foreach ($columnConfigs as $columConfig) {
        $content .= get_news($columConfig);
    }
    
    return $content;
}

function cat_menu() {
    global $tpl;
    
    $menu_data['cat_list'] = get_fathers_cat_list();
    
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

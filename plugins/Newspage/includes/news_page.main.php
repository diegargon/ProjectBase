<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function news_show_page() {
    global $config, $LANGDATA, $tpl, $sm, $ml, $acl_auth;

    $news_data = [];

    if ((empty($_GET['nid'])) || ($nid = S_GET_INT("nid", 8, 1)) == false ||
            (empty($_GET['lang'])) || ($lang = S_GET_CHAR_AZ("lang", 2, 2)) == false) {
        return news_error_msg("L_NEWS_NOT_EXIST");
    }

    if ($config['NEWS_MULTIPLE_PAGES'] && !empty($_GET['npage'])) {
        $page = S_GET_INT("npage", 11, 1);
    } else {
        $page = 1;
    }

    $user = $sm->getSessionUser();
    if (S_GET_INT("admin")) {
        if (!$user ||(defined("ACL") && !$acl_auth->acl_ask("admin_all||news_admin"))) {
            return news_error_msg("L_E_NOACCESS");
        }
        if (!defined('ACL') && $user['isAdmin'] != 1) {           
            return news_error_msg("L_E_NOACCESS");
        }
    }
    news_process_admin_actions();

    if (defined('MULTILANG') && $lang != null) {
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if ($site_lang['iso_code'] == $lang) {
                $lang_id = $site_lang['lang_id'];
                break;
            }
        }
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }
    if (($news_data = get_news_byId($nid, $lang_id, $page)) == false) {
        return false;
    }
    //HEAD MOD
    $config['NEWS_STATS'] ? news_stats($nid, $lang, $page, $news_data['visits']) : false;
    $config['PAGE_TITLE'] = $news_data['title'] . ": " . $config['TITLE'];
    $config['NEWS_META_OPENGRAPH'] ? news_add_social_meta($news_data) : false;
    $config['PAGE_DESC'] = $news_data['title'] . ":" . $news_data['lead'];
    //END HEAD MOD

    $news_data['news_admin_nav'] = news_nav_options($news_data);
    $config['NEWS_MULTIPLE_PAGES'] ? $news_data['pager'] = news_pager($news_data) : false;

    $news_data['title'] = str_replace('\r\n', '', $news_data['title']);
    $news_data['lead'] = str_replace('\r\n', PHP_EOL, $news_data['lead']);
    $news_data['news_url'] = "news.php?nid={$news_data['nid']}";
    $news_data['date'] = format_date($news_data['date']);
    $news_data['author'] = $news_data['author'];
    $news_data['author_uid'] = $news_data['author_id'];

    !isset($news_parser) ? $news_parser = new parse_text : false;
    $news_data['text'] = $news_parser->parse($news_data['text']);

    if (!empty($news_data['translator_id'])) {
        $translator = $sm->getUserByID($news_data['translator_id']);
        $news_data['translator'] = "<a rel='nofollow' href='/{$config['WEB_LANG']}/profile&viewprofile={$translator['uid']}'>{$translator['username']}</a>";
    }
    $author = $sm->getUserByID($news_data['author_id']);
    $config['PAGE_AUTHOR'] = $author['username'];
    $news_data['author_avatar'] = $author['avatar'];

    if ($config['NEWS_SOURCE'] && ($news_source = get_news_source_byID($news_data['nid'])) != false) {
        $news_data['news_sources'] = news_format_source($news_source);
    }
    if ($config['NEWS_RELATED'] && ($news_related = news_get_related($news_data['nid'])) != false) {
        $related_content = "<span>{$LANGDATA['L_NEWS_RELATED']}:</span>";
        foreach ($news_related as $related) {
            $related_content .= "<li><a rel='nofollow' target='_blank' href='{$related['link']}'>{$related['link']}</a></li>";
        }
        $news_data['news_related'] = $related_content;
    }
    $config['NEWS_BREADCRUMB'] ? $news_data['NEWS_BREADCRUMB'] = getNewsCatBreadcrumb($news_data) : false;

    do_action("news_show_page", $news_data);

    news_cat_menu();
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_body", $news_data));
}

function news_process_admin_actions() {
    global $config, $acl_auth, $sm, $ml;

    //if we enter with &admin=1 already passing the admin check in news_show_page, check if not enter with admin=1 , do again and remove if?
    $user = $sm->getSessionUser();
    if (!S_GET_INT("admin")) {
        if (!$user || (defined("ACL") && !$acl_auth->acl_ask("admin_all || news_admin"))) {
            return false;
        }
        if (!defined("ACL") && $user['isAdmin'] != 1) {
            return false;
        }
    }
    if (!empty($_GET['news_delete'])) {
        $delete_nid = S_GET_INT("nid", 11, 1);
        $delete_lang = S_GET_CHAR_AZ("lang", 2, 2);
        if (!empty($delete_nid) && !empty($delete_lang)) {
            defined('MULTILANG') ? $delete_lang_id = $ml->iso_to_id($delete_lang) : $delete_lang_id = $config['WEB_LANG_ID'];
            news_delete($delete_nid, $delete_lang_id);
            S_GET_CHAR_AZ("backlink") == "home" ? header("Location: /{$config['WEB_LANG']}") : header("Location: " . S_SERVER_URL("HTTP_REFERER") . "");
        }
    }
    if (!empty($_GET['news_approved']) && !empty($_GET['lang_id']) &&
            $_GET['news_approved'] > 0 && $_GET['lang_id'] > 0) {
        news_approved(S_GET_INT("news_approved"), S_GET_INT("lang_id"));
    }
    if (isset($_GET['news_featured']) && !empty($_GET['lang_id'] && !empty($_GET['nid']))) {
        empty($_GET['news_featured']) ? $news_featured = 0 : $news_featured = 1;
        news_featured(S_GET_INT("nid", 11, 1), $news_featured, S_GET_INT("lang_id"));
    }
    if (isset($_GET['news_frontpage']) && !empty($_GET['lang_id'])) {
        news_frontpage(S_GET_INT("nid", 11, 1), S_GET_INT("lang_id"), S_GET_INT("news_frontpage", 1, 1));
    }

    return true;
}

function news_nav_options($news) { //TODO Use Template
    global $LANGDATA, $config, $sm, $acl_auth;
    $content = "";
    $news_url = "/{$config['CON_FILE']}?module=Newspage&page=news&nid={$news['nid']}&lang={$news['lang']}&npage={$news['page']}&lang_id={$news['lang_id']}";
    $user = $sm->getSessionUser();
    // EDIT && NEW PAGE: ADMIN, AUTHOR or Translator
    if (( $user && defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin")) || ( $user && !defined('ACL') && $user['isAdmin'] == 1)) {
        $admin = 1;
    } else {
        $admin = 0;
    }
    //Only admin but show disabled to all
    if ($admin && $news['featured'] == 1 && $news['page'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href='$news_url&news_featured=0&featured_value=0&admin=1'>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";
    } else if ($admin && $news['page'] == 1) {
        $content .= "<li><a rel='nofollow' href='$news_url&news_featured=1&featured_value=1&admin=1'>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";
    } else if ($news['featured'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LANGDATA['L_NEWS_FEATURED']}</a></li>";
    }
    if ($admin && $news['page'] == 1 && $news['frontpage'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href='$news_url&news_frontpage=0'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
    } else if ($admin && $news['page'] == 1) {
        $content .= "<li><a rel='nofollow' href='$news_url&news_frontpage=1'>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
    } else if ($news['frontpage'] == 1) {
        $content .= "<li><a class='link_active' rel='nofollow' href=''>{$LANGDATA['L_NEWS_FRONTPAGE']}</a></li>";
    }

    if ($admin || $news['author'] == $user['username'] || !empty($news['translator'] && ($news['translator'] == $user['username']))) {
        $content .= "<li><a rel='nofollow' href='$news_url&newsedit=1'>{$LANGDATA['L_NEWS_EDIT']}</a></li>";
    }
    //not translator
    if ($config['NEWS_MULTIPLE_PAGES'] && ( $admin || $news['author'] == $user['username'])) {
        $content .= "<li><a rel='nofollow' href='$news_url&newpage=1'>{$LANGDATA['L_NEWS_NEW_PAGE']}</a></li>";
    }

    // TRANSLATE ADMIN, ANON IF, REGISTERED IF
    if (defined('MULTILANG')) {
        if ($config['NEWS_ANON_TRANSLATE'] || $admin || ($user && defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'] && $acl_auth->acl_ask("registered_all")) || ($user && !defined('ACL') && $config['NEWS_TRANSLATE_REGISTERED'])
        ) {
            $content .= "<li><a rel='nofollow' href='$news_url&news_new_lang=1'>{$LANGDATA['L_NEWS_NEWLANG']}</a></li>";
        }
    }
    if ($admin) {
        if ($news['moderation'] && $news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='/$news_url&news_approved={$news['nid']}&admin=1'>{$LANGDATA['L_NEWS_APPROVED']}</a></li>";
        }
        //TODO  Add a menu for enable/disable news
        //    //$content .= "<li><a href=''>{$LANGDATA['L_NEWS_DISABLE']}</a></li>";
        if ($news['page'] == 1) {
            $content .= "<li><a rel='nofollow' href='$news_url&news_delete=1&admin=1&backlink=home' onclick=\"return confirm('{$LANGDATA['L_NEWS_CONFIRM_DEL']}')\">{$LANGDATA['L_NEWS_DELETE']}</a></li>";
        }
    }

    return $content;
}

function news_pager($news_page) {
    global $db, $config;

    $query = $db->select_all("news", array("nid" => $news_page['nid'], "lang_id" => $news_page['lang_id']));
    if (($num_pages = $db->num_rows($query)) <= 1) {
        return false;
    }
    $content = "<div id='pager'><ul>";

    $news_page['page'] == 1 ? $a_class = "class='active'" : $a_class = '';
    if ($config['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/1/$friendly_title'>1</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=1'>1</a></li>";
    }

    $pager = page_pager($config['NEWS_PAGER_MAX'], $num_pages, $news_page['page']);

    for ($i = $pager['start_page']; $i < $pager['limit_page']; $i++) {
        $news_page['page'] == $i ? $a_class = "class='active'" : $a_class = '';
        if ($config['FRIENDLY_URL']) {
            $friendly_title = news_friendly_title($news_page['title']);
            $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$i/$friendly_title'>$i</a></li>";
        } else {
            $content .= "<li><a $a_class href='{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=$i'>$i</a></li>";
        }
    }
    $news_page['page'] == $num_pages ? $a_class = "class='active'" : $a_class = '';
    if ($config['FRIENDLY_URL']) {
        $friendly_title = news_friendly_title($news_page['title']);
        $content .= "<li><a $a_class href='/{$news_page['lang']}/news/{$news_page['nid']}/$num_pages/$friendly_title'>$num_pages</a></li>";
    } else {
        $content .= "<li><a $a_class href='{$config['CON_FILE']}?module=Newspage&page=news&nid={$news_page['nid']}&lang={$news_page['lang']}&npage=$num_pages'>$num_pages</a></li>";
    }
    $content .= "</ul></div>";

    return $content;
}

function page_pager($max_pages, $num_pages, $actual_page) {
    $addition = 0;
    $middle = (round(($max_pages / 2), 0, PHP_ROUND_HALF_DOWN) );
    $start_page = $actual_page - $middle;

    if ($start_page < 2) {
        if ($start_page < 0) {
            $addition = ($start_page * -1) + 2;
        } else if ($start_page == 0) {
            $addition = $start_page + 2;
        } else {
            $addition = $start_page;
        }
        $start_page = 2;
    }

    $limit_page = $actual_page + $middle + $addition;
    $limit_page > $num_pages ? $limit_page = $num_pages : null;

    if (($max_pages + $start_page) > $limit_page) {
        $start_page = $start_page - (($max_pages + $start_page) - $limit_page);
    }
    $start_page < 2 ? $start_page = 2 : null;

    $pager['start_page'] = $start_page;
    $pager['limit_page'] = $limit_page;

    return $pager;
}

function news_delete($nid, $lang_id) {
    global $db;

    $db->delete("news", array("nid" => $nid, "lang_id" => $lang_id));
    $query = $db->select_all("news", array("nid" => $nid), "LIMIT 1"); //check if other lang
    if ($db->num_rows($query) <= 0) {
        $db->delete("links", array("plugin" => "Newspage", "source_id" => $nid));
        //ATM by default this fuction delete all "links" if no exists the same news in other lang, mod like 
        do_action("news_delete_mod", $nid);
    }
    return true;
}

function news_approved($nid, $lang_id) {
    global $db;

    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $db->update("news", array("moderation" => 0), array("nid" => $nid, "lang_id" => $lang_id));

    return true;
}

function news_featured($nid, $featured, $lang_id) {
    global $db;

    //$time = format_date(time(), true);
    $time = date('Y-m-d H:i:s', time());

    if (empty($nid) || empty($lang_id)) {
        return false;
    }
    $update_ary = array("featured" => "$featured");
    $featured == 1 ? $update_ary['featured_date'] = $time : false;
    $db->update("news", $update_ary, array("nid" => $nid, "lang_id" => $lang_id));

    return true;
}

function news_frontpage($nid, $lang_id, $frontpage_state = 0) {
    global $db;

    if (empty($nid) || empty($lang_id) || $nid <= 0 && $lang_id <= 0) {
        return false;
    }
    $db->update("news", array("frontpage" => $frontpage_state), array("nid" => $nid, "lang_id" => $lang_id));

    return true;
}

function news_stats($nid, $lang, $page, $visits) {
    global $db, $config;
    $db->update("news", array("visits" => ++$visits), array("nid" => "$nid", "lang" => "$lang", "page" => "$page"), "LIMIT 1");
    $config['NEWS_ADVANCED_STATS'] ? news_adv_stats($nid, $lang) : false;
}

function news_adv_stats($nid, $lang) {
    global $db, $sm;

    $plugin = "Newspage";

    $user = $sm->getSessionUser();
    empty($user) ? $user['uid'] = 0 : false; //Anon        
    $ip = S_SERVER_REMOTE_ADDR();
    $hostname = gethostbyaddr($ip);
    $where_ary = array(
        "type" => "user_visits_page",
        "plugin" => "$plugin",
        "lang" => "$lang",
        "rid" => "$nid",
        "uid" => $user['uid']
    );
    $user['uid'] == 0 ? $where_ary['ip'] = $ip : false;

    $query = $db->select_all("adv_stats", $where_ary, "LIMIT 1");

    $user_agent = S_SERVER_USER_AGENT();
    $referer = S_SERVER_URL("HTTP_REFERER");

    if ($db->num_rows($query) > 0) {
        $user_adv_stats = $db->fetch($query);
        $counter = ++$user_adv_stats['counter'];
        $db->update("adv_stats", array("counter" => "$counter", "user_agent" => "$user_agent", "referer" => "$referer"), array("advstatid" => $user_adv_stats['advstatid']));
    } else {
        $insert_ary = array(
            "plugin" => "$plugin",
            "type" => "user_visits_page",
            "rid" => "$nid",
            "lang" => "$lang",
            "uid" => $user['uid'],
            "ip" => "$ip",
            "hostname" => $hostname,
            "user_agent" => "$user_agent",
            "referer" => "$referer",
            "counter" => 1
        );
        $db->insert("adv_stats", $insert_ary);
    }

    if ((!empty($referer)) && ( (strpos($referer, "://" . $_SERVER['SERVER_NAME']) ) === false)) {
        $query = $db->select_all("adv_stats", array("type" => "referers_only", "referer" => "$referer"), "LIMIT 1");
        if ($db->num_rows($query) > 0) {
            $allreferers = $db->fetch($query);
            $counter = ++$allreferers['counter'];
            $db->update("adv_stats", array("counter" => "$counter"), array("advstatid" => $allreferers['advstatid']));
        } else {
            $insert_ary = array(
                "plugin" => "$plugin",
                "type" => "referers_only",
                "referer" => $referer,
                "counter" => 1,
            );
            $db->insert("adv_stats", $insert_ary);
        }
    }
}

function news_add_social_meta($news) { // TODO: Move to plugin NewsSocialExtra
    global $tpl, $config;
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
    $news['url'] = $protocol . $_SERVER['HTTP_HOST'] . S_SERVER_REQUEST_URI();
    $news['web_title'] = $config['TITLE'];
    $match_regex = "/img\](.*)\[\/.*img\]/";
    $match = false;
    preg_match($match_regex, $news['text'], $match);
    if (empty($match[1])) {
        return false;
    }
    $url = preg_replace('/\[S\]/si', "/" . $config['IMG_SELECTOR'] . "/", $match[1]);
    $news['mainimage'] = $config['STATIC_SRV_URL'] . $url;
    $content = $tpl->getTPL_file("Newspage", "NewsSocialmeta", $news);
    $tpl->addto_tplvar("META", $content);
}

function getNewsCatBreadcrumb($news_data) {
    global $db, $config;
    $content = "";
    
    $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => $news_data['lang_id']));
    while ($cat_row = $db->fetch($query)) {
        $categories[$cat_row['cid']] = $cat_row;
    }
    $news_cat_id = $news_data['category'];

    $cat_list = "";
    $cat_check = $categories[$news_cat_id]['father'];
    do {
        $cat_list = $categories[$cat_check]['name'] . "," . $cat_list;
        $cat_check = $categories[$cat_check]['father'];
    } while ($cat_check != 0);

    $cat_list = $cat_list . $categories[$news_cat_id]['name'];
    $cat_ary = explode(",", $cat_list);

    $breadcrumb = "";
    $cat_path = "";
    foreach ($cat_ary as $cat) {
        $cat_path .= $cat;
        !empty($breadcrumb) ? $breadcrumb .= $config['NEWS_BREADCRUMB_SEPARATOR'] : null;
        $cat = preg_replace('/\_/', ' ', $cat);
        $breadcrumb .= "<li><a href='/{$config['WEB_LANG']}/section/$cat_path'>$cat</a></li>";
        $cat_path .= ".";
    }
    $content .= $breadcrumb;    

    return $content;
}

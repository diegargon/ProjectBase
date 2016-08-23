<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_layout_select() {
    global $config;

    if(empty($_POST['news_switch']) || $_POST['news_switch'] > $config['NEWS_BODY_STYLES']) {
        $news_switch = 1;
    } else{
        $news_switch = S_POST_INT("news_switch", 1);
    }

    return $news_switch;
}

function news_layout_switcher() { 
    global $tpl;

    $switcher_tpl = "<li class='nav_left'><form action='#' method='post'>";
    $switcher_tpl .= "<input type='submit'  value='' class='button_switch' />";
    $switcher_tpl .= "<input type='hidden' value=" . $tpl->gettpl_value("news_nSwitch") ." name='news_switch'/>";
    $switcher_tpl .= "</form></li>";

    return $switcher_tpl;
}

function getNews_featured ($category = 0, $limit = 1) {
    return get_news($category, $limit , $headlines = 0, $frontpage = 1, $featured = 1);
}
//FRONTPAGE
function getNews_frontpage ($category = 0, $limit = null) {
    return get_news($category, $limit , $headlines = 0, $frontpage = 1);
}
function getNews_frontpage_h ($category = 0, $limit = null) {
    return get_news($category, $limit , $headlines = 1, $frontpage = 1);
}
//BACKPAGE
function getNews_backpage ($category = 0 , $limit = null) {
    return get_news($category, $limit , $headlines = 0, $frontpage = 0);
}
function getNews_backpage_h($category = 0 , $limit = null) {
    return get_news($category, $limit , $headlines = 1, $frontpage = 0);
}

function get_news($category = 0, $limit = null, $headlines = 0, $frontpage = 1, $featured = null) {
    global $config, $db, $tpl, $ml, $LANGDATA;
    $content = "";

    $where_ary =  array( "page" => 1 );

    if ($featured != null) {
        $where_ary['featured'] = $featured;
    } else {
        $where_ary['featured'] = array("value" => "1", "operator" => "<>");
    }
    $config['NEWS_SELECTED_FRONTPAGE'] ? $where_ary['frontpage'] = $frontpage : false;
    $config['NEWS_MODERATION'] == 1 ? $where_ary['moderation'] = 0 : false;

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

    !empty($category) && $category != 0 ? $where_ary['category'] = $category : false;
    $q_extra = " ORDER BY date DESC";
    $limit > 0 ? $q_extra .= " LIMIT $limit" : false;

    $query = $db->select_all("news", $where_ary, $q_extra);
    if ($db->num_rows($query) <= 0) {
        return false;
    }

    $catname = null;
    if (defined('MULTILANG') && !empty($category)) {
        $catname = get_category_name($category, $lang_id);
    } else if (!empty($category)) {
        $catname = get_category_name($category);
    } else if (empty($category) && $frontpage == 0 && $featured == 0) {
        $catname = $LANGDATA['L_NEWS_BACKPAGE'];
    } else if (empty($category) && $frontpage == 1 && $featured == 0) {
        $catname = $LANGDATA['L_NEWS_FRONTPAGE'];
    }

    $content .= "<h2>$catname</h2>";

    $save_img_selector = $config['IMG_SELECTOR'];
    empty($featured) ? $config['IMG_SELECTOR'] = "thumbs" : false; //no thumb for featured image
    while($news_row = $db->fetch($query)) {
        if ( ($news_data = fetch_news_data($news_row)) != false) {
            $headlines == 1 ? $news_data['headlines'] = 1 : false;
            if ($featured == 1) {
                do_action("news_featured_mod" ,$news_data);
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

    if( $config['NEWS_ACL_PREVIEW_CHECK']  && defined('ACL') && 
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
        $news['url'] = "/".$config['WEB_LANG']."/news/{$row['nid']}/{$row['page']}/$friendly_title";
    } else {
        $news['url'] = "/{$config['CON_FILE']}?module=Newspage&page=news&nid={$row['nid']}&lang=".$config['WEB_LANG']."&npage={$row['page']}";
    }
    $mainimage = news_determine_main_image($row);
    if (!empty($mainimage)) {
        require_once 'parser.class.php';
        !isset($news_parser) ? $news_parser = new parse_text : false;
        $news['mainimage'] = $news_parser->parse($mainimage);
    }
    return $news;
}

function get_category_name($cid, $lang_id = null) {
    global $db; 

    $where_ary['cid'] = $cid;
    defined('MULTILANG') && $lang_id != null ? $where_ary['lang_id'] = $lang_id : false;

    $query = $db->select_all("categories", $where_ary, "LIMIT 1");
    $category = $db->fetch($query);
    $db->free($query);  

    return $category['name'];
}

function news_portal_config(){
    global $config;
    $portal_content = [];

    if ($config['NEWS_PORTAL_COLS'] >=1) {
        $portal_content['COL1_ARTICLES'] = news_getPortalConfig($config['NEWS_PORTAL_COL1_CONTENT'], $config['NEWS_PORTAL_COL1_CONTENT_CATS'], $config['NEWS_PORTAL_COL1_CONTENT_LIMIT']);
    }
    if ($config['NEWS_PORTAL_COLS'] >=2) {
        $portal_content['COL2_ARTICLES'] = news_getPortalConfig($config['NEWS_PORTAL_COL2_CONTENT'], $config['NEWS_PORTAL_COL2_CONTENT_CATS'], $config['NEWS_PORTAL_COL2_CONTENT_LIMIT']);
    }
    if ($config['NEWS_PORTAL_COLS'] >=3) {
        $portal_content['COL3_ARTICLES'] = news_getPortalConfig($config['NEWS_PORTAL_COL3_CONTENT'], $config['NEWS_PORTAL_COL3_CONTENT_CATS'], $config['NEWS_PORTAL_COL3_CONTENT_LIMIT']);
    }

    return $portal_content;
}

function news_getPortalConfig($type, $cats, $limit = 0) {
    $content = "";
    
    $cats = preg_replace('/\s+/', '', $cats); 
    $cats = explode(",", $cats);
    
    foreach ($cats as $cat) {
        if(isset($cat)) {
            if ($type == "frontpage") {
                $content .= getNews_frontpage($cat, $limit);
            } else if ($type == "frontpage_h") {
                $content .= getNews_frontpage_h($cat, $limit);
            } else if ($type == "backpage") {
                $content .= getNews_backpage($cat, $limit);
            } else if ($type == "backpage_h") {
                $content .= getNews_backpage_h($cat, $limit);
            }
        }
    }
    return $content;
}
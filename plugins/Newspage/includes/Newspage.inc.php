<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

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

function get_news_byId($nid, $lang = null, $page = null){
    global $config, $acl_auth, $ml, $db;         

    empty($page) ? $page = 1 : false;

    $where_ary = array ( "nid" => $nid, "page" => $page);

    if (defined('MULTILANG') && $lang != null) {
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if($site_lang['iso_code'] == $lang) {
                $where_ary['lang_id'] = $site_lang['lang_id'];
                break;
            }
        }
    }
    $query = $db->select_all("news", $where_ary, "LIMIT 1");

    if($db->num_rows($query) <= 0 ) {
        $query = $db->select_all("news", array("nid" => $nid, "page" => $page), "LIMIT 1");
        $db->num_rows($query) > 0 ? $msgbox['MSG'] = "L_NEWS_WARN_NOLANG" : $msgbox['MSG'] = "L_NEWS_DELETE_NOEXISTS";
        do_action("message_box", $msgbox);
        return false;
    }
    $news_row = $db->fetch($query);

    if( 'ACL' && !empty($news_row['acl']) && !$acl_auth->acl_ask($news_row['acl']) ) {
            $msgbox['MSG'] = "L_ERROR_NOACCESS";
            do_action("message_box", $msgbox);
            return false;
    }
    $db->free($query);

    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin") ) {
        $msgbox['MSG'] = "L_NEWS_ERROR_WAITINGMOD";
        do_action("message_box", $msgbox);
        return false;
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
    $data .= "'>". $LANGDATA['L_SEND_NEWS'] ."</a>";
    $data .= "</li>";

    return $data;
}

function news_check_display_submit () {
    global $config, $acl_auth, $sm;
    $user = $sm->getSessionUser();

    if( (!empty($user) && $config['NEWS_SUBMIT_REGISTERED'])
            || (empty($user) && $config['NEWS_SUBMIT_ANON'] ) ) {
        return true;
    }
    if(defined('ACL') && ( $acl_auth->acl_ask("news_submit||admin_all") )) {
        return true;
    } else if(!defined('ACL') && !empty($user) && $user['isAdmin']) {
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

function news_clean_featured($lang_id) {
    global $db;

    $set_ary['featured'] = '0';
    if (defined('MULTILANG')) {
        $where_ary['lang_id'] = $lang_id;
        $db->update("news", $set_ary, $where_ary);
    } else {
        $db->update("news", $set_ary);
    }
}

function news_friendly_title($title) {
    //FIX: better way for clean all those character?
    $friendly_filter = array('"','\'','?','$',',','.','‘','’',':',';','[',']','{','}','*','!','¡','¿','+','<','>','#','@','|','~','%','&','(',')','=','`','´','/','º','ª','\\');
    $friendly = str_replace(' ', "-", $title);
    $friendly = str_replace($friendly_filter, "", $friendly);    

    return $friendly;
}
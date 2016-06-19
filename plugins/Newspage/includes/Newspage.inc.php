<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_format_media($media) {
    //TODO MEDIA TYPES   
    if ($media['type'] == 'image') {
        $result =  "<img src=" . $media['link'] ." alt=". $media['link'] . "/>"; //TODO FIX ALT        
    } else if ($media['type'] == 'source') {
        $url = parse_url($media['link']);        
        $domain = $url['host'];
        $result = "<a rel='nofollow' target='_blank' href='{$media['link']}'>$domain</a>";
    } else {
        return false;
    }
    return $result;
}

function get_news_byId($nid, $lang = null){
    global $config, $acl_auth, $ml, $db, $tpl;         
    
    $where_ary['nid'] = $nid;
    if (defined('MULTILANG') && 'MULTILANG' && $lang != null) {        
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
        if( ($news_row = get_news_byId($nid)) == false) {
            $msgbox['MSG'] = "L_NEWS_NOT_EXIST";
            do_action("message_box", $msgbox);
            return false;
        } else {
            $msgbox['MSG'] = "L_NEWS_WARN_NOLANG";
            do_action("message_box", $msgbox);
            return false;            
        }
    }
    $news_row = $db->fetch($query);
    
    if( 'ACL' && !empty($acl_auth) && !empty($news_row['acl'])) {
        if(!$acl_auth->acl_ask($news_row['acl'])) {
            $msgbox['MSG'] = "L_ERROR_NOACCESS";
            do_action("message_box", $msgbox); 
            return false;
        }
    } 
    $db->free($query);

    if ($config['NEWS_MODERATION'] && $news_row['moderation'] && !S_GET_INT("admin") ) {
        $msgbox['MSG'] = "L_NEWS_ERROR_WAITINGMOD";
        do_action("message_box", $msgbox);
        return false;        
    }         

    return $news_row;
}

function get_news_main_link_byID($nid) {
    global $db;
    
    $query = $db->select_all("links", array("source_id" => "$nid", "itsmain" => 1), "LIMIT 1");    
    if ($db->num_rows($query) <= 0) {
        return false;
    } else {
        $media = $db->fetch($query);
    }
    $db->free($query);

    return $media;   
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
        $data .= "{$config['WEB_LANG']}/?";
    } else {
        $data .= "?lang={$config['WEB_LANG']}&";
    }
    $data .= "sendnews=1'>". $LANGDATA['L_SEND_NEWS'] ."</a>";
    $data .= "</li>";
    return $data;    
}

function news_check_display_submit () {
    global $config, $acl_auth;
    
    if( (empty($_SESSION['isLogged'])  && $config['NEWS_SUBMIT_ANON'] == 1) ||  // Anon can send
         ( !empty($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1 && $config['NEWS_SUBMIT_REGISTERED'] == 1) // Registered can send
      ){       
            return true;
    } else {
        if(defined('ACL') && 'ACL') {
            if ( $acl_auth->acl_ask("news_submit||admin_all") ) {
                return true;
            }
        } else {
            $user = $sm->getSessionUser();
            if ($user['isAdmin']) {
                return true;
            }
        }
    }    
    return false;
}

function news_get_related($nid) {
    global $db;
    $plugin = "Newspage";
    $type = "related";
    
    $query = $db->select_all("links", array("source_id" => $nid, "plugin" => $plugin, "type" => $type));
    if ($db->num_rows($query) > 0) {
        while ($relate_row = $db->fetch($query)) {
            $related[] = array (
                "rid"  => $relate_row['rid'],
                "link" => $relate_row['link'],
                "type" => $relate_row['type']
            );
        }
    } else {
        return false;
    }
    
    return $related;
}

function news_clean_featured($lang_id) {
    global $db;
       
    $set_ary['featured'] = '0';
    if (defined('MULTILANG') && 'MULTILANG') {
        $where_ary['lang_id'] = $lang_id;
    }
    $db->update("news", $set_ary, $where_ary);
}
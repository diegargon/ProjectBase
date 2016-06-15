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
        $result = "<a href='{$media['link']}'>$domain</a>";
    } else {
        return false;
    }
    return $result;
}

function get_news_byId($nid, $lang = null){
    global $acl_auth, $ml, $db;         

    $where_ary['nid'] = $nid;
    if (defined('MULTILANG') && 'MULTILANG' && $lang != null) {        
        $site_langs = $ml->get_site_langs();
        foreach ($site_langs as $site_lang) {
            if($site_lang['iso_code'] == $lang) {
                //$q .= "AND lang_id = '{$site_lang['lang_id']}'";
                $where_ary['lang_id'] = $site_lang['lang_id'];
                break;
            }
        }
    }                
    $query = $db->select_all("news", $where_ary, "LIMIT 1");
    
    if($db->num_rows($query) == 0 ) {        
        return false;
    }
    $row = $db->fetch($query);
    
    if( 'ACL' && !empty($acl_auth) && !empty($row['acl'])) {
        if(!$acl_auth->acl_ask($row['acl'])) {
            return 403;
        }
    } 
    $db->free($query);

    return $row;
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
    $data .= "<a rel='nofollow' href='/{$config['WEB_LANG']}/?sendnews=1'>". $LANGDATA['L_SEND_NEWS'] ."</a>";
    $data .= "</li>";
    return $data;    
}

function news_check_display_submit () {
    global $config, $acl_auth;
    
    if(
            (empty($_SESSION['isLogged'])  && $config['NEWS_SUBMIT_ANON'] == 1) ||  // Anon can send
            ( !empty($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1 && $config['NEWS_SUBMIT_REGISTERED'] = 1) // Registered can send
                ){       
            return true;
    } else {
        if(defined('ACL') && 'ACL') {
            if ( $acl_auth->acl_ask("news_submit") ||
                 $acl_auth->acl_ask("admin_all")
                    ) {
                return true;
            }
        }
    }    
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
<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_format_media($media) {
    //TODO MEDIA TYPES   
    if ($media['type'] == 'image') {
        $result =  "<img src=" . $media['link'] ." alt=". $media['link'] . "/>"; //TODO FIX ALT        
    } else {
        return false;
    }
    return $result;
}

function get_news_byId($id, $lang = null){
    global $config, $acl_auth;         
    
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE nid = $id ";
    if (defined('MULTILANG') && 'MULTILANG' && $lang != null) {        
        $LANGS = do_action("get_site_langs");
        foreach ($LANGS as $content) {
            if($content->iso_code == $lang) {
                $q .= "AND lang_id = '$content->lang_id'";
                break;
            }
        }
    }                
    $q .= " LIMIT 1";
    $query = db_query($q);
    if(db_num_rows($query) == 0 ) {        
        return false;
    }
    $row = db_fetch($query);
    
    if( 'ACL' && !empty($acl_auth) && !empty($row['acl'])) {
        if(!$acl_auth->acl_ask($row['acl'])) {
            return 403;
        }
    } 
    db_free_result($query);

    return $row;
}

function get_news_media_byID($id) {
    global $config;
    
    $query = db_query("SELECT * FROM {$config['DB_PREFIX']}links WHERE source_id = '$id' AND plugin='Newspage' ");    
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch($query)) {
            $media[] = array (
                "rid" => $row['rid'], 
                "type" => $row['type'], 
                "link" => $row['link'], 
                "itsmain" => $row['itsmain']);        
        }                
    } else {
        $media = false;
    }   
    db_free_result($query);

    return $media;   
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
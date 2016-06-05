<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_page_edit() {
    global $config, $LANGDATA, $acl_auth;    
    
    $nid = S_GET_INT("newsedit");
    $lang_id = S_GET_INT("lang_id");
    
    if ($nid == false || $lang_id == false) {
        echo "ERROR 1"; 
    }    
    $q = "SELECT * FROM {$config['DB_PREFIX']}news WHERE nid = '$nid' AND lang_id = '$lang_id' LIMIT 1"; 
    $query = db_query($q);
    if (db_num_rows($query) <= 0) {
        echo "ERROR 2";
    }
    $news_data = db_fetch($query);    
    $news_data['NEWS_FORM_TITLE'] = $LANGDATA['L_NEWS_EDIT_NEWS'];
    
    if (defined('ACL') && 'ACL') {        
        if($acl_auth->acl_ask("news_admin") || $acl_auth->acl_ask("admin_all")) {
            $news_data['select_acl'] = $acl_auth->get_roles_select("news", $news_data['acl']);
            $can_change_author = 1;
        }
    } 
    empty($can_change_author) ?  $news_data['can_change_author'] = "disabled" : $news_data['can_change_author'] = ""; 
    $news_data['select_categories'] = news_get_categories_select($news_data);
    
    if (defined('MULTILANG') && 'MULTILANG') {
        if ( ($site_langs = news_get_sitelangs($news_data)) != false ) {
            $news_data['select_langs'] = $site_langs;
        }
    }  
    
    if ( ($media = news_getMedia($news_data['nid'])) != false) {
        $news_data['main_media'] = $media['link'];
    }    
    $news_data['update'] = $nid;  
    $news_data['current_langid'] = $news_data['lang_id'];
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_form", $news_data));     
}

function news_getMedia($nid) {
    global $config;
    
    $q = "SELECT * FROM {$config['DB_PREFIX']}links WHERE source_id = '$nid' LIMIT 1";
    $query = db_query($q);
    
    if (db_num_rows($query) <= 0) {
        return false;
    }    
    $media = db_fetch($query);
    
    return $media;    
}

function news_update($news_data) {
    global $config;

    $nid = $news_data['update'];
    $current_langid = $news_data['current_langid'];
    $lang_id = ML_iso_to_id($news_data['lang']);

    $q = "SELECT * FROM {$config['DB_PREFIX']}news WHERE nid = '$nid' AND lang_id = '$current_langid' ";
    $query = db_query($q);
    if (db_num_rows($query) <= 0) {
        return false;
    }

    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl=""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($news_data['lang']) ;
    
    $q = "UPDATE {$config['DB_PREFIX']}news SET"
        . " lang_id = '$lang_id', title = '{$news_data['title']}', lead = '{$news_data['lead']}', text = '{$news_data['text']}', featured = '{$news_data['featured']}', "
        . " author = '{$news_data['author']}', category = '{$news_data['category']}',  lang = '{$news_data['lang']}', acl = '$acl'"                            
        . " WHERE nid = '$nid' AND lang_id = '$current_langid'";

     db_query($q);
        
    if (!empty($news_data['main_media'])) {
        //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
        $source_id = $nid;
        $plugin = "Newspage";
        $type = "image";
        
        $q = "SELECT * FROM {$config['DB_PREFIX']}links WHERE source_id = '$source_id' AND plugin = '$plugin' AND itsmain = '1' ";
        $query = db_query($q);

        if (db_num_rows($query) > 0) {        
            $q = "UPDATE {$config['DB_PREFIX']}links SET"
                . " link = '{$news_data['main_media']}' "
                . " WHERE source_id = '$source_id' ";
        } else {
            $q = "INSERT INTO {$config['DB_PREFIX']}links ("
                . "source_id, plugin, type, link, itsmain"
                . ") VALUES ("
                . "'$source_id', '$plugin', '$type', '{$news_data['main_media']}', '1'"
                . ");";              
        }
        db_query($q);
    }        
    
    return true;
}
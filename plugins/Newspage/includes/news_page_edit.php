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
    $news_data['update'] = 1;    
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
    return true;
}
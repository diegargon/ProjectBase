<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
 
function news_new_form($post_data = null) {
    global $config, $LANGDATA, $acl_auth;
    
    $data['NEWS_FORM_TITLE'] = $LANGDATA['L_SEND_NEWS'];
            
    if (  isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1) {
        $user = SMBasic_getUserbyID(S_VAR_INTEGER($_SESSION['uid']), 11);        
    } else {
        $user['username'] = $LANGDATA['L_NEWS_ANONYMOUS'];
    }
    $data['author'] = $user['username'];    
    
    if (defined('MULTILANG') && 'MULTILANG') {
        if ( ($site_langs = news_get_sitelangs()) != false ) {
            $data['select_langs'] = $site_langs;
        }
    }    
    if (defined('ACL') && 'ACL') {        
        if($acl_auth->acl_ask("news_admin||admin_all")) { //|| $acl_auth->acl_ask("admin_all")) {
            $data['select_acl'] = $acl_auth->get_roles_select("news");
            $can_change_author = 1;
        }
    } 
    empty($can_change_author) ?  $data['can_change_author'] = "disabled" : $data['can_change_author'] = "";    
    $data['select_categories'] = news_get_categories_select();    
      
    if(!empty($post_data)) {
        $data = array_merge($data, $post_data);
    }  
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Newspage", "news_form", $data));     
}

function news_create_new($news_data) {
    global $config, $ml;
    
    $nid = db_get_next_num("nid", $config['DB_PREFIX']."news"); 
    if (defined('MULTILANG') && 'MULTILANG') {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }
        
    
    if ( ($uid = SMBasic_getUserID()) == false ) {
        $uid = 0;
    }    
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl=""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($news_data['lang']) ;

    if ($news_data['featured'] == 1 && $config['NEWS_MODERATION'] == 1) {
        $moderation = 0;
    } else if ($config['NEWS_MODERATION'] == 1){
        $moderation = 1;
    }    
    $q = "INSERT INTO {$config['DB_PREFIX']}news ("
        . "nid, lang_id, title, lead, text,  featured, author, author_id, category, lang, acl, moderation"    
        . ") VALUES ("
        . "'$nid', '$lang_id', '{$news_data['title']}', '{$news_data['lead']}', '{$news_data['text']}', "         
        . "'{$news_data['featured']}', '{$news_data['author']}', '$uid', '{$news_data['category']}', '{$news_data['lang']}', '$acl', '$moderation'"       
        . ");";       
    $query = db_query($q);    
    if (!empty($news_data['main_media'])) {
        $source_id = $nid;
        $plugin = "Newspage";
        //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
        $type = "image";
        $q = "INSERT INTO {$config['DB_PREFIX']}links ("
            . "source_id, plugin, type, link, itsmain"
            . ") VALUES ("
            . "'$source_id', '$plugin', '$type', '{$news_data['main_media']}', '1'"
            . ");";    
        $query = db_query($q);
    }
    return true;
}

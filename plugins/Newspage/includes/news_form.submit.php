<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
 
function news_new_form($post_data = null) {
    global $LANGDATA, $acl_auth, $tpl, $sm;
    
    $data['NEWS_FORM_TITLE'] = $LANGDATA['L_SEND_NEWS'];
            
    if (  isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1) {        
        $user = $sm->getSessionUser();        
    } else { 
        $user['username'] = $LANGDATA['L_NEWS_ANONYMOUS'];
    }
    $data['author'] = $user['username'];    
    
    if (defined('MULTILANG') && 'MULTILANG') {
        if ( ($site_langs = news_get_all_sitelangs()) != false ) {
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
    !empty($post_data) ? $data = array_merge($data, $post_data) : false;
      
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $data));     
}

function news_create_new($news_data) {
    global $config, $ml, $db;
        
    $nid = $db->get_next_num("news", "nid");
    if (defined('MULTILANG') && 'MULTILANG') {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }        
 
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl=""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($lang_id) ;

    if ($news_data['featured'] == 1 && $config['NEWS_MODERATION'] == 1) {
        $moderation = 0;
    } else if ($config['NEWS_MODERATION'] == 1){
        $moderation = 1;
    } else {
        $moderation = 0;
    }
    
    $news_data['title'] = $db->escape_strip($news_data['title']);
    $news_data['lead'] = $db->escape_strip($news_data['lead']);
    $news_data['text'] = $db->escape_strip($news_data['text']);
    
    $insert_ary = array (
        "nid" => $nid,
        "lang_id" => $lang_id,
        "title" => $db->escape_strip($news_data['title']),
        "lead" => $db->escape_strip($news_data['lead']),
        "text" => $db->escape_strip($news_data['text']),
        "featured" => $news_data['featured'],
        "author" => $news_data['author'],
        "author_id" => $news_data['author_id'],
        "category" => $news_data['category'],
        "lang" => $news_data['lang'],
        "acl" => $acl,
        "moderation" => $moderation
    );
    $db->insert("news", $insert_ary);
    //MEDIA LINK
    if (!empty($news_data['main_media'])) {
        $source_id = $nid;
        $plugin = "Newspage";
        //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
        $type = "image";
        $insert_ary = array (
            "source_id" => $source_id,
            "plugin" => $plugin,
            "type" => $type,
            "link" => $news_data['main_media'],
            "itsmain" => 1
        );
        $db->insert("links", $insert_ary);
    }
    //SOURCE LINK
    if (!empty($news_data['news_source'])) {
        $source_id = $nid;
        $plugin = "Newspage";
        $type = "source";
        $insert_ary = array (
            "source_id" => $source_id,
            "plugin" => $plugin,
            "type" => $type,
            "link" => $news_data['news_source']
        );
        $db->insert("links", $insert_ary);        
    }    
   //NEW RELATED
    if (!empty($news_data['news_new_related'])) {
        $source_id = $nid;
        $plugin = "Newspage";
        $type = "related";
        $insert_ary = array ( 
            "source_id" => $source_id, "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        );
        $db->insert("links", $insert_ary);        
    }    
    return true;
}
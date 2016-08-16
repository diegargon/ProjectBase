<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_edit($news_data) {
    global $config, $LANGDATA, $acl_auth, $tpl;    

    $news_data['NEWS_FORM_TITLE'] = $LANGDATA['L_NEWS_EDIT_NEWS'];

    if ($news_data['news_auth'] == "admin") {        
        $news_data['select_acl'] = $acl_auth->get_roles_select("news", $news_data['acl']);
        $news_data['can_change_author'] = "";
    } else {
        $news_data['can_change_author'] = "disabled";
    } 

    if ( $news_data['news_auth'] == "admin" || $news_data['news_auth'] == "author") {        
        $news_data['select_categories'] = news_get_categories_select($news_data);
        if ( ($news_source = get_news_source_byID($news_data['nid'])) != false) {
            $news_data['news_source'] = $news_source['link'];
        }
        if($config['NEWS_RELATED'] && ($news_related = news_get_related($news_data['nid'])) ) {        
            $news_data['news_related'] = "";
            foreach ($news_related as $related)  {
                $news_data['news_related'] .= "<input type='text' class='news_link' name='news_related[{$related['link_id']}]' value='{$related['link']}' />\n";
            }
        }                    
    }
    if (defined('MULTILANG') && ($site_langs = news_get_available_langs($news_data)) != false) {
        $news_data['select_langs'] = $site_langs;
    }  
    
    do_action("news_edit_form_add", $news_data);
    news_editor_getBar();

    $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OTHER_OPTION","<input type='hidden' value='1' name='news_update' />" );
    $tpl->addto_tplvar("NEWS_FORM_BOTTOM_OTHER_OPTION","<input type='hidden' value='{$news_data['lang_id']}' name='news_current_langid' />" );
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $news_data));
}

function news_check_edit_authorized() {
    global $config, $sm, $acl_auth;

    $nid = S_GET_INT("newsedit", 11, 1);
    $lang = S_GET_CHAR_AZ("lang", 2, 2);
    $page = S_GET_INT("page", 11, 1);

    if ($nid == false || $lang == false || $page == false) {
        $msgbox['MSG'] = "L_NEWS_NOT_EXIST";
        do_action("message_box", $msgbox);
        return false;
    }
    if( ! $news_data = get_news_byId($nid, $lang, $page)) {
        return false; //get_news... error already setting
    }
    if ( (!$user = $sm->getSessionUser()) ) {
        $msgbox['MSG'] = "L_ERROR_NOACCESS";
        do_action("message_box", $msgbox);        
    } else if ($user['uid'] > 0) {            
        $news_data['tos_checked'] = 1;
    }

    if( (defined('ACL') && $acl_auth->acl_ask("admin_all||news_admin"))
            || (!defined('ACL') && $user['isAdmin'])
            ) { 
        $news_data['news_auth'] = "admin";
        return $news_data;
    } 

    if ( (($news_data['author'] == $user['username']) && $config['NEWS_AUTHOR_CAN_EDIT']) ) {
        $news_data['news_auth'] = "author";
        return $news_data;
    }
    if ( (($news_data['translator'] == $user['username']) && $config['NEWS_TRANSLATOR_CAN_EDIT']) ) {
        $news_data['news_auth'] = "translator";
        return $news_data;
    }

    $msgbox['MSG'] = "L_ERROR_NOACCESS";
    do_action("message_box", $msgbox); 
    return false;
}

function news_full_update($news_data) {
    global $config, $db, $ml;

    if(empty($news_data['page']) || empty($news_data['current_langid'])) {
        return false; //TODO ERROR?
    }

    $news_data['nid'] = S_GET_INT("nid");    
    $current_langid = $news_data['current_langid'];
    
    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "$current_langid"));
    if ( ($num_pages = $db->num_rows($query))  <= 0) {
        return false;
    }
   
    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = ""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($lang_id) ;
    !isset($news_data['news_translator']) ? $news_data['news_translator'] = "" : false;

        
    $set_ary = array (
      "lang_id" => $lang_id, "title" => $news_data['title'],  "lead" => $news_data['lead'],  "text" => $news_data['text'],  
        "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], "category" => $news_data['category'],
        "lang" => $news_data['lang'], "acl" => $acl, "translator" => $news_data['news_translator']
    );

    do_action("news_edit_mod_set", $set_ary);

    $where_ary = array ( 
        "nid" => "{$news_data['nid']}", "lang_id" => "$current_langid", "page" => $news_data['page']
    );
    $db->update("news", $set_ary, $where_ary);
    //UPDATE ACL/CATEGORY/LANG/FEATURE on pages;
    if ($num_pages > 1) { 
        $page_set_ary = array (
            "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], 
            "category" => $news_data['category'], "lang" => $news_data['lang']
        );
        $page_where_ary = array ( 
            "nid" => "{$news_data['nid']}", "lang_id" => "$current_langid", "page" => array("operator" => "!=", "value" => $news_data['page'])
        );
        $db->update("news", $page_set_ary, $page_where_ary);
    }
    //Custom/MOD
    do_action("news_form_update", $news_data);
  
    //SOURCE LINK
    if (!empty($news_data['news_source'])) {                
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "source";

        $query = $db->select_all("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin ), "LIMIT 1");
        if ($db->num_rows($query) > 0) {        
            $db->update("links", array("link" => $news_data['news_source']), array("source_id" => $source_id, "type" => $type, "plugin" => $plugin));
        } else {
            $insert_ary = array ( 
                "source_id" => $source_id, "plugin" => $plugin,
                "type" => $type, "link" => $news_data['news_source'],
            );
            $db->insert("links", $insert_ary);
        }
    } else {
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "source";
        $db->delete("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin), "LIMIT 1");
    }         
    //NEW RELATED
    if (!empty($news_data['news_new_related'])) {      
        $source_id = $news_data['nid'];
        $plugin = "Newspage";
        $type = "related";
        $insert_ary = array ( 
            "source_id" => $source_id, "plugin" => $plugin,
            "type" => $type, "link" => $news_data['news_new_related'],
        );
        $db->insert("links", $insert_ary);        
    }
    //OLD RELATED
    if(!empty($news_data['news_related'])) {        
        foreach($news_data['news_related'] as $link_id => $value) {
            if (S_VAR_INTEGER($link_id)) { //value its checked on post $link_id no 
                if(empty($value)) {
                    $db->delete("links", array("link_id" => $link_id), "LIMIT 1");
                } else {                    
                    $db->update("links", array("link" => $value), array("link_id" => $link_id), "LIMIT 1");
                }
            }
        }
    }
    return true;
}

function news_limited_update($news_data) {
    global $config, $db, $ml;

    if(empty($news_data['page']) || empty($news_data['current_langid'])) {
        return false; //TODO ERROR?
    }
    $news_data['nid'] = S_GET_INT("nid");    
    $current_langid = $news_data['current_langid'];
    
    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }

    $query = $db->select_all("news", array("nid" => "{$news_data['nid']}", "lang_id" => "$current_langid"));
    if ( ($num_pages = $db->num_rows($query))  <= 0) {
        return false;
    }
       
    $set_ary = array (
      "lang_id" => $lang_id, "title" => $news_data['title'],  "lead" => $news_data['lead'],  "text" => $news_data['text'],
        "lang" => $news_data['lang']
    );
    
    $where_ary = array ( 
        "nid" => "{$news_data['nid']}", "lang_id" => "$current_langid", "page" => $news_data['page']
    );
    $db->update("news", $set_ary, $where_ary);

    return true;    
}
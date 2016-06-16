<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
 
function news_page_edit() {
    global $config, $LANGDATA, $acl_auth, $tpl, $db;    
    
    $nid = S_GET_INT("newsedit");
    $lang_id = S_GET_INT("lang_id");
    
    if ($nid == false || $lang_id == false) {
        echo "ERROR 1";  //TODO error mesage
        exit();
    }    
    $query = $db->select_all("news", array("nid" => "$nid", "lang_id" => "$lang_id"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        echo "ERROR 2: No existe esa noticia"; //TODO error mesage
        exit();
    }
    $news_data = $db->fetch($query);    
    $news_data['NEWS_FORM_TITLE'] = $LANGDATA['L_NEWS_EDIT_NEWS'];
    
    if (defined('ACL') && 'ACL') {        
        if($acl_auth->acl_ask("news_admin||admin_all")) {// || $acl_auth->acl_ask("admin_all")) {
            $news_data['select_acl'] = $acl_auth->get_roles_select("news", $news_data['acl']);
            $can_change_author = 1;
        }
    } 
    empty($can_change_author) ?  $news_data['can_change_author'] = "disabled" : $news_data['can_change_author'] = ""; 
    $news_data['select_categories'] = news_get_categories_select($news_data);
    
    if (defined('MULTILANG') && 'MULTILANG') {
        if ( ($site_langs = news_get_available_langs($news_data)) != false ) {
            $news_data['select_langs'] = $site_langs;
        }
    }  
    
    if ( ($media = get_news_main_link_byID($news_data['nid'])) != false) {
        $news_data['main_media'] = $media['link'];
    }  
    
    if ( ($news_source = get_news_source_byID($news_data['nid'])) != false) {
        $news_data['news_source'] = $news_source['link'];
    }
    
    if($config['NEWS_RELATED']) {        
        $news_related = news_get_related($news_data['nid']);
        if($news_related) {
            $news_data['news_related'] = "";
            foreach ($news_related as $related)  {
                $news_data['news_related'] .= "<input type='text' class='news_link' name='news_related[{$related['rid']}]' value='{$related['link']}' />\n";
            }
        }
    }    
    $news_data['update'] = $nid;  
    $news_data['current_langid'] = $news_data['lang_id'];
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $news_data));     
}

function news_update($news_data) {
    global $config, $db, $ml;

    $nid = $news_data['update'];
    $current_langid = $news_data['current_langid'];
    
    if (defined('MULTILANG') && 'MULTILANG') {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }

    $query = $db->select_all("news", array("nid" => "$nid", "lang_id" => "$current_langid"));
    if ($db->num_rows($query) <= 0) {
        return false;
    }

    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl = ""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($lang_id) ;

    $news_data['title'] = $db->escape_strip($news_data['title']);
    $news_data['lead'] = $db->escape_strip($news_data['lead']);
    $news_data['text'] = $db->escape_strip($news_data['text']);    
    
    $set_ary = array (
      "lang_id" => $lang_id, "title" => $news_data['title'],  "lead" => $news_data['lead'],  "text" => $news_data['text'],  
        "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], "category" => $news_data['category'],
        "lang" => $news_data['lang'], "acl" => $acl
    );

    $where_ary = array ( 
        "nid" => "$nid", "lang_id" => "$current_langid"
    );
    $db->update("news", $set_ary, $where_ary);

    //MEDIA
    if (!empty($news_data['main_media'])) {        
        //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
        $source_id = $nid;
        $plugin = "Newspage";
        $type = "image";

        $query = $db->select_all("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin, "itsmain" => 1 ));
        if ($db->num_rows($query) > 0) {       
            $db->update("links", array("link" => $news_data['main_media']), array("source_id" => $source_id, "type" => $type, "itsmain" => 1));
        } else {
            $insert_ary = array ( 
                "source_id" => $source_id, "plugin" => $plugin,
                "type" => $type, "link" => $news_data['main_media'],
                "itsmain" => 1
            );            
            $db->insert("links", $insert_ary);
        }
    }        
    
    //SOURCE LINK
    if (!empty($news_data['news_source'])) {                
        $source_id = $nid;
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
        $source_id = $nid;
        $plugin = "Newspage";
        $type = "source";
        $db->delete("links", array("source_id" => $source_id, "type" => $type, "plugin" => $plugin), "LIMIT 1");
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
    //OLD RELATED
    if(!empty($news_data['news_related'])) {        
        foreach($news_data['news_related'] as $rid => $value) {
            if (S_VAR_INTEGER($rid)) { //value its checked on post $rid no 
                if(empty($value)) {
                    $db->delete("links", array("rid" => $rid), "LIMIT 1");
                } else {
                    $db->update("links", array("link" => $value), array("rid" => $rid), "LIMIT 1");
                }
            }
        }
    }
    return true;
}

function news_new_lang() {
   global $config, $LANGDATA, $acl_auth, $tpl, $db, $sm;    
    
    $nid = S_GET_INT("news_new_lang");
    $lang_id = S_GET_INT("lang_id");
    
    if ($nid == false || $lang_id == false) {
        echo "ERROR 1";  //TODO error mesage
        exit();
    }    
    $query = $db->select_all("news", array("nid" => "$nid", "lang_id" => "$lang_id"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        echo "ERROR 2: No existe esa noticia"; //TODO error mesage
        exit();
    }
    $news_data = $db->fetch($query);    
    $news_data['NEWS_FORM_TITLE'] = $LANGDATA['L_NEWS_NEWLANG'];
    
    if (  isset($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1) {        
        $translator = $sm->getSessionUser();        
    } else { 
        $translator['username'] = $LANGDATA['L_NEWS_ANONYMOUS'];
    }
    $news_data['translator'] = $translator['username'];  
    
    if (defined('ACL') && 'ACL') {        
        if($acl_auth->acl_ask("news_admin||admin_all")) {// || $acl_auth->acl_ask("admin_all")) {
            $news_data['select_acl'] = $acl_auth->get_roles_select("news", $news_data['acl']);
            $can_change_author = 1;
        }
    } 
    empty($can_change_author) ?  $news_data['can_change_author'] = "disabled" : $news_data['can_change_author'] = ""; 
    $news_data['select_categories'] = news_get_categories_select($news_data);
    
    if (defined('MULTILANG') && 'MULTILANG') {
        if ( ($site_langs = news_get_missed_langs($news_data['nid'])) != false ) {
            $news_data['select_langs'] = $site_langs;
        } else {
            echo "Error: 3 Already translate to all languages";
            exit();
        }
    }  
    
    if ( ($media = get_news_main_link_byID($news_data['nid'])) != false) {
        $news_data['main_media'] = $media['link'];
    }  
    
    if ( ($news_source = get_news_source_byID($news_data['nid'])) != false) {
        $news_data['news_source'] = $news_source['link'];
    }
    
    if($config['NEWS_RELATED']) {        
        $news_related = news_get_related($news_data['nid']);
        if($news_related) {
            $news_data['news_related'] = "";
            foreach ($news_related as $related)  {                
                $news_data['news_related'] .= "<input disabled type='text' class='news_link' name='news_related[{$related['rid']}]' value='{$related['link']}' />\n";
                $news_data['news_related'] .= "<input type='hidden' name='news_related[{$related['rid']}]' value='{$related['link']}' />\n";
            }
        }
    }    
    $news_data['post_newlang'] = $nid;  
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Newspage", "news_form", $news_data));            
}


function news_translate($news_data) {    
    global $config, $db, $ml;

    $nid = $news_data['post_newlang'];
    
    if (defined('MULTILANG') && 'MULTILANG') {
        $lang_id = $ml->iso_to_id($news_data['lang']);        
    } else {
        $lang_id  = $config['WEB_LANG_ID'];        
    }
    
    if(empty($nid) || empty($lang_id)) {
        return false;
    }

    $query = $db->select_all("news", array("nid" => "$nid", "lang_id" => "$lang_id"));
    if ($db->num_rows($query) > 0) {
        return false;
    }

    !empty($news_data['acl']) ? $acl = $news_data['acl'] : $acl=""; 
    empty($news_data['featured']) ? $news_data['featured'] = 0 : news_clean_featured($lang_id) ;

    $news_data['title'] = $db->escape_strip($news_data['title']);
    $news_data['lead'] = $db->escape_strip($news_data['lead']);
    $news_data['text'] = $db->escape_strip($news_data['text']);

    if ($news_data['featured'] == 1 && $config['NEWS_MODERATION'] == 1) {
        $moderation = 0;
    } else if ($config['NEWS_MODERATION'] == 1){
        $moderation = 1;
    }  else {
        $moderation = 0;
    }
    
    $insert_ary = array (
      "nid" => $nid, "lang_id" => $lang_id, "translator" => $news_data['news_translator'], "title" => $news_data['title'], 
      "lead" => $news_data['lead'],  "text" => $news_data['text'],  
      "featured" => $news_data['featured'], "author" => $news_data['author'], "author_id" => $news_data['author_id'], "category" => $news_data['category'],
      "lang" => $news_data['lang'], "acl" => $acl, "moderation" => $moderation
    );   
    $db->insert("news", $insert_ary);
    
    return true;
}
<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_get_categories_select($news_data = null) {
    global $db;
    $query = news_get_categories();
    
    $select = "<select name='news_category' id='news_category'>";
    while($row = $db->fetch($query)) {
        if( ($news_data != null) && ($row['cid'] == $news_data['category']) ) {
            $select .= "<option selected value='{$row['cid']}'>{$row['name']}</option>"; 
        } else {
            $select .= "<option value='{$row['cid']}'>{$row['name']}</option>"; 
        }        
    } 
    $select .= "</select>";
    
    return $select;
}

function news_get_categories() {
    global $config, $ml, $db;
    
    if (defined('MULTILANG') && 'MULTILANG') {
        $lang_id = $ml->iso_to_id($config['WEB_LANG']); 
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }
    
    $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id"));
    
    return $query;
}

function news_form_getPost() {
    global $acl_auth, $config, $sm;
       
    if (defined('ACL') && 'ACL') { //if admin can change author if not ignore news_author
        if( ( $acl_auth->acl_ask('news_admin') ) == true || ( $acl_auth->acl_ask('admin_all') ) == true ) {            
            isset($_POST['news_author']) ? $data['author'] = S_POST_STRICT_CHARS("news_author", 25,3) : false;
            if (!empty($data['author'])) {               
                if( ($selected_user = $sm->getUserByUsername($data['author'])) ) {
                    $data['author_id'] = $selected_user['uid'];                
                } else {
                    $data['author'] = false; // author not exists clear for use the session username.
                }
            }
        } 
    }
    if(empty($data['author'])) { 
        $session_user = $sm->getSessionUser();
        $data['author'] = $session_user['username'];
        $data['author_id'] = $session_user['uid'];
    }
    isset($_POST['news_title']) ? $data['title'] = S_POST_TEXT_UTF8("news_title") : false;
    isset($_POST['news_lead']) ? $data['lead'] = S_POST_TEXT_UTF8("news_lead") : false;
    isset($_POST['news_text']) ? $data['text'] = S_POST_TEXT_UTF8("news_text") : false;
    isset($_POST['news_category']) ? $data['category'] = S_POST_INT("news_category", 8) : false;
    isset($_POST['news_featured']) ? $data['featured'] = S_POST_INT("news_featured", 1) : false;     
    isset($_POST['news_lang']) ? $data['lang'] = S_POST_CHAR_AZ("news_lang", 2) : $data['lang'] = $config['WEB_LANG'];    
    isset($_POST['news_acl']) ? $data['acl'] = S_POST_STRICT_CHARS($_POST['news_acl']) : false; 
    !empty($_POST['news_main_media']) ? $data['main_media'] = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']) : $data['main_media'] = "";
    !empty($_POST['news_update']) ? $data['update'] = S_POST_INT("news_update", 11, 1) : $data['update'] = 0;
    !empty($_POST['news_current_langid']) ? $data['current_langid'] = S_POST_INT("news_current_langid", 8, 1) : $data['current_langid'] = 0;
    !empty($_POST['news_source']) ? $data['news_source'] = S_POST_URL("news_source") : false;
    !empty($_POST['news_new_related']) ? $data['news_new_related'] = S_POST_URL("news_new_related") : false;
    !empty($_POST['news_related']) ? $data['news_related'] = S_POST_URL("news_related") : false;
    !empty($_POST['news_translator']) ? $data['news_translator'] = S_POST_STRICT_CHARS("news_translator", 25, 3) : false;
    !empty($_POST['post_newlang']) ? $data['post_newlang'] = S_POST_INT("post_newlang") : false;
    return $data;
}


function news_form_process() {
    global $LANGDATA, $config;
    
    $news_data = news_form_getPost();
    //USERNAME/AUTHOR
    if (empty($news_data['author']) ) {
        $news_data['author'] = $LANGDATA['L_NEWS_ANONYMOUS']; //TODO CHECK if anonymous its allowed        
    }           
    if ($news_data['author'] == false) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_NEWS_ERROR_INCORRECT_AUTHOR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }    
    //UID/AUTHORUID       
    if  ( !empty($_SESSION['uid'])) {
        if (($news_data['uid'] = S_VAR_INTEGER($_SESSION['uid'])) == false ) {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);    
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;        
        }
    } else {
        $news_data['uid'] = 0;
    } 
    //TITLE        
    if($news_data['title'] == false) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    if( (strlen($news_data['title']) > $config['NEWS_TITLE_MAX_LENGHT']) || 
            (strlen($news_data['title']) < $config['NEWS_TITLE_MIN_LENGHT'])
            ){
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //LEAD    
    if($news_data['lead'] == false) {
        $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if( (strlen($news_data['lead']) > $config['NEWS_LEAD_MAX_LENGHT']) || 
            (strlen($news_data['lead']) < $config['NEWS_LEAD_MIN_LENGHT'])
            ){
        $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //TEXT
    if($news_data['text'] == false) {
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if( (strlen($news_data['text']) > $config['NEWS_TEXT_MAX_LENGHT']) || 
            (strlen($news_data['text']) < $config['NEWS_TEXT_MIN_LENGHT'])
            ){
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //CATEGORY
    if($news_data['category'] == false) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //MEDIA    
    if ( $config['NEWS_MAIN_MEDIA_REQUIRED'] || !empty($news_data['main_media'])) {
        if($news_data['main_media'] == -1 || empty($news_data['main_media'])) {
            $response[] = array("status" => "6", "msg" => $LANGDATA['L_NEWS_MEDIALINK_ERROR']);    
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;        
        }
    }
    //FEATURED
    //NOCHECK ATM
    //
    //ACL
    //NO CHECK ATM
    //

    //ALL OK SUBMIT or UPDATE

    if($news_data['update'] > 0) {
        if (news_update($news_data)) {
            $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'], "url" => $config['WEB_URL']);    
        } else {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
        }
    } else if($news_data['post_newlang'] > 0) {
        if (news_translate($news_data)) {
            $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_TRANSLATE_SUCESSFUL'], "url" => $config['WEB_URL']);    
        } else {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
        }        
    } else {
        if(news_create_new($news_data)) {
            $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_SUBMITED_SUCESSFUL'], "url" => $config['WEB_URL']);
        } else {
            $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
        }
    }
     echo json_encode($response, JSON_UNESCAPED_SLASHES);    

     return true;
}

function Newspage_FormScript() {
    $script = "";
    
    if (!check_jsScript("jquery.min.js")) {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }           
    $script .= "<script type=\"text/javascript\" src=\"plugins/Newspage/js/newsform.js\"></script>\n";
    
    return $script;
}
//Used when submit new news, get all site available langs and selected the default/user lang
function news_get_all_sitelangs() {  
    global $config, $ml; 

    $site_langs = $ml->get_site_langs();

    if (empty($site_langs)) { return false; }
    
    $select = "<select name='news_lang' id='news_lang'>";     
    foreach ($site_langs as $site_lang) {
        if($site_lang['iso_code'] == $config['WEB_LANG']) {
            $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        } else {            
            $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        }
    }        
    $select .= "</select>";

    return $select;
}
//used when edit news, omit langs that already have this news translate
function news_get_available_langs($news_data) {  
    global $config, $ml, $db; 

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) { return false; }
    
    if (!empty($news_data['lang'])) {
        $match_lang = $news_data['lang'];
    } else {
        $match_lang = $config['WEB_LANG'];
    }
    
    $select = "<select name='news_lang' id='news_lang'>";     
    foreach ($site_langs as $site_lang) {
        if($site_lang['iso_code'] == $match_lang) {
            $select .= "<option selected value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
        } else {
            $query = $db->select_all("news", array("nid" => $news_data['nid'], "lang_id" => $site_lang['lang_id']), "LIMIT 1");
            if ($db->num_rows($query) <= 0) {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
            }
        }
    }        
    $select .= "</select>";

    return $select;
}
//used when translate a news, omit all already translate langs include original
function news_get_missed_langs($nid) { 
    global $config, $ml, $db; 

    $nolang = 1;
    
    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) { return false; }
    
    $select = "<select name='news_lang' id='news_lang'>";     
    foreach ($site_langs as $site_lang) {
            $query = $db->select_all("news", array("nid" => $nid, "lang_id" => $site_lang['lang_id']), "LIMIT 1");
            if ($db->num_rows($query) <= 0) {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
                $nolang = 0;
            }
    }       
    $select .= "</select>";
    if ($nolang) {
        return false;
    } else {
        return $select;
    }
}
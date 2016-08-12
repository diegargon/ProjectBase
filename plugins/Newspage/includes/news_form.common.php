<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_get_categories_select($news_data = null, $disabled = null) {
    global $db;
    if (empty($disabled)) {
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
    } else {
        $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => $news_data['lang_id'], "cid" => $news_data['category'] ), "LIMIT 1");
        $cat = $db->fetch($query);
        $select = "<input type='text' value='{$cat['name']}' readonly />";
        $select .= "<input type='hidden' name='news_category' value='{$news_data['category']}' />";
    }
    return $select;
}

function news_get_categories() {
    global $config, $ml, $db;

    if (defined('MULTILANG')) {
        $lang_id = $ml->iso_to_id($config['WEB_LANG']); 
    } else {
        $lang_id = $config['WEB_LANG_ID'];
    }
    $query = $db->select_all("categories", array("plugin" => "Newspage", "lang_id" => "$lang_id"));

    return $query;
}

function news_form_getPost() {
    global $acl_auth, $sm, $LANGDATA, $db;

    $session_user = $sm->getSessionUser();
    if( (!defined('ACL') && $session_user['isAdmin'])
        || ( defined('ACL') && ( $acl_auth->acl_ask('news_admin||admin_all') ) == true) ) {

        !empty($_POST['news_author']) ? $data['author'] = S_POST_STRICT_CHARS("news_author", 25,3) : false;
        if (!empty($data['author'])) {
            if( ($selected_user = $sm->getUserByUsername($data['author'])) ) {
                $data['author_id'] = $selected_user['uid'];
            } else {
                $data['author'] = false; // author not exists clear for use the session username.
            }
        }
    }

    if(empty($data['author'])) {
        if (!empty($session_user)) {
            $data['author'] = $session_user['username'];
            $data['author_id'] = $session_user['uid'];
        } else {
            $data['author'] = $LANGDATA['L_NEWS_ANONYMOUS'];
            $data['author_id'] = 0;
        }
    }

    $data['nid'] = S_GET_INT("nid", 11, 1);
    $data['title'] = $db->escape_strip(S_POST_TEXT_UTF8("news_title"));
    $data['lead'] = $db->escape_strip(S_POST_TEXT_UTF8("news_lead"));
    $data['text'] = $db->escape_strip(S_POST_TEXT_UTF8("news_text"));
    $data['category'] = S_POST_INT("news_category", 8);
    $data['featured'] = S_POST_INT("news_featured", 1, 1);
    $data['lang'] = S_POST_CHAR_AZ("news_lang", 2);
    $data['acl'] = S_POST_STRICT_CHARS("news_acl");
    $data['current_langid'] = S_POST_INT("news_current_langid", 8, 1);
    $data['news_source'] = S_POST_URL("news_source");
    $data['news_new_related'] = S_POST_URL("news_new_related");
    $data['news_related'] = S_POST_URL("news_related");
    $data['news_translator'] = S_POST_STRICT_CHARS("news_translator", 25, 3);
    $data['post_newlang'] = S_POST_INT("post_newlang");
    $data['page'] = S_GET_INT("page", 11, 1);

    return $data;
}

function news_form_process($news_auth) {
    global $LANGDATA, $config;

    $news_data = news_form_getPost();

    if(news_form_common_field_check($news_data) == false) {
        return false;
    }

    if ($news_auth == "admin" || $news_auth == "author") {
        if (news_form_extra_check($news_data) == false) {
            return false;
        }
    }

    //ALL OK, check if SUBMIT, UPDATE or translate

    if(S_POST_INT("news_update") > 0) {
        if ($news_auth == "admin" || $news_auth == "author") {
            if (news_full_update($news_data)) {
                $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'], "url" => $config['WEB_URL']);
            } else {
                $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
            }
        } else if ($news_auth == "translator") {
            if (news_limited_update($news_data)) {
                $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_UPDATE_SUCESSFUL'], "url" => $config['WEB_URL']);
            } else {
                $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
            }
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

function news_form_common_field_check($news_data) {
    global $config, $LANGDATA;

    //USERNAME/AUTHOR
    if (empty($news_data['author']) ) {
        $news_data['author'] = $LANGDATA['L_NEWS_ANONYMOUS']; //TODO CHECK if anonymous its allowed 
        $news_data['author_id'] = 0;
    }
    if ($news_data['author'] == false) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_NEWS_ERROR_INCORRECT_AUTHOR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
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
    if(isset($_GET['page']) && $_GET['page'] > 1 ) {
      if( (strlen($news_data['lead']) > $config['NEWS_LEAD_MAX_LENGHT'])) {
            $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_MINMAX_ERROR']);
            echo json_encode($response, JSON_UNESCAPED_SLASHES);
            return false;            
        }        
    } else {
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

    return true;
}

function news_form_extra_check(&$news_data) {
    global $config, $LANGDATA;
    //CATEGORY
    if($news_data['category'] == false) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    //Source check valid if input
    if (!empty($_POST['news_source']) && $news_data['news_source'] == false && $config['NEWS_SOURCE']) {
        $response[] = array("status" => "7", "msg" => $LANGDATA['L_NEWS_E_SOURCE']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    //New related   check valid if input 
    if (!empty($_POST['news_new_related']) && $news_data['news_new_related'] == false && $config['NEWS_RELATED']) {
        $response[] = array("status" => "7", "msg" => $LANGDATA['L_NEWS_E_RELATED']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    //Old related  if input
    if (!empty($_POST['news_related']) && $news_data['news_related'] == false && $config['NEWS_RELATED']) {
        $response[] = array("status" => "8", "msg" => $LANGDATA['L_NEWS_E_RELATED']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    /* Custom /Mod Validators */
    if( ($return = do_action("news_form_add_check", $news_data)) && !empty($return) ) {        
        $response[] = array("status" => "9", "msg" => $return);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    //FEATURED
    //NOCHECK ATM
    //
    //ACL
    //NO CHECK ATM
    // 

    return true;
}

function Newspage_FormScript() {
    global $tpl;

    $tpl->AddScriptFile("standard", "jquery.min", "TOP" );
    $tpl->AddScriptFile("Newspage", "newsform", "BOTTOM" );
    $tpl->AddScriptFile("Newspage", "editor", "BOTTOM" );
}

function Newspage_FormPageScript() { //Used for new page and edit non main page for avoid lead check
    global $tpl;

    $tpl->AddScriptFile("standard", "jquery.min", "TOP" );
    $tpl->AddScriptFile("Newspage", "newsform_page", "BOTTOM" );
    $tpl->AddScriptFile("Newspage", "editor", "BOTTOM" );
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

    empty($news_data['lang']) ? $match_lang = $news_data['lang'] : $match_lang = $config['WEB_LANG'];

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
//used when translate a news, omit all already translate langs, exclude original lang too. just missed news langs
function news_get_missed_langs($nid, $page) { 
    global $ml, $db; 

    $nolang = 1;

    $site_langs = $ml->get_site_langs();
    if (empty($site_langs)) { return false; }

    $select = "<select name='news_lang' id='news_lang'>";     
    foreach ($site_langs as $site_lang) {
            $query = $db->select_all("news", array("nid" => $nid, "lang_id" => $site_lang['lang_id'], "page" => "$page"), "LIMIT 1");
            if ($db->num_rows($query) <= 0) {
                $select .= "<option value='{$site_lang['iso_code']}'>{$site_lang['lang_name']}</option>";
                $nolang = 0;
            }
    }       
    $select .= "</select>";

    return (!empty($nolang)) ? false : $select;
}

function news_editor_getBar() {
        global $tpl;
        do_action("news_add_editor_item");
        
        $content = $tpl->getTPL_file("Newspage", "NewsEditorBar");
        $tpl->addto_tplvar("NEWS_TEXT_BAR", $content);
}

function news_form_preview() { 
    global $db;
    require_once("parser.class.php");
       
    //$news_text = $_POST['news_text'];
    $news['news_text'] = $db->escape_strip(S_POST_TEXT_UTF8("news_text"));
    $news['news_text'] = stripcslashes($news['news_text']);
    !isset($news_parser) ? $news_parser = new parse_text : false;
    
    do_action("news_form_preview", $news);
    $content = $news_parser->parse($news['news_text']);
    
    echo $content;
    
}
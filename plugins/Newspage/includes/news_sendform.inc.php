<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
function news_sendnews_getPost() {
    global $acl_auth, $config;

    if (defined('ACL') && 'ACL') { //if admin can change author if not ignore news_author
        if( ( $acl_auth->acl_ask('news_admin') ) == true || ( $acl_auth->acl_ask('admin_all') ) == true ) {            
            isset($_POST['news_author']) ? $data['username'] = S_VAR_CHAR_AZ_NUM($_POST['news_author']) : false;
        } 
    }    
    isset($_POST['news_title']) ? $data['post_title'] = S_VAR_TEXT_ESCAPE($_POST['news_title']) : false;
    isset($_POST['news_lead']) ? $data['post_lead'] = S_VAR_TEXT_ESCAPE($_POST['news_lead']) : false;
    isset($_POST['news_text']) ? $data['post_text'] = S_VAR_TEXT_ESCAPE($_POST['news_text']) : false;
    isset($_POST['news_category']) ? $data['post_category'] = S_VAR_INTEGER($_POST['news_category'], 8) : false;
    isset($_POST['news_featured']) ? $data['post_featured'] = S_VAR_INTEGER($_POST['news_featured'], 1) : false; 
    isset($_POST['news_lang']) ? $data['post_lang'] = S_VAR_TEXT_ESCAPE($_POST['news_lang']) : $data['post_lang'] = $config['WEB_LANG'];
    isset($_POST['news_acl']) ? $data['post_acl'] = S_VAR_TEXT_ESCAPE($_POST['news_acl']) : false; //TODO CHECK FILTER OK   
    isset($_POST['news_main_media']) ? $data['post_main_media'] = S_VALIDATE_MEDIA($_POST['news_main_media'], $config['NEWS_MEDIA_MAX_LENGHT'], $config['NEWS_MEDIA_MIN_LENGHT']) : false;
    
    return $data;
}

function news_form_submit_process() {
    global $LANGDATA, $config;
    
    $news_data = news_sendnews_getPost();

    //USERNAME/AUTHOR
    if (empty($news_data['username']) ) {
        $news_data['username'] = $LANGDATA['L_ANONYMOUS']; //TODO CHECK IF ITS RIGHT THAT PROCEDURE
        //$news_data['username'] = S_VAR_CHAR_AZ_NUM($_SESSION['username']);
    }           
    if ($news_data['username'] == false) {
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
    if($news_data['post_title'] == false) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    if( (strlen($news_data['post_title']) > $config['NEWS_TITLE_MAX_LENGHT']) || 
            (strlen($news_data['post_title']) < $config['NEWS_TITLE_MIN_LENGHT'])
            ){
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_NEWS_TITLE_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //LEAD    
    if($news_data['post_lead'] == false) {
        $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if( (strlen($news_data['post_lead']) > $config['NEWS_LEAD_MAX_LENGHT']) || 
            (strlen($news_data['post_lead']) < $config['NEWS_LEAD_MIN_LENGHT'])
            ){
        $response[] = array("status" => "4", "msg" => $LANGDATA['L_NEWS_LEAD_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //TEXT
    if($news_data['post_text'] == false) {
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }    
    if( (strlen($news_data['post_text']) > $config['NEWS_TEXT_MAX_LENGHT']) || 
            (strlen($news_data['post_text']) < $config['NEWS_TEXT_MIN_LENGHT'])
            ){
        $response[] = array("status" => "5", "msg" => $LANGDATA['L_NEWS_TEXT_MINMAX_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //CATEGORY
    if($news_data['post_category'] == false) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_NEWS_INTERNAL_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }
    //FEATURED
    //NOCHECK ATM
    //
    //ACL
    //NO CHECK ATM
    //
    //
    //MEDIA
    //echo "* {$news_data['post_main_media']} * {$_POST['news_media_link']} *";
    if($news_data['post_main_media'] == false) {
        $response[] = array("status" => "6", "msg" => $LANGDATA['L_NEWS_MEDIALINK_ERROR']);    
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;        
    }

    //
    //ALL OK
    if( news_db_submit($news_data)) {
     $response[] = array("status" => "ok", "msg" => $LANGDATA['L_NEWS_SUBMITED_SUCESSFUL'], "url" => $config['WEB_URL']);    
     echo json_encode($response, JSON_UNESCAPED_SLASHES);    
    }

     return false;
}

function Newspage_SendNewsScript() {
    $script = "";
    
    if (!check_jsScript("jquery.min.js")) 
    {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }           
    $script .= "<script type=\"text/javascript\" src=\"plugins/Newspage/js/sendnews.js\"></script>\n";
    
    return $script;
}


function news_db_submit($news_data) {
    global $config;
    
    $nid = db_get_next_num("nid", $config['DB_PREFIX']."news");    
    $lang_id = ML_iso_to_id($news_data['post_lang']);
    if ( ($uid = SMBasic_getUserID()) == false ) {
        $uid = 0;
    }
    
    !empty($news_data['post_acl']) ? $acl = $news_data['post_acl'] : $acl=""; 
    
    if (empty($news_data['post_featured'])) {
        $news_data['post_featured'] = 0;
    } else {        
        news_clean_featured($news_data['post_lang']);
    }
    
    $q = "INSERT INTO {$config['DB_PREFIX']}news ("
        . "nid, lang_id, title, lead, text, media, featured, author, author_id, category, lang, acl, moderation"    
        . ") VALUES ("
        . "'$nid', '$lang_id', '{$news_data['post_title']}', '{$news_data['post_lead']}', '{$news_data['post_text']}', "         
        . "'0', '{$news_data['post_featured']}', '{$news_data['username']}', '$uid', '{$news_data['post_category']}', '{$news_data['post_lang']}', '$acl', '{$config['NEWS_MODERATION']}'"       
        . ");";       
    $query = db_query($q);    
    $source_id = $nid;
    $plugin = "Newspage";
     //TODO DETERMINE IF OTS IMAGE OR VIDEO ATM VALIDATOR ONLY ACCEPT IMAGES, IF ITS NOT A IMAGE WE MUST  CHECK IF ITS A VIDEO OR SOMETHING LIKE THAT
    $type = "image";
    $q = "INSERT INTO {$config['DB_PREFIX']}links ("
        . "source_id, plugin, type, link, itsmain"
        . ") VALUES ("
        . "'$source_id', '$plugin', '$type', '{$news_data['post_main_media']}', '1'"
        . ");";    
    $query = db_query($q);
    
    return true;
}

function news_clean_featured($lang) {
    global $config;
    
    $q = "UPDATE {$config['DB_PREFIX']}news SET featured = '0'";
    if (defined('MULTILANG') && 'MULTILANG') {
        $q .= "WHERE lang = '$lang'";
    }            
    db_query($q);
}
<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function get_news($category, $limit = null) {
    global $config;
    
    $content = "";         
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured <> '1' ";
        
    if (defined('MULTILANG') && 'MULTILANG') {
        $LANGS = do_action("get_site_langs");
        
        foreach ($LANGS as $lang) {
            if ($lang->iso_code == $config['WEB_LANG']) {
                $lang_id = $lang->lang_id;                
                $q .= " AND lang_id = $lang_id";                
            } 
        }
    } 
    
    if ((!empty($category)) && ($category != 0 )) {
        $q .= " AND category = '$category'";
    }
    if ($limit > 0) {
        $q .= " LIMIT $limit";
    }
    $query = db_query($q);
   
    if (db_num_rows($query) <= 0) {
        return false;
    }
       
    if(!empty($category)) {
        if (defined('MULTILANG') && 'MULTILANG') {
            $catname = get_category_name($category, $lang_id);
        } else {
            $catname = get_category_name($category);    
        }
        $content .= "<h2>$catname</h2>";        
    }     

    while($row = db_fetch($query)) {
        if ( ($content_data = fetch_news_data($row)) != false) {
            $content .= getTPL_file("Newspage", "News", $content_data);        
        }
    }
    db_free_result($query);    
    
    return $content;
}

function get_news_featured($category = null, $limit = 1) {
    global $config;
    $content = "";
        
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured = '1'";

    if (defined('MULTILANG') && 'MULTILANG') {
        $LANGS = do_action("get_site_langs");
        
        foreach ($LANGS as $lang) {
            if ($lang->iso_code == $config['WEB_LANG']) {
                $lang_id = $lang->lang_id;
                $q .= " AND lang_id = $lang_id";
            } 
        }
    }
    
    if ((!empty($category)) && ($category != 0 )) {
        $q .= " AND category = $category";
    }

    $q .= " LIMIT $limit";
    $query = db_query($q);
   
    if (db_num_rows($query) <= 0) {
        return false;
    }
    
    if(!empty($category)) {
        if (defined('MULTILANG') && 'MULTILANG') {
            $catname = get_category_name($category, $lang_id);       
        } else {
            $catname = get_category_name($category);
        }
    } 
  
    while($row = db_fetch($query)) {
        if ( ($content_data = fetch_news_data($row)) != false ) {
            isset($catname) ? $content_data['CATEGORY'] = $catname: false;         
            $content .= getTPL_file("Newspage", "NewsFeatured", $content_data);
        }
    }
    
    db_free_result($query);
    
    return $content;
}

function fetch_news_data($row) {
    global $config, $acl_auth;    

    if( 'ACL' && !empty($acl_auth) && !empty($row['acl']) && !$acl_auth->acl_ask($row['acl'])) {
        return false;
    }     
    $data['NID'] = $row['nid'];
    $data['TITLE'] = $row['title'];
    $data['LEAD'] = $row['lead'];                
    $data['date'] = format_date($row['date']);    
    $data['ALT_TITLE'] = htmlspecialchars($row['title']);            

    if ($config['FRIENDLY_URL']) {    
        //FIX: one line str_replace?        
        $friendly_url = str_replace(' ', "-", $row['title']); 
        $friendly_url = str_replace('"', "", $friendly_url);        
        $data['URL'] = "/".$config['WEB_LANG']."/news/{$row['nid']}/$friendly_url";  
    } else {            
        $data['URL'] = $config['WEB_LANG']. "/newspage.php?nid={$row['nid']}&title=" . str_replace(' ', "_", $row['title']);
    }
    $query = db_query("SELECT * FROM $config[DB_PREFIX]media WHERE nid = $row[nid] AND itsmain = '1'");
    $media_row = db_fetch($query);
    $data['MEDIA'] = $media_row['medialink'];
    db_free_result($query);

    return $data;
}

function get_category_name($cid, $lang_id = null) {
    global $config; 
    
    $q = "SELECT name FROM {$config['DB_PREFIX']}categories WHERE cid = '$cid'";    
    if (defined('MULTILANG') && 'MULTILANG' && $lang_id != null) {
        $q .= " AND lang_id = $lang_id";
    }
    $q .= " LIMIT 1";
    $query = db_query($q);
    $category = db_fetch($query);
    db_free_result($query);  

    return $category['name'];
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
    
    $query = db_query("SELECT * FROM $config[DB_PREFIX]media WHERE nid = $id");    
    if (db_num_rows($query) > 0) {
        while ($row = db_fetch($query)) {
            $media[] = array (
                "mediaid" => $row['mediaid'], 
                "mediatype" => $row['mediatype'], 
                "medialink" => $row['medialink'], 
                "itsmain" => $row['itsmain']);        
        }                
    } else {
        $media = false;
    }   
    db_free_result($query);

    return $media;   
}

function news_layout_select() {
    global $config;
    
    if(empty($_POST['news_switch']) || $_POST['news_switch'] > $config['NEWS_BODY_STYLES']) {
        $news_switch = 1;
    } else{
        $news_switch = S_VAR_INTEGER($_POST['news_switch'],1);        
    }
    return $news_switch;    
}

function news_layout_switcher() { 
    global $tpldata;
    
    $data = "<li class='nav_left'><form action='' method='post'>";
    $data .= "<input type='submit'  value='' class='button_switch' />";
    $data .= "<input type='hidden' value=" . $tpldata['news_nSwitch'] ." name='news_switch'/>";
    $data .= "</form></li>";
    return $data;
}

function news_menu_submit_news() {
    global $LANGDATA;
    
    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='?sendnews=1'>". $LANGDATA['L_SEND_NEWS'] ."</a>";
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

function news_sendnews_getPost($stage = 1) {
    global $acl_auth, $config;

    if (defined('ACL') && 'ACL') { //if admin can change author if not ignore news_author
        if( ( $acl_auth->acl_ask('news_admin') ) == true || ( $acl_auth->acl_ask('admin_all') ) == true ) {
            if($stage == 1) {
                isset($_POST['news_author']) ? $data['username'] = S_VAR_CHAR_AZ_NUM($_POST['news_author']) : false;
            } else {
                isset($_POST['news_author1']) ? $data['username'] = S_VAR_CHAR_AZ_NUM($_POST['news_author1']) : false;
            }                
        }
    }
    if($stage == 1) {
        isset($_POST['news_title']) ? $data['post_title'] = S_VAR_TEXT($_POST['news_title']) : false;
        isset($_POST['news_lead']) ? $data['post_lead'] = S_VAR_TEXT($_POST['news_lead']) : false;
        isset($_POST['news_text']) ? $data['post_text'] = S_VAR_TEXT($_POST['news_text']) : false;
        isset($_POST['news_category']) ? $data['post_category'] = S_VAR_INTEGER($_POST['news_category'], 8) : false;
        isset($_POST['news_lang']) ? $data['post_lang'] = S_VAR_TEXT($_POST['news_lang']) : $data['post_lang'] = $config['WEB_LANG'];
    } else {
        isset($_POST['news_title1']) ? $data['post_title'] = S_VAR_TEXT($_POST['news_title1']) : false;
        isset($_POST['news_lead1']) ? $data['post_lead'] = S_VAR_TEXT($_POST['news_lead1']) : false;
        isset($_POST['news_text1']) ? $data['post_text'] = S_VAR_TEXT($_POST['news_text1']) : false;
        isset($_POST['news_category1']) ? $data['post_category'] = S_VAR_INTEGER($_POST['news_category1'], 8) : false;
        isset($_POST['news_lang1']) ? $data['post_lang'] = S_VAR_TEXT($_POST['news_lang1']) : $data['post_lang'] = $config['WEB_LANG'];
    }   
    return $data;
}

function news_form_submit_process() {
    global $LANGDATA, $config;
    
    $news_data = news_sendnews_getPost(2);

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
    $return = news_db_submit($news_data);
    
     $response[] = array("status" => "10", "msg" => htmlspecialchars($return));    
     echo json_encode($response, JSON_UNESCAPED_SLASHES);
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
    
    $acl =""; //TODO ACL
    
    $q = "INSERT INTO {$config['DB_PREFIX']}news ("
        . "nid, lang_id, title, lead, text, media, featured, author, author_id, category, lang, acl, moderation"    
        . ") VALUES ("
        . "'$nid', '$lang_id', '{$news_data['post_title']}', '{$news_data['post_lead']}', '{$news_data['post_text']}', "         
        . "'null', '{$news_data['username']}', '$uid', '{$news_data['post_category']}', '{$news_data['post_lang']}', '$acl', '{$config['NEWS_MODERATION']}'"       
        . ");";
        //TODO finish
    return $q;
}

function news_get_categories() {
    global $config;
    
    $lang_id = ML_iso_to_id($config['WEB_LANG']);
    
    $q = "SELECT * FROM {$config['DB_PREFIX']}categories WHERE plugin = 'news' AND lang_id = '$lang_id'";
    $query = db_query($q);
    return $query;
}

function news_get_categories_select() {
    $query = news_get_categories();
    
    $select = "<select name='news_category' id='news_category'>";
    while($row = db_fetch($query)) {
        $select .= "<option value='{$row['cid']}'>{$row['name']}</option>";        
    } 
    $select .= "</select>";
    return $select;
}
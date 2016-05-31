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
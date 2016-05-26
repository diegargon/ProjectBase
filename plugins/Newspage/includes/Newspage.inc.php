<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function get_news($category, $limit) {
    global $config;
    
    $content = "";         
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured <> '1' ";
    
    if (isset($config['multilang']) && $config['multilang'] == 1) {
        $LANGS = do_action("get_site_langs");
        
        foreach ($LANGS as $lang) {
            if ($lang->iso_code == $config['WEB_LANG']) {
                $lang_id = $lang->lang_id;                
//                $q .= " WHERE lang_id = $lang_id";
                $q .= " AND lang_id = $lang_id";                
            } 
        }
    } 
    
    if ((!empty($category)) && ($category != 0 )) {
//        if( !empty($lang_id)) {               
            $q .= " AND category = '$category'";
//        } else {
//            $q .= " WHERE category = '$category'";
//        }
    }
    if ($limit > 0) {
        $q .= " LIMIT $limit";
    }
    $query = db_query($q);
   
    if (db_num_rows($query) <= 0) {
        db_free_result($query);
        return false;
    }
       
    if(!empty($category)) {
        $catname = get_category_name($category, $lang_id);
        $content .= "<h2>$catname</h2>";
        
    }     

    while($row = db_fetch($query)) {
        $content_data = fetch_news_data($row);
        $content .= tpl_get_file("tpl", "Newspage", "News", $content_data);                
    }
    db_free_result($query);    
    
    return $content;
}

function get_news_featured($category = null, $limit = 1) {
    global $config;
    $content = "";
        
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured = '1'";

    if (isset($config['multilang']) && $config['multilang'] == 1) {
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
        db_free_result($query);
        return false;
    }
    
    if(!empty($category)) {
        $catname = get_category_name($category, $lang_id);       
    } 
  
    while($row = db_fetch($query)) {
        $content_data = fetch_news_data($row);
        isset($catname) ? $content_data['CATEGORY'] = $catname: false; 
        $content .= tpl_get_file("tpl", "Newspage", "NewsFeatured", $content_data);
    }
    
    db_free_result($query);
    
    return $content;
}

function fetch_news_data($row) {
    global $config;    

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

function get_category_name($cid, $lang_id) {
    global $config; 
    
    $q = "SELECT name FROM {$config['DB_PREFIX']}categories WHERE cid = '$cid'";

    if (isset($config['multilang']) && $config['multilang'] == 1) {
        $q .= " AND lang_id = $lang_id";
    }
    $q .= " LIMIT 1";
    $query = db_query($q);
    $category = db_fetch($query);
    db_free_result($query);  

    return $category['name'];
}

function get_news_byId($id, $lang){
    global $config;
    
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE nid = $id ";
    if ($config['multilang'] == 1 && !empty($lang)) { 
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
    db_free_result($query);

    return $row;
}

function get_news_media_byID($id) {
    global $config;
    
    $query = db_query("SELECT * FROM $config[DB_PREFIX]media WHERE nid = $id");
    $media = [];
    while ($row = db_fetch($query)) {
        $media[] = array (
            "mediaid" => $row['mediaid'], 
            "mediatype" => $row['mediatype'], 
            "medialink" => $row['medialink'], 
            "itsmain" => $row['itsmain']);        
    }
    db_free_result($query);

    return $media;
}

function news_get_full_news() {
    global $tpldata;
    global $config;
    global $LANGDATA;
    
    if( ($nid = S_VAR_INTEGER($_GET['nid'], 8, 1)) == false) {
        $tpldata['E_MSG'] = $LANGDATA['L_NEWS_NOT_EXIST'];
        $config['BACKLINK'] = '/';
        return do_action("error_message_box");    
    }

    if (($row = get_news_byId($nid, $config['WEB_LANG'])) == false) {
        $row = get_news_byId($nid, "");
        $tpldata['NEWS_MSG'] = $LANGDATA['L_NEWS_WARN_NOLANG'];
    }
    $tpldata['NID'] = $row['nid'];    
    $tpldata['NEWS_TITLE'] = $row['title'];    
    $tpldata['NEWS_LEAD'] = $row['lead'];    
    $tpldata['NEWS_URL'] = "news.php?nid=$row[nid]";
    $tpldata['NEWS_DATE'] = format_date($row['date']);
    $tpldata['NEWS_AUTHOR'] = $row['author'];
    $tpldata['NEWS_TEXT']  = $row['text'];

    $allmedia = get_news_media_byID($nid);
    
    foreach ($allmedia as $media) {
        if($media['itsmain'] == 1 ) {
            $tpldata['NEWS_MAIN_MEDIA'] = $media['medialink'];
        }
    }
      
     return tpl_get_file("tpl", "Newspage", "news_show_body");
}
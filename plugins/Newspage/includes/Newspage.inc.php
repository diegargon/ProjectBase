<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function get_news($category, $limit, $preview, $featured) {
    global $config;
    
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured = $featured";

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
        if ($limit > 0) {
            $q .= " LIMIT $limit";
        }

    $query = db_query($q);
   
    if (db_num_rows($query) <= 0) {
        db_free_result($query);
        return false;
    }

        $q = "SELECT name FROM {$config['DB_PREFIX']}categories WHERE cid = '$category'";

        if (isset($config['multilang']) && $config['multilang'] == 1) {
            $q .= " AND lang_id = $lang_id";
        }
        $q .= " LIMIT 1";
        $query2 = db_query($q);
        $category = db_fetch($query2);
        db_free_result($query2);
    
    if ($featured) {
        //NEWS FEATURE
        $TPLPATH = tpl_get_path("tpl", "Newspage", "NewsFeatured");
        $data['FEATURED_CAT'] = "Featured " . $category['name'];
    } else {
        //NEWS
        $TPLPATH = tpl_get_path("tpl", "Newspage", "News");
        $content = "<h2 class=\"category_name\">{$category['name']}</h2>";        
    }
    
    if ($TPLPATH != "0") {

        while ($row = db_fetch($query)) {
            $data['NID'] = $row['nid'];
            $data['TITLE'] = $row['title'];
            $data['LEAD'] = $row['lead'];
            //FIX: Better... str_replace
            $data['date'] = format_date($row['date']);
            $date['ALT_TITLE'] = str_replace(' ', "-", $row['title']);
            $date['ALT_TITLE'] = str_replace('"', "", $date['ALT_TITLE']);
            if ($config['FRIENDLY_URL']) {
                $friendly_url = str_replace(' ', "-", $row['title']);
                $friendly_url = str_replace('"', "", $friendly_url);
                $data['URL'] = "/".$config['WEB_LANG']."/news/{$row['nid']}/$friendly_url";  
            } else {
                  $data['URL'] = $config['WEB_LANG']. "/newspage.php?nid={$row['nid']}&title=" . str_replace(' ', "_", $row['title']);
            }
            
            if (!$preview) {
                $data['TEXT'] = $row['text'];
            }
            
            $query2 = db_query("SELECT * FROM $config[DB_PREFIX]media WHERE nid = $row[nid] AND itsmain = '1'");
            $row2 = db_fetch($query2);
            $data['MEDIA'] = $row2['medialink'];
            if (isset($content)) {
                $content .= codetovar($TPLPATH, $data);
            } else {
                $content = codetovar($TPLPATH, $data);
            }
            db_free_result($query2);
        }
        db_free_result($query);
        
        return $content;
    }
    db_free_result($query);
    
    return false;
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
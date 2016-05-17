<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */


function get_news($category, $limit, $preview, $featured) {
    global $config;
    
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured = $featured";

    if (isset($config['multilang']) && $config['multilang'] == 1) {
        $q .= " AND lang = '{$config['WEB_LANG']}'";
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
    
    if ($featured) {
        $TPLPATH = tpl_get_path("tpl", "Newspage", "NewsFeatured");
    } else {
        $TPLPATH = tpl_get_path("tpl", "Newspage", "News");
    }
    
    if ($TPLPATH != "0") {
       
        while ($row = db_fetch($query)) {
            $data['NID'] = $row['nid'];
            $data['TITLE'] = $row['title'];
            $data['LEAD'] = $row['lead'];
            
            if ($config['FRIENDLY_URL']) {
                $friendly_url = str_replace(' ', "_", $row['title']);
                $data['URL'] = "/".$config['WEB_LANG']."/news/{$row['nid']}/$friendly_url";
//                    $data['URL'] = "/en/news_/{$row['nid']}/$friendly_url";
//                }   
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

function get_news_byId($id){
    global $config;
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE nid = $id LIMIT 1";
    $query = db_query($q);
    $row = db_fetch($query);


    db_free_result($query);
    return $row;
}

function get_news_media_byID($id) {
    global $config;
    
    $query = db_query("SELECT * FROM $config[DB_PREFIX]media WHERE nid = $id");
    $media = [];
    while ($row = db_fetch($query)) {
        $media[] = array ("mediaid" => $row['mediaid'], "mediatype" => $row['mediatype'], "medialink" => $row['medialink'], "itsmain" => $row['itsmain']);        
    }
    db_free_result($query);
    return $media;
}
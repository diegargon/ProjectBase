<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */


function get_news($category, $limit, $preview, $featured) {
    global $config;
    
    $q = "SELECT * FROM $config[DB_PREFIX]news WHERE featured = $featured";
               
    if ((empty($category)) || ($category == 0 )) {
            
        if ($limit > 0) {
            $q .= " LIMIT $limit";
        }
        $query = db_query($q);    
        
    } else {
        $q .= " AND category = $category";

        if ($limit > 0) {
            $q .= " LIMIT $limit";
        }
        $query = db_query($q);
    }
    
    if (!$query) {
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
            $data['URL'] = "news.php?nid=$row[nid]";

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
        }
        db_free_result($query);
        db_free_result($query2);
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
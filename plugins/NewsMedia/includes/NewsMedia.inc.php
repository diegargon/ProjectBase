<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_format_media($media) {
    //TODO MEDIA TYPES   
    if ($media['type'] == 'image') {
        $result =  "<img src=" . $media['link'] ." alt=". $media['link'] . "/>"; //TODO FIX ALT        
    } else {
        return false;
    }
    return $result;
}

function get_news_main_link_byID($nid) {
    global $db;
    
    $query = $db->select_all("links", array("source_id" => "$nid", "itsmain" => 1), "LIMIT 1");
    
    return ($db->num_rows($query) <= 0) ? false : $db->fetch($query);     
}

function S_VALIDATE_MEDIA($url, $max_size = null, $min_size = null, $force_no_remote_check = null) {
    global $config;

    if ( (strpos($url, 'http://') !== 0) && (strpos($url, 'https://') !== 0)) { 
        $url = "http://" . $url;
    }

    $regex = '/\.('. $config['ACCEPTED_MEDIA_REGEX'] .')(?:[\?\#].*)?$/';
    
    if( ($url = S_VAR_URL($url, $max_size, $min_size)) == false ) {
        return -1;
    }    
    if ( !preg_match($regex, $url) ) {
      return -1;
    }  
    if ($config['REMOTE_CHECKS'] && empty($force_no_remote_check)) {
        if (!remote_check($url)) {
            return -1;
        }
    }
 
    return $url;
}
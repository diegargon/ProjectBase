<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function news_format_media($media_ary) {
    $result = "";
    //TODO MEDIA TYPES   
    foreach ($media_ary as $media) {
        if ($media['type'] == 'image') {
            $result .=  "<img src=" . $media['link'] ." alt=". $media['link'] . "/>"; //TODO FIX ALT        
        } else {
            return false;
        }
    }
    return $result;
}

function get_links($source_id, $type, $extra_ary = null, $extra_db = null) {
    global $db;
    $query_ary = array ( 
        "source_id" => "$source_id",
        "type" => "$type",        
    );
    if (!empty($extra_ary) && is_array($extra_ary)) {
        $query_ary = array_merge($query_ary, $extra_ary);
    }
    $query = $db->select_all("links", $query_ary, "$extra_db");
    
    if ($db->num_rows($query) <= 0) {
        return false;
    }
       
    while( $links_row = $db->fetch($query) ) {
        $links[] = $links_row;
    }
    return $links;
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
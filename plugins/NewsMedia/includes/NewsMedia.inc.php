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
            $result .=  "<img class='image_link' src=" . $media['link'] ." alt=". $media['link'] . "/>"; //TODO FIX ALT        
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

<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function getCatIDbyName($catname) {
    global $db;

    $catname = $db->escape_strip($catname);

    $query = $db->select_all("categories", array("name" => $catname), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    $cat_data = $db->fetch($query);
    return $cat_data['cid'];
}

function getCatbyName($catname, $catparent = null) {
    global $db;

    $catname = $db->escape_strip($catname);

    $where_ary = array(
        "name" => $catname,
    );
    if (!empty($catparent)) {
        $catparent = $db->escape_strip($catparent);
        $catparent_id = getCatIDbyName($catparent);
        !empty($catparent_id) ? $where_ary['father'] = $catparent_id : null;
    }

    $query = $db->select_all("categories", $where_ary, "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    return $db->fetch($query);
}

function getCatIDbyName_LIST($cat_list) {
    global $db;

    
    //check parent to avoid errors on duplicate name != section    
    $cat_list = explode(".", $cat_list);
    if (count($cat_list) > 1) {
        $catname = array_pop($cat_list);
        $catparent = array_pop($cat_list);
        $cat_data = getCatbyName($catname, $catparent);
    } else {
        $catname = array_pop($cat_list);
        $cat_data = getCatbyName($catname);
    }

    return $cat_data['cid'];
}

function getCatChildsID($cat, $lang_id) {
    global $db;
    $cat_ids = "";

    $query = $db->select_all("categories", array("lang_id" => $lang_id, "father" => $cat));
    while ($c_row = $db->fetch($query)) {
        $cat_ids .= "," . $c_row['cid'];
    }
    if (!empty($cat_ids)) {
        $cat_ids .= getCatChildID_Multiple($cat_ids, $lang_id);
    }
    return $cat_ids;
}

function getCatChildID_Multiple($cats, $lang_id) {
    global $db;
    $cat_ids = "";
    $cats = ltrim($cats, ','); //remove first ,

    $query = $db->select_all("categories", array("lang_id" => $lang_id, "father" => array("value" => "($cats)", "operator" => "IN")));
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    while ($c_row = $db->fetch($query)) {
        $cat_ids .= "," . $c_row['cid'];
    }
    $cat_ids .= getCatChildID_Multiple($cat_ids, $lang_id); //Recursion
    return $cat_ids;
}

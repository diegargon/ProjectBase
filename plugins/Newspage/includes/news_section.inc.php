<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function getCatIDbyName($catname) {
    global $db;

    $query = $db->select_all("categories", array("name" => $catname), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    $cat_data = $db->fetch($query);
    return $cat_data['cid'];
}

function getCatIDbyName_LIST($cat_list) {
    global $db;

    //FIX: That give problems/conflict with same category name in other sections
    //check and get father match one    
    $cat_list = explode(".", $cat_list);
    $catname = end($cat_list);

    $catname = $db->escape_strip($catname);

    $query = $db->select_all("categories", array("name" => $catname), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    $cat_data = $db->fetch($query);
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

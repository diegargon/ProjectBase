<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function getCatIDbyName($cat) {
    global $db;
    $catname = $db->escape_strip($cat);

    $query = $db->select_all("categories", array("name" => $catname), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    $cat_data = $db->fetch($query);
    return $cat_data['cid'];
}

function getCatChildsID($cat) {
    global $db;
    $cat_ids = "";

    $query = $db->select_all("categories", array("father" => $cat));
    while ($c_row = $db->fetch($query)) {
        !empty($cat_ids) ? $cat_ids = "," : null;
        $cat_ids .= $c_row['cid'];
    }
    return $cat_ids;
}

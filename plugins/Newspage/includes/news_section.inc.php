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
/* WORKS but checking use recursion with Multiple for less querys
  function getCatChildsID($cat, $lang_id) {
  global $db;
  $cat_ids = "";

  $query = $db->select_all("categories", array("lang_id" => $lang_id, "father" => $cat));
  while ($c_row = $db->fetch($query)) {
  $cat_ids .= "," . $c_row['cid'];
  $cat_ids .= getCatChildsID($c_row['cid'], $lang_id);
  }
  return $cat_ids;
  }
 */

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

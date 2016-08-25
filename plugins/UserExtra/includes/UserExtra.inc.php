<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function uXtra_upsert($set_ary, $where_ary) {
    global $db;
    $db->upsert("user_extra", $set_ary, $where_ary);
}

function uXtra_get($uid) {
    global $db;
    !empty($uid) ? $userEx_query = $db->select_all("user_extra", array("uid" => "$uid"), "LIMIT 1") : false;
    !empty($userEx_query) ? $userEx_data = $db->fetch($userEx_query) : false;
    return !empty($userEx_data) ? $userEx_data : false;
}

function uXtra_checkdup($field_ary) {
    global $db;

    $query = $db->select_all("user_extra", $field_ary);
    if ($db->num_rows($query) > 0) {
        return true;
    } else {
        return false;
    }
}

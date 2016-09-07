<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

class UserExtra {

    private $UXtra_db_cache = [];

    function getById($uid) {
        global $db;
        if (empty($uid)) {
            return false;
        }
        if (isset($this->UXtra_db_cache[$uid])) {
            return $this->UXtra_db_cache[$uid];
        }
        if (($userEx_query = $db->select_all("user_extra", array("uid" => "$uid"), "LIMIT 1"))) {
            $userEx_data = $db->fetch($userEx_query);
            $this->UXtra_db_cache[$userEx_data['uid']] = $userEx_data;
        } else {
            return false;
        }

        return $userEx_data;
    }

    function upsert($set_ary, $where_ary) {
        global $db;
        $db->upsert("user_extra", $set_ary, $where_ary);
    }

    function checkdup($field_ary) {
        global $db;

        $query = $db->select_all("user_extra", $field_ary, "LIMIT 1");

        return $db->num_rows($query) > 0 ? true : false;
    }

}

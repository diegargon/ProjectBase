<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

class SessionManager {
    private $user;
    
    function __construct() {
        session_start();
    }
    
    function getUserbyID($uid) { 
        global $db;
                       
        $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");

        return ($db->num_rows($query) <= 0) ? false : $db->fetch($query);
    }
    
    function getUserByUsername($username) {
        global $db;
        $query = $db->select_all("users", array("username" => $username), "LIMIT 1");

        return ($db->num_rows($query) <= 0) ? false : $db->fetch($query);
    }

    function getSessionUser() {  //TODO Recheck/rethink  THIS FUCTION logic SESSION THINK
        global $db;
                
        if (empty($this->user)) {
            ($uid = S_SESSION_INT("uid", 11, 1)) == false ? $this->user = false : false;
            $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");
            $db->num_rows($query) <= 0 ? $this->user = false : $this->user = $db->fetch($query);
        }
        return $this->user;    
    }

    function getAllUsersArray($order_field = "regdate", $order = "ASC",  $limit = 20) {
        global $db;
        $extra = "ORDER BY " . $order_field ." ". $order ." LIMIT ". $limit;
        $query = $db->select_all("users", null, $extra);
        while ($user_row = $db->fetch($query)) {
            $users_ary[] = $user_row;
        }
        return $users_ary;
    }
    
    function searchUser($string, $email = 0, $glob = 0) {
        global $db;
        
        $where_ary = [];
        if (!empty($email)) {
            if (empty($glob)) {
                $where_ary = array ("email" => array("value" => $string, "operator" => "LIKE")); 
            } else {
                $where_ary = array ("email" =>  array("value" => "%". $string ."%", "operator" => "LIKE")); 
            }
        } else {
            if (empty($glob)) {
                $where_ary = array ("username" =>  array("value" => $string, "operator" => "LIKE"));
            } else {
                $where_ary = array ("username" =>  array("value" => "%". $string ."%", "operator" => "LIKE"));
            }
        }
        
        $query = $db->select_all("users", $where_ary);
        if ($db->num_rows($query) > 0) {
            while ($user_row = $db->fetch($query)) {
                $users_ary[] = $user_row;
            }
            return $users_ary;
        }
        return false;
    }
}

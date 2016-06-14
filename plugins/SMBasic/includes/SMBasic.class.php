<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

class SessionManager {
    private $user;
    
    function getUserbyID($uid) { 
        global $db;
   
        if (empty($this->user)) {            
            $query = $db->select_all("users", array("uid" => "$uid"), "LIMIT 1");
    
            if ($db->num_rows($query) <= 0) {
                $this->user = false;        
            } else {
                $this->user = $db->fetch($query);
            }
        }    
        return $this->user;    
    }
    
    function getUserID () { 
        //TODO use $user instead of $_SESSION?
        if (S_SESSION_INT("isLogged", 1) == 1) {
            return S_SESSION_INT("uid", 11, 1);
        } else {
            return false;
        }
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

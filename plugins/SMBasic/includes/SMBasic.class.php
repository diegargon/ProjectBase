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
}

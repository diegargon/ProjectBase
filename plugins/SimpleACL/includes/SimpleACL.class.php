<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * 
 * Fast Implementation, need some basic ACL now,  work on this later
 */
if (!defined('IN_WEB')) { exit; }

class ACL {
    private $roles, $user_roles;
    
    function __construct() {     
   }
    
    function acl_ask($role, $resource = "ALL") {
        if (!isset($_SESSION['isLogged']) || $_SESSION['isLogged'] != 1) {
            $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_NOACCESS'];        
            do_action("error_message_page");            
            return false;
        }
        if (empty($this->roles) || empty($this->user_roles)) {
            $this->getRoles();
            $this->getUserRoles();   
        } else if ($this->roles == false || $this->$user_roles == false) {
            $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_ROLES_DB'];        
            do_action("error_message_page");            
            return false;            
        }

        list($role_group, $role_type) = preg_split("/_/", $role);                        

        if (!$this->checkUserPerms($role_group, $role_type, $resource) ) {
            $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_NOACCESS'];        
            do_action("error_message_page");            
            return false;            
        } else {
            return true;
        }
    }
    
    private function checkUserPerms($role_group, $role_type, $resource) {
        if(!$asked_role = $this->getRoleDataByName($role_group, $role_type)) {
            return false;
        }        
                     
        foreach ($this->user_roles as $user_role) {
            if(!$user_role_data = $this->getRoleByID($user_role['role_id'])) {
                return false;
            }
            if (
                    ($user_role_data['role_id'] ==  $asked_role['role_id']) &&
                    ($user_role_data['resource'] == $resource) //Used later for specific resources                    
                    ) {                
                return true; //its the exact role
            }                          
            if ( //Look if role its upper level
                    ( $asked_role['role_group'] == $user_role_data['role_group'] )&&
                    ( $asked_role['level'] > $user_role_data['level'] ) &&
                    ( $user_role_data['resource'] == $resource) //Used later for specific resources
                    ) {
                return true;
            }
        }
        return false;        
    }
    
    private function getRoleDataByName($role_group, $role_type) {        
        foreach ($this->roles as $rol) {
            if (
                    ( ($rol['role_group'] == $role_group) && ($rol['role_type'] == $role_type) )
                    ){
                        return $rol;
                    }
        }
        return false;
    }    
    
    private function getRoleByID($role_id) {
        foreach ($this->roles as $role) {
            if (
                    ( ($role['role_id'] == $role_id))                    
                    ){
                        return $role;
                    }
        }
        return false;
    }
    
    private function getRoles() {
        global $config;
        
        $q = "SELECT * FROM {$config['DB_PREFIX']}acl_roles";
        $query = db_query ($q);
        if (db_num_rows($query) > 0) {
            while ($row = db_fetch($query)) {
                $this->roles[] = $row;
            }
        } else {
            $this->roles = false;
        }
        db_free_result($query);        
    }

    private function getUserRoles() {
        global $config;

        if(!$uid = S_VAR_INTEGER($_SESSION['uid'], 11, 0)) { //TODO change to a global user variable?                                
            return false;
        }
        $q = "SELECT * FROM {$config['DB_PREFIX']}acl_users WHERE uid = '$uid'";
        $query = db_query ($q);
        if (db_num_rows($query) > 0) {
            while ($row = db_fetch($query)) {
                $this->user_roles[] = $row;
            }
        } else {
            $this->user_roles = false;
        }
        db_free_result($query);
    }    
    
}
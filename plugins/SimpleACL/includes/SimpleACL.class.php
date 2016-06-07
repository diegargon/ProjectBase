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
            return false;
        }
        if (empty($this->roles) || empty($this->user_roles)) {
            $this->getRoles();
            $this->getUserRoles();   
        }
        if ($this->roles == false) { 
            $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_ROLES_DB'];        
            do_action("error_message_page");  //TODO RECHECK THAT
            return false;            
        }

        if($this->user_roles == false) { //No user_roles in DB for that user
            return false;
        }
        list($role_group, $role_type) = preg_split("/_/", $role);                        

        if (!$this->checkUserPerms($role_group, $role_type, $resource) ) {    
            return false;            
        } else {
            return true;
        }
    }

    function get_roles_select($acl_group = null, $selected = null) {
        global $LANGDATA, $db;
       
        $select = "<select name='{$acl_group}_acl' id='{$acl_group}_acl'>";
        if ($selected == null) {
            $select .= "<option selected value=''>{$LANGDATA['L_ACL_NONE']}</option>";
        } else {
            $select .= "<option value=''>{$LANGDATA['L_ACL_NONE']}</option>";
        }
        
        $query = $this->get_roles_query($acl_group);               
        while($row = $db->fetch($query)) {
            $full_role = $row['role_group'] ."_". $row['role_type'];
            if ($full_role != $selected) {
                $select .= "<option value='$full_role'>{$LANGDATA[$row['role_name']]}</option>";
            } else {
                $select .= "<option selected value='$full_role'>{$LANGDATA[$row['role_name']]}</option>";
            }
        } 
        $select .= "</select>";
        return $select;        
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
        global $db;
        
        $query = $db->select_all("acl_roles"); 
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->roles[] = $row;
            }
        } else {
            $this->roles = false;
        }
        $db->free($query);        
    }

    private function getUserRoles() {
        global $db;

        if(!$uid = S_VAR_INTEGER($_SESSION['uid'], 11, 0)) { //TODO change to a global user variable?                                
            return false;
        }
        $query = $db->select_all("acl_users", array("uid" => "$uid"));
        if ($db->num_rows($query) > 0) {
            while ($row = $db->fetch($query)) {
                $this->user_roles[] = $row;
            }
        } else {
            $this->user_roles = false;
        }
        $db->free($query);
    }    

    private function get_roles_query($acl_group = null) {
        global $db;

        if (!empty($acl_group)) {
            $query = $db->select_all("acl_roles", array("role_group" => "$acl_group"));
        } else {
            $query = $db->select_all("acl_roles");
        }
        
        return $query;
    }    
}
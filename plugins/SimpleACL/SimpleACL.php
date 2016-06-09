<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_init(){
    global $acl_auth; 
    print_debug("SimpleACL Inititated", "PLUGIN_LOAD");
          
    includePluginFiles("SimpleACL"); 
    
    if(empty($acl_auth)) {
        $acl_auth = new ACL;
    }    
}

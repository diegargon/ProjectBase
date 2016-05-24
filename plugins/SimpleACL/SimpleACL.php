<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_init(){
    if (DEBUG_PLUGINS_LOAD) { print_debug("SimpleACL Inititated<br/>"); }

    require("includes/SimpleACL.class.php");
    includeConfig("Admin");
    includeLang("Admin"); 
    
    $auth = new ACL;
    //$auth->acl_ask("test");
}


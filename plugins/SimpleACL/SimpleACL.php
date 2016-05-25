<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SimpleACL_init(){
    global $auth; 
    if (DEBUG_PLUGINS_LOAD) { print_debug("SimpleACL Inititated<br/>"); }

    require("includes/SimpleACL.class.php");
    includeConfig("Admin");
    includeLang("Admin"); 
    
    if(!isset($auth)) {
        $auth = new ACL;
    } else {
        echo "Error on SimpleACL/php 18: Auth its set";
    }
    
}


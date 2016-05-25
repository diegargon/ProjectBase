<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Admin_init(){
    if (DEBUG_PLUGINS_LOAD) { print_debug("Admin Inititated<br/>"); }
    
    register_uniq_action("admin_page", "Admin_main_page");
}

function Admin_main_page() {
    global $auth;
    require("includes/Admin.inc.php");
    includeConfig("Admin");
    includeLang("Admin"); 
    
    do_action("common_web_structure");
    
    if (!$auth->acl_ask("admin_read")) {
        echo "Haven't permiss";
        exit(0); //TODO EROR MSG HAVENT PERMISS
    }    
}

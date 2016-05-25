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
    
    
    
    if (!$auth->acl_ask("admin_read")) {
        global $tpldata, $LANGDATA;
        
        $tpldata['ERROR_TITLE'] = $LANGDATA['L_ERROR'];
        $tpldata['ERROR_MSG'] = $LANGDATA['L_ERROR_NOACCESS'];
        $tpldata['ERROR_BACKLINK'] = "./";
        $tpldata['ERROR_BACKLINK_TITLE'] = "Back home";
        do_action("error_message");
        return false;
    }    
    
    do_action("common_web_structure");
}

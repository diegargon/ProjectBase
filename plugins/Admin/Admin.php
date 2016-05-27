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
    global $tpldata;
    $tpldata['ADD_ADMIN_MENU'] = "";
    $tpldata['ADD_TOP_ADMIN'] = "";
    $tpldata['ADD_BOTTOM_ADMIN'] = "";
    
    includePluginFiles("Admin");
    
    if (!$auth->acl_ask("admin_read")) {
        return false;
    }
    
    tpl_addto_var("LINK", "tpl_get_file", "css", "Admin");
        
    $tpldata['ADD_ADMIN_MENU'] .= do_action("add_admin_menu");
    $tpldata['ADD_TOP_ADMIN'] .= do_action("add_top_admin");
    $tpldata['ADD_BOTTOM_ADMIN'] .= do_action("add_bottom_admin");    
    tpl_addto_var("POST_ACTION_ADD_TO_BODY", "tpl_get_file", "tpl", "Admin", "admin_main_body");
    do_action("common_web_structure");
}

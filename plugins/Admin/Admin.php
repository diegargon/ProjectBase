<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Admin_init(){
    print_debug("Admin Inititated", "PLUGIN_LOAD");
    
    includePluginFiles("Admin");    
    register_uniq_action("admin_page", "Admin_main_page");
}

function Admin_main_page() {
    global $acl_auth, $tpldata;
    $tpldata['ADD_ADMIN_MENU'] = $tpldata['ADD_TOP_ADMIN'] = $tpldata['ADD_BOTTOM_ADMIN'] = "";
              
    if (!$acl_auth->acl_ask("admin_read")) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_NOACCESS'];        
        do_action("error_message_page");        
        return false;
    }
    
    includePluginFiles("Admin");
    admin_load_plugin_files();    
    
    if (!$admtab = S_GET_INT("admtab")) {
        $admtab = 1;        
    }
    $tpldata['ADMIN_TAB_ACTIVE'] = $admtab;
    getCSS_filePath("Admin");   
    $params['admtab'] = $admtab;
    $tpldata['ADD_ADMIN_MENU'] .= do_action("add_admin_menu", $params);
    $tpldata['ADD_TOP_ADMIN'] .= do_action("add_top_admin");
    $tpldata['ADD_BOTTOM_ADMIN'] .= do_action("add_bottom_admin");

    if($admtab == 1) {
        addto_tplvar("ADD_ADMIN_CONTENT", Admin_generalContent($params));
    } else {
        addto_tplvar("ADD_ADMIN_CONTENT", do_action("admin_get_content", $params));
    }
    addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("Admin", "admin_main_body"));
    do_action("common_web_structure");
}

function Admin_generalContent($params) {
    global $tpldata, $LANGDATA;    
    $tpldata['ADM_ASIDE_OPTION'] = "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .=  "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>Opcion 2</a></li>\n";
    $tpldata['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_GENERAL_OPT");
    
    if ( (!$opt = S_GET_INT("opt")) || $opt == 1 ) {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'];
        $tpldata['ADM_CONTENT'] = Admin_GetPluginState("Admin");
        $tpldata['ADM_CONTENT'] .= "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles("Admin")) . "</pre></p>";
    } else {
        $tpldata['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": Other opt";
        $tpldata['ADM_CONTENT'] = "Content from other opt";
    }
    return getTPL_file("Admin", "admin_std_content");

}

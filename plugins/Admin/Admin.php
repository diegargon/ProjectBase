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
    global $acl_auth, $tpl, $sm;    
              
    if (defined('ACL') && !$acl_auth->acl_ask("admin_read")) {
        $msgbox['MSG'] = "L_ERROR_NOACCESS";
        do_action("message_page", $msgbox );        
        return false;
    }
    $user = $sm->getSessionUser();
    if (!defined('ACL') && $user['isAdmin'] != 1) {
        $msgbox['MSG'] = "L_ERROR_NOACCESS";
        do_action("message_page", $msgbox );        
        return false;        
    }
    
    includePluginFiles("Admin");
    admin_load_plugin_files();    
    
    !$admtab = S_GET_INT("admtab") ? $admtab = 1 : false;        

    $tpl->addto_tplvar("ADMIN_TAB_ACTIVE", $admtab);
    $tpl->getCSS_filePath("Admin");   
    $params['admtab'] = $admtab;
    $tpl->addto_tplvar("ADD_ADMIN_MENU", do_action("add_admin_menu", $params));
    $tpl->addto_tplvar("ADD_TOP_MENU", do_action("add_top_menu"));
    $tpl->addto_tplvar("ADD_BOTTOM_MENU", do_action("add_bottom_menu"));

    if($admtab == 1) {
        $tpl->addto_tplvar("ADD_ADMIN_CONTENT", Admin_generalContent($params));
    } else {
        $tpl->addto_tplvar("ADD_ADMIN_CONTENT", do_action("admin_get_content", $params));
    }
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Admin", "admin_main_body"));
    do_action("common_web_structure");
}

function Admin_generalContent($params) {
    global $LANGDATA, $tpl;  

    $tpl_data['ADM_ASIDE_OPTION'] = "<li><a href='?admtab=" . $params['admtab'] ."&opt=1'>". $LANGDATA['L_PL_STATE'] ."</a></li>\n";
    $tpl_data['ADM_ASIDE_OPTION'] .=  "<li><a href='?admtab=" . $params['admtab'] ."&opt=2'>Opcion 2</a></li>\n";
    $tpl_data['ADM_ASIDE_OPTION'] .= do_action("ADD_ADM_GENERAL_OPT");
  
    if ( (!$opt = S_GET_INT("opt")) || $opt == 1 ) {
        $tpl_data['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": ".  $LANGDATA['L_PL_STATE'];
        $tpl_data['ADM_CONTENT'] = Admin_GetPluginState("Admin");
        $tpl_data['ADM_CONTENT'] .= "<hr/><p><pre>" . htmlentities(Admin_GetPluginConfigFiles("Admin")) . "</pre></p>";
    } else {
        $tpl_data['ADM_CONTENT_DESC'] = $LANGDATA['L_GENERAL'] .": Other opt";
        $tpl_data['ADM_CONTENT'] = "Content from other opt";
    }
    $tpl->addtpl_array($tpl_data);
    
    return $tpl->getTPL_file("Admin", "admin_std_content");
}

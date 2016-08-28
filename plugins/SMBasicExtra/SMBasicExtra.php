<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasicExtra_init() {
    print_debug("SMBasicExtra initiated", "PLUGIN_LOAD");
    register_action("preload_SMBasic_profile", "SMBasic_Extra_Show");
}

function SMBasic_Extra_Show() {
    global $tpl, $sm, $tpl;
    includePluginFiles("SMBasicExtra");
    $tpl->getCSS_filePath("SMBasicExtra");
    
    plugin_start("UserExtra");
    if (!isset($_GET['viewprofile'])) {
        $tpl->AddScriptFile("standard", "jquery.min", "BOTTOM");
        $tpl->AddScriptFile("SMBasicExtra", "profile_extra", "BOTTOM");
        $user = $sm->getSessionUser();
        $userEx_data = uXtra_get($user['uid']);
        $tpl->addto_tplvar("SMB_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "profile_fields", $userEx_data));
        register_action("SMBasic_ProfileChange", "SMB_Ex_ProfileChange");
    } else if ($vid = S_GET_INT("viewprofile", 11, 1)) { 
        $v_user = $sm->getUserByID($vid);
        $userEx_data = uXtra_get($vid);
        if(!empty($userEx_data['email_public'])) {
            $profile_data['profile_title'] = "L_EMAIL";
            $profile_data['profile_content'] = $v_user['email'];
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
        if(!empty($userEx_data['realname_public']) && !empty($userEx_data['realname'])) {
            $profile_data['profile_title'] = "L_SM_REALNAME";
            $profile_data['profile_content'] = $userEx_data['realname'];
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
        
    }
}

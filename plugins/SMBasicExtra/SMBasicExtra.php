<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasicExtra_init() {
    print_debug("SMBasicExtra initiated", "PLUGIN_LOAD");
    register_action("preload_SMBasic_profile", "SMBasic_Extra_Show");
}

function SMBasic_Extra_Show() {
    global $tpl, $sm, $tpl;
    includePluginFiles("SMBasicExtra");
    $tpl->getCSS_filePath("SMBasicExtra");

    if (!isset($_GET['viewprofile'])) {
        $tpl->AddScriptFile("standard", "jquery.min", "BOTTOM");
        $tpl->AddScriptFile("SMBasicExtra", "profile_extra", "BOTTOM");
        $user = $sm->getSessionUser();
        $userEx_data = uXtra_get($user['uid']);
        do_action("profile_xtra_show", $userEx_data);
        $tpl->addto_tplvar("SMB_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "profile_fields", $userEx_data));
        register_action("SMBasic_ProfileChange", "SMB_Ex_ProfileChange");
    } else if ($vid = S_GET_INT("viewprofile", 11, 1)) {
        $v_user = $sm->getUserByID($vid);
        $userEx_data = uXtra_get($vid);
        if (!empty($userEx_data['email_public'])) {
            $profile_data['profile_title'] = "L_EMAIL";
            $profile_data['profile_content'] = $v_user['email'];
            $profile_data['profile_class'] = "v_email";
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
        if (!empty($userEx_data['realname_public']) && !empty($userEx_data['realname'])) {
            $profile_data['profile_title'] = "L_SM_REALNAME";
            $profile_data['profile_content'] = $userEx_data['realname'];
            $profile_data['profile_class'] = "v_realname";
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
        if (!empty($userEx_data['age_public']) && !empty($userEx_data['age'])) {
            $profile_data['profile_title'] = "L_SM_AGE";
            $profile_data['profile_content'] = $userEx_data['age'];
            $profile_data['profile_class'] = "v_age";
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
        if (!empty($userEx_data['aboutme_public']) && !empty($userEx_data['aboutme_public'])) {
            $profile_data['profile_title'] = "L_SM_ABOUTME";
            $profile_data['profile_content'] = $userEx_data['aboutme'];
            $profile_data['profile_class'] = "v_aboutme";
            $tpl->addto_tplvar("SMB_VIEWPROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "viewprofile_field", $profile_data));
        }
    }
}

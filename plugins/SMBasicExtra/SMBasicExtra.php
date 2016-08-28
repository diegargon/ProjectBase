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
    global $tpl, $sm;
    includePluginFiles("SMBasicExtra");
    $tpl->getCSS_filePath("SMBasicExtra");
    $tpl->AddScriptFile("SMBasicExtra", "profile_extra", "BOTTOM");
    plugin_start("UserExtra");
    $user = $sm->getSessionUser();
    $userEx_data = uXtra_get($user['uid']);
    $tpl->addto_tplvar("SMB_PROFILE_FIELDS_BOTTOM", $tpl->getTPL_file("SMBasicExtra", "profile_fields", $userEx_data));

    register_action("SMBasic_ProfileChange", "SMB_Ex_ProfileChange");
}

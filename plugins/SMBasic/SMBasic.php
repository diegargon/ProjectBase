<?php

/*
 *  Copyright @ 2016 Diego Garcia
 * 
 * do_action("encrypt_password") // Override/set for change default one
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_Init() {
    global $sm, $cfg, $db;

    print_debug("SMBasic initialice", "PLUGIN_LOAD");

    includePluginFiles("SMBasic");

    !isset($sm) ? $sm = new SessionManager : false;
    $sm->start($cfg, $db);

    if (action_isset("encrypt_password") == false) {
        register_uniq_action("encrypt_password", "SMBasic_encrypt_password");
    }

    if (!$sm->checkSession()) {
        print_debug("Check session return false", "SM_DEBUG");
        $sm->setAnonSession();
    } else {
        print_debug("SMBasic: Check session OK", "SM_DEBUG");
    }

    defined('SM_DEBUG') ? SMBasic_sessionDebugDetails() : null;

    register_action("header_menu_element", "SMBasic_navLogReg");
}

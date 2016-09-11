<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Admin_init() {
    global $sm;
    print_debug("Admin Inititated", "PLUGIN_LOAD");

    //includePluginFiles("Admin");
    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if ((defined('ACL') && $acl_auth->acl_ask("admin_all")) || (!defined('ACL') && $user['isAdmin'])) {
            register_action("nav_element", "action_menu_opt");
        }
    }
    //register_action("common_web_structure", "adm_menu_opt");
}

function action_menu_opt() {
    adm_menu_opt();
}

function adm_menu_opt() {
    global $config, $tpl;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($config['FRIENDLY_URL']) {
        $data .= "{$config['WEB_LANG']}/admin";
    } else {
        $data .= "{$config['CON_FILE']}?module=Newspage&page=adm&lang={$config['WEB_LANG']}";
    }
    $data .= "'>" . "Admin" . "</a>";
    $data .= "</li>";

    $tpl->addto_tplvar("NAV_ELEMENT", $data);
}

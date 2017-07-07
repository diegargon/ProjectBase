<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function Admin_init() {
    global $sm;
    print_debug("Admin Inititated", "PLUGIN_LOAD");

    $user = $sm->getSessionUser();
    if ($user) {
        global $acl_auth;
        if ((defined('ACL') && $acl_auth->acl_ask("admin_all")) || (!defined('ACL') && $user['isAdmin'])) {
            register_action("header_menu_element", "action_menu_opt");
        }
    }
}

function action_menu_opt() {
    adm_menu_opt();
}

function adm_menu_opt() {
    global $cfg, $tpl;

    $data = "<li class='nav_left'>";
    $data .= "<a rel='nofollow' href='/";
    if ($cfg['FRIENDLY_URL']) {
        $data .= "{$cfg['WEB_LANG']}/admin";
    } else {
        $data .= "{$cfg['CON_FILE']}?module=Newspage&page=adm&lang={$cfg['WEB_LANG']}";
    }
    $data .= "'>" . "Admin" . "</a>";
    $data .= "</li>";

    $tpl->addto_tplvar("HEADER_MENU_ELEMENT", $data);
}

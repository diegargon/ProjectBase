<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("SMBasic");
require_once("includes/SMBasic.login.php");

$user = $sm->getSessionUser();

if ($user && $user['uid'] != 0) {
    $msgbox['MSG'] = "L_E_ALREADY_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

if (isset($_GET['active'])) {
    if (!SMBasic_user_activate_account()) {
        $msgbox['title'] = "L_SM_REGISTERED";
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
        $msgbox['backlink'] = $config['WEB_URL'];
        do_action("message_page", $msgbox);
        return false;
    } else {
        $msgbox['MSG'] = "L_SM_ACTIVATION_OK";
        $msgbox['backlink'] = $config['WEB_URL'];
        do_action("message_page", $msgbox);
        return false;
    }
}
if (isset($_GET['reset'])) {
    if (!SMBasic_user_reset_password()) {
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
        $msgbox['backlink'] = $config['WEB_URL'];
        do_action("message_page", $msgbox);
        return false;
    } else {
        $msgbox['MSG'] = "L_SM_RESET_OK";
        $msgbox['backlink'] = $config['WEB_URL'];
        do_action("message_page", $msgbox);
        return false;
    }
}
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['login'])) {
    SMBasic_Login();
} else if (!empty($_POST['reset_password_chk'])) {
    SMBasic_RequestResetOrActivation();
} else {
    do_action("common_web_structure");
    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    SMBasic_LoginScript();
    if ($config['FRIENDLY_URL']) {
        $login_data['register_url'] = "/{$config['WEB_LANG']}/register";
    } else {
        $login_data['register_url'] = "/app.php?module=SMBasic&page=register&lang={$config['WEB_LANG']}";
    }
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "login", $login_data));
}
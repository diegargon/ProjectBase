<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) {
    exit;
}

plugin_start("SMBasic");

require_once("includes/SMBasic.login.php");

if (S_SESSION_INT("isLogged") == 1) {
    $msgbox['MSG'] = "L_ERROR_ALREADY_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}
if (isset($_GET['active'])) {
    if (!SMBasic_user_activate_account()) {
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
        do_action("message_page", $msgbox);
    }
}
if (isset($_GET['reset'])) {
    if (!SMBasic_user_reset_password()) {
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
        do_action("message_page", $msgbox);
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
    $login_data['register_url'] = "app.php?module=SMBasic&page=register&lang={$config['WEB_LANG']}"; //TODO FRIENDLY
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "login", $login_data));
}
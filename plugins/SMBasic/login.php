<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("SMBasic");
require_once("includes/SMBasic.login.php");

$user = $sm->getSessionUser();

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_LOGIN'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_LOGIN'];
//END HEAD MOD

if ($user && $user['uid'] != 0) {
    $msgbox['MSG'] = "L_E_ALREADY_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

if (isset($_GET['active'])) {
    if (!SMBasic_user_activate_account()) {
        $msgbox['title'] = "L_SM_REGISTERED";
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
    } else {
        $msgbox['title'] = "L_SM_TITLE_OK";
        $msgbox['MSG'] = "L_SM_ACTIVATION_OK";
    }
    $msgbox['backlink'] = $cfg['WEB_URL'];    
    do_action("message_page", $msgbox);
    return false;
}

if (isset($_GET['reset'])) {
    if (!SMBasic_user_reset_password()) {
        $msgbox['MSG'] = "L_SM_E_ACTIVATION";
    } else {
        $msgbox['title'] = 'L_SM_TITLE_OK';
        $msgbox['MSG'] = "L_SM_RESET_OK";
    }
    $msgbox['backlink'] = $cfg['WEB_URL'];    
    do_action("message_page", $msgbox);
    return false;
}
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['login'])) {
    SMBasic_Login();
} else if (!empty($_POST['reset_password_chk'])) {
    SMBasic_RequestResetOrActivation();
} else {
    if ($cfg['smbasic_oauth']) {
        require_once 'includes/SMBasic-oauth.inc.php';
        if (!empty($_GET['provider'])) {
            SMB_oauth_DoLogin();
        } else {
            $login_data['oAuth_data'] = SMB_oauth_getLoginURL();
        }
    }
    do_action("common_web_structure");
    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    SMBasic_LoginScript();
    if ($cfg['FRIENDLY_URL']) {
        $login_data['register_url'] = "/{$cfg['WEB_LANG']}/register";
    } else {
        $login_data['register_url'] = "/{$cfg['CON_FILE']}?module=SMBasic&page=register&lang={$cfg['WEB_LANG']}";
    }
    $tpl->addto_tplvar("ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "login", $login_data));
}
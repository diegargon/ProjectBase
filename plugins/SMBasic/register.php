<?php
/*
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

plugin_start("SMBasic");

require_once("includes/SMBasic.register.php");

$user = $sm->getSessionUser();

if ($user && $user['uid'] != 0) {
    $msgbox['MSG'] = "L_E_ALREADY_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

if ((!isset($_POST['email']) || ($config['smbasic_need_username'] == 1) && !isset($_POST['username'])) &&
        !isset($_POST['password']) && !isset($_POST['register'])) {
    do_action("common_web_structure");
    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    SMBasic_RegisterScript();
    $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "register"));
} else {
    SMBasic_Register();
}
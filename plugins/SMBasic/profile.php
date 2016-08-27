<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

plugin_start("SMBasic");
require_once("includes/SMBasic.profile.php");

if (S_SESSION_INT("isLogged") != 1) {
    $msgbox['MSG'] = "L_ERROR_NOT_LOGGED";
    do_action("message_page", $msgbox);
    return false;
}

if (isset($_POST['profile'])) {
    SMBasic_ProfileChange();
} else if (isset($_GET['viewprofile'])) {
    SMBasic_ViewProfile();
} else {
    if (($user = $sm->getSessionUser()) == false) {
        $sm->destroy();
        $msgbox['MSG'] = "L_SM_E_USER_NOT_EXISTS";
        do_action("message_page", $msgbox);
    } else {
        do_action("common_web_structure");
        $tpl->getCSS_filePath("SMBasic");
        $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
        SMBasic_ProfileScript();
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "profile", $user));
    }
}
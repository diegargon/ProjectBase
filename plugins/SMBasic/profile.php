<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

plugin_start("SMBasic");
require_once("includes/SMBasic.profile.php");

$user = $sm->getSessionUser();

//HEAD MOD
$cfg['PAGE_TITLE'] = $cfg['WEB_NAME'] . ": " . $LNG['L_PROFILE'];
$cfg['PAGE_DESC'] = $cfg['WEB_NAME'] . ": " . $LNG['L_PROFILE'];
//END HEAD MOD

if (empty($user) || $user['uid'] == 0) {
    $msgbox['MSG'] = "L_E_NOT_LOGGED";
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
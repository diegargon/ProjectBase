<?php

/*
  File: index.php
 */
!defined('IN_WEB') ? exit : true;

$user = $sm->getSessionUser();

if (!$user || ( !defined('ACL') && !$acl_auth->acl_ask("admin_read")) ) {
    $msgbox['MSG'] = "L_E_NOACCESS";
    do_action("message_page", $msgbox);
    return false;
} 

if (!defined('ACL') && $user['isAdmin'] != 1) {
    $msgbox['MSG'] = "L_E_NOACCESS";
    do_action("message_page", $msgbox);
    return false;
}

includePluginFiles("Admin");
admin_load_plugin_files();

!($admtab = S_GET_INT("admtab")) ? $admtab = 1 : false;

$tpl->addto_tplvar("ADMIN_TAB_ACTIVE", $admtab);
$tpl->getCSS_filePath("Admin");
$params['admtab'] = $admtab;
$tpl->addto_tplvar("ADD_ADMIN_MENU", do_action("add_admin_menu", $params));
$tpl->addto_tplvar("ADD_TOP_MENU", do_action("add_top_menu"));
$tpl->addto_tplvar("ADD_BOTTOM_MENU", do_action("add_bottom_menu"));

if ($admtab == 1) {
    $tpl->addto_tplvar("ADD_ADMIN_CONTENT", Admin_generalContent($params));
} else {
    $tpl->addto_tplvar("ADD_ADMIN_CONTENT", do_action("admin_get_content", $params));
}
$tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("Admin", "admin_main_body"));
do_action("common_web_structure");

<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_AdminInit() {
    register_action("add_admin_menu", "SMBasic_AdminMenu", "5");
}

function SMBasic_AdminMenu($params) {
    $tab_num = 100; //TODO: A WAY TO ASSIGN UNIQ NUMBERS
    if ($params['admtab'] == $tab_num) {
        register_uniq_action("admin_get_content", "SMBasic_AdminContent");
        return "<li class='tab_active'><a href='admin&admtab=$tab_num'>SMBasic</a></li>";
    } else {
        return "<li><a href='admin&admtab=$tab_num'>SMBasic</a></li>";
    }
}

function SMBasic_AdminContent($params) {
    global $LANGDATA, $tpl;

    includePluginFiles("SMBasic", 1);

    $tpl->getCSS_filePath("SMBasic");
    $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LANGDATA['L_PL_STATE'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=2'>" . $LANGDATA['L_SM_SEARCH_USER'] . "</a></li>\n";
    $page_data['ADM_ASIDE_OPTION'] .= "<li><a href='admin&admtab=" . $params['admtab'] . "&opt=3'>" . $LANGDATA['L_SM_USERS_LIST'] . "</a></li>\n";

    $opt = S_GET_INT("opt");
    if ($opt == 1 || $opt == false) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_GENERAL'] . ": " . $LANGDATA['L_PL_STATE'];
        $page_data['ADM_CONTENT'] = Admin_GetPluginState("SMBasic");
    } else if ($opt == 2) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_SM_SEARCH_USER'];
        $page_data['ADM_CONTENT'] = $LANGDATA['L_SM_USERS_DESC'] . SMBasic_UserSearch();
    } else if ($opt == 3) {
        $page_data['ADM_CONTENT_H2'] = $LANGDATA['L_SM_USERS_LIST'];
        $page_data['ADM_CONTENT'] = $LANGDATA['L_SM_USERS_LIST_DESC'] . SMBasic_UserList();
    }

    return $tpl->getTPL_file("Admin", "admin_std_content", $page_data);
}

function SMBasic_UserSearch() {
    global $config, $LANGDATA, $tpl, $sm;

    if (isset($_POST['btnDeleteSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        SMBasic_DeleteUser($member_id);
    }
    if (isset($_POST['btnActivateSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        SMBasic_ActivateUser($member_id);
    }
    if (isset($_POST['btnDisableSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        $disable_state = S_POST_INT("member_disable", 1, 1);
        SMBasic_DisableUser($member_id, $disable_state);
    }

    $content = $tpl->getTPL_file("SMBasic", "sm_adm_usersearch_form");

    isset($_POST['posted_glob']) ? $glob = 1 : $glob = 0;
    isset($_POST['posted_email']) ? $email = 1 : $email = 0;
    $s_string = S_POST_STRICT_CHARS("search_user", 32, 1);

    if (!empty($_POST['btnSearchUser']) && !empty($s_string)) {
        if (($users_ary = $sm->searchUser($s_string, $email, $glob))) {

            $table['ADM_TABLE_ROW'] = "";
            foreach ($users_ary as $user_match) {
                if ($config['FRIENDLY_URL']) {
                    $user_match['profile_url'] = "/{$config['WEB_LANG']}/profile?viewprofile={$user_match['uid']}";
                } else {
                    $user_match['profile_url'] = "/{$config['CON_FILE']}?module=SMBasic&page=profile?lang={$config['WEB_LANG']}&viewprofile={$user_match['uid']}";
                }
                $table['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user_match);
            }
            $content .= $tpl->getTPL_file("SMBasic", "memberlist", $table);
        }
    }
    return $content;
}

function SMBasic_UserList() {
    global $config, $LANGDATA, $tpl, $sm;

    if (isset($_POST['btnDeleteSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        SMBasic_DeleteUser($member_id);
    }
    if (isset($_POST['btnActivateSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        SMBasic_ActivateUser($member_id);
    }
    if (isset($_POST['btnDisableSubmit']) && ( ($member_id = S_POST_INT("member_uid") )) > 0) {
        $disable_state = S_POST_INT("member_disable", 1, 1);
        SMBasic_DisableUser($member_id, $disable_state);
    }

    $users_list = $sm->getAllUsersArray();

    $active['ADM_TABLE_ROW'] = $inactive['ADM_TABLE_ROW'] = $disable['ADM_TABLE_ROW'] = "";

    foreach ($users_list as $user) {
        if ($config['FRIENDLY_URL']) {
            $user['profile_url'] = "/{$config['WEB_LANG']}/profile?viewprofile={$user['uid']}";
        } else {
            $user['profile_url'] = "/{$config['CON_FILE']}?module=SMBasic&page=profile?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}";
        }
        if ($user['active'] == 0 && !$user['disable']) {

            $active['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        } else if ($user['active'] > 0 && !$user['disable']) {
            $inactive['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        } else if ($user['disable']) {
            $disable['ADM_TABLE_ROW'] .= $tpl->getTPL_file("SMBasic", "sm_adm_userlist", $user);
        }
    }

    $active['ADM_TABLE_TITLE'] = $LANGDATA['L_SM_USERS_ACTIVE'];
    $inactive['ADM_TABLE_TITLE'] = $LANGDATA['L_SM_USERS_INACTIVE'];
    $disable['ADM_TABLE_TITLE'] = $LANGDATA['L_SM_USERS_DISABLE'];

    $content = $tpl->getTPL_file("SMBasic", "memberlist", $active);
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", $inactive);
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", $disable);

    return $content;
}

function SMBasic_DeleteUser($uid) {
    global $db;
    $db->delete("users", array("uid" => $uid), "LIMIT 1");
}

function SMBasic_DisableUser($uid, $state) {
    global $db;
    empty($state) ? $new_state = 1 : $new_state = 0;
    $db->update("users", array("disable" => $new_state), array("uid" => $uid), "LIMIT 1");
}

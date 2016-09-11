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
    $page_data['ADM_ASIDE_OPTION'] = "<li><a href='?admin&admtab=" . $params['admtab'] . "&opt=1'>" . $LANGDATA['L_PL_STATE'] . "</a></li>\n";
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

    $content = "<form action='' method='post'>";
    $content .= "<label for='glob'>{$LANGDATA['L_SM_GLOB']}: </label><input type='checkbox' name='posted_glob' id='glob' value='1' />";
    $content .= "<label for='email'>{$LANGDATA['L_EMAIL']}: </label><input type='checkbox' name='posted_email' id='email' value='1' />";
    $content .= "<input type='text' maxlength='32' minlength='3' name='search_user' id='search_user' required />";
    $content .= "<input type='submit' name='btnSearchUser' id='btnSearchUser' />";
    $content .= "</form><br/>";
    isset($_POST['posted_glob']) ? $glob = 1 : $glob = 0;
    isset($_POST['posted_email']) ? $email = 1 : $email = 0;
    $s_string = S_POST_STRICT_CHARS("search_user", 32, 1);

    if (!empty($_POST['btnSearchUser']) && !empty($s_string)) {
        if (($users_ary = $sm->searchUser($s_string, $email, $glob))) {
            $table['ADM_TABLE_TH'] = "<th>" . $LANGDATA ['L_SM_USERNAME'] . "</th>";
            $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_EMAIL'] . "</th>";
            $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_REGISTERED'] . "</th>";
            $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_LASTLOGIN'] . "</th>";
            $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_ACTIONS'] . "</th>";
            $table['ADM_TABLE_ROW'] = "";
            foreach ($users_ary as $user_match) {
                $table['ADM_TABLE_ROW'] .= "<tr>";
                $table['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user_match['uid']}'>" . $user_match['username'] . "</a></td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . $user_match['email'] . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . format_date($user_match['regdate']) . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td>" . $user_match['last_login'] . "</td>";
                $table['ADM_TABLE_ROW'] .= "<td><form action='' method='post'>";
                $table['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_uid' class='member_uid' value='{$user_match['uid']}' />";
                $table['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_disable' value='{$user_match['disable']}' />";
                $table['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDeleteSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_DELETE']}' />";
                if ($user_match['active'] > 0) {
                    $table['ADM_TABLE_ROW'] .= "<input type='submit' name='btnActivateSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_ACTIVATE']}' />";
                }
                if ($user_match['disable']) {
                    $table['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDisableSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_ENABLE']}' />";
                } else {
                    $table['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDisableSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_DISABLE']}' />";
                }
                $table['ADM_TABLE_ROW'] .= "</form></td>";
                $table['ADM_TABLE_ROW'] .= "</tr>";
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

    $table['ADM_TABLE_TH'] = "<th>" . $LANGDATA ['L_SM_USERNAME'] . "</th>";
    $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_EMAIL'] . "</th>";
    $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_REGISTERED'] . "</th>";
    $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_LASTLOGIN'] . "</th>";
    $table['ADM_TABLE_TH'] .= "<th>" . $LANGDATA ['L_SM_ACTIONS'] . "</th>";

    $active['ADM_TABLE_ROW'] = $inactive['ADM_TABLE_ROW'] = $disable['ADM_TABLE_ROW'] = "";

    foreach ($users_list as $user) {
        if ($user['active'] == 0 && !$user['disable']) {
            $active['ADM_TABLE_ROW'] .= "<tr>";
            $active['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}'>" . $user['username'] . "</a></td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td>" . format_date($user['last_login']) . "</td>";
            $active['ADM_TABLE_ROW'] .= "<td><form action='' method='post'>";
            $active['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_uid'  value='{$user['uid']}' />";
            $active['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_disable' value='{$user['disable']}' />";
            $active['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDeleteSubmit' id='btnDeleteSubmit' value='{$LANGDATA['L_SM_DELETE']}' />";
            $active['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDisableSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_DISABLE']}' />";
            $active['ADM_TABLE_ROW'] .= "</form></td>";
            $active['ADM_TABLE_ROW'] .= "</tr>";
        } else if ($user['active'] > 0 && !$user['disable']) {
            $inactive['ADM_TABLE_ROW'] .= "<tr>";
            $inactive['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}'>" . $user['username'] . "</a></td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td>" . format_date($user['last_login']) . "</td>";
            $inactive['ADM_TABLE_ROW'] .= "<td><form action='' method='post'>";
            $inactive['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_uid'  value='{$user['uid']}' />";
            $inactive['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_disable' value='{$user['disable']}' />";
            $inactive['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDeleteSubmit' id='btnDeleteSubmit' value='{$LANGDATA['L_SM_DELETE']}' />";
            $inactive['ADM_TABLE_ROW'] .= "<input type='submit' name='btnActivateSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_ACTIVATE']}' />";
            $inactive['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDisableSubmit'class='btnSubmit' value='{$LANGDATA['L_SM_DISABLE']}' />";
            $inactive['ADM_TABLE_ROW'] .= "</form></td>";
            $inactive['ADM_TABLE_ROW'] .= "</tr>";
        } else if ($user['disable']) {
            $disable['ADM_TABLE_ROW'] .= "<tr>";
            $disable['ADM_TABLE_ROW'] .= "<td><a href='/profile.php?lang={$config['WEB_LANG']}&viewprofile={$user['uid']}'>" . $user['username'] . "</a></td>";
            $disable['ADM_TABLE_ROW'] .= "<td>" . $user['email'] . "</td>";
            $disable['ADM_TABLE_ROW'] .= "<td>" . format_date($user['regdate']) . "</td>";
            $disable['ADM_TABLE_ROW'] .= "<td>" . format_date($user['last_login']) . "</td>";
            $disable['ADM_TABLE_ROW'] .= "<td><form action='' method='post'>";
            $disable['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_uid'  value='{$user['uid']}' />";
            $disable['ADM_TABLE_ROW'] .= "<input type='hidden' name='member_disable' value='{$user['disable']}' />";
            $disable['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDeleteSubmit' id='btnDeleteSubmit' value='{$LANGDATA['L_SM_DELETE']}' />";
            if ($user['active'] > 0) {
                $disable['ADM_TABLE_ROW'] .= "<input type='submit' name='btnActivateSubmit' class='btnSubmit' value='{$LANGDATA['L_SM_ACTIVATE']}' />";
            }
            $disable['ADM_TABLE_ROW'] .= "<input type='submit' name='btnDisableSubmit'class='btnSubmit' value='{$LANGDATA['L_SM_ENABLE']}' />";
            $disable['ADM_TABLE_ROW'] .= "</form></td>";
            $disable['ADM_TABLE_ROW'] .= "</tr>";
        }
    }

    $active['ADM_TABLE_TITLE'] = "<h3>" . $LANGDATA['L_SM_USERS_ACTIVE'] . "</h3>";
    $inactive['ADM_TABLE_TITLE'] = "<h3>" . $LANGDATA['L_SM_USERS_INACTIVE'] . "</h3>";
    $disable['ADM_TABLE_TITLE'] = "<h3>" . $LANGDATA['L_SM_USERS_DISABLE'] . "</h3>";

    $content = $tpl->getTPL_file("SMBasic", "memberlist", array_merge($table, $active));
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", array_merge($table, $inactive));
    $content .= $tpl->getTPL_file("SMBasic", "memberlist", array_merge($table, $disable));

    return $content;
}

function SMBasic_DeleteUser($uid) {
    global $db;
    $db->delete("users", array("uid" => $uid), "LIMIT 1");
}

function SMBasic_ActivateUser($uid) {
    global $db;
    $db->update("users", array("active" => 0), array("uid" => $uid), "LIMIT 1");
}

function SMBasic_DisableUser($uid, $state) {
    global $db;
    empty($state) ? $new_state = 1 : $new_state = 0;
    $db->update("users", array("disable" => $new_state), array("uid" => $uid), "LIMIT 1");
}
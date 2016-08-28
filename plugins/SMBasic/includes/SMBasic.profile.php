<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_ProfileScript() {
    global $tpl;
    $tpl->AddScriptFile("standard", "jquery.min", "TOP");
    $tpl->AddScriptFile("SMBasic", "profile", "BOTTOM");
}

function SMBasic_ViewProfile() {
    global $tpl, $sm;

    $uid = S_GET_INT("viewprofile", 11, 1);
    if (empty($uid)) {
        $msgbox['MSG'] = "L_SM_E_USER_NOT_EXISTS";
        do_action("message_page", $msgbox);
    }
    $v_user = $sm->getUserbyID($uid);
    if ($v_user) {
        do_action("common_web_structure");
        $tpl->getCSS_filePath("SMBasic");
        $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "viewprofile", $v_user));
    } else {
        $msgbox['MSG'] = "L_SM_E_USER_NOT_EXISTS";
        do_action("message_page", $msgbox);
    }
}

function SMBasic_ProfileChange() {
    global $LANGDATA, $config, $db, $sm;

    if (empty($_POST['cur_password']) || strlen($_POST['cur_password']) < $config['sm_min_password']) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_ERROR_PASSWORD_EMPTY_SHORT'] . '"}]');
    }
    if (!$password = S_POST_PASSWORD("cur_password")) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_ERROR_PASSWORD'] . '"}]');
    }

    $password_encrypted = do_action("encrypt_password", $password);

    $user = $sm->getSessionUser();
    if (empty($user)) {
        die('[{"status": "0", "msg": "' . $LANGDATA['L_ERROR_INTERNAL'] . '"}]');
    }
    //Check USER password
    $query = $db->select_all("users", array("uid" => $user['uid'], "password" => "$password_encrypted"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_WRONG_PASSWORD'] . '"}]');
    }

    $q_set_ary = [];

    if (!empty($_POST['avatar'])) {
        $avatar = S_VALIDATE_MEDIA($_POST['avatar'], 256);
        if ($avatar < 0) {
            die('[{"status": "6", "msg": "' . $LANGDATA['L_SM_E_AVATAR'] . '"}]');
        } else {
            $user['avatar'] != $avatar ? $q_set_ary['avatar'] = $db->escape_strip($avatar) : false;
        }
    }

    if ((!empty($_POST['new_password']) && empty($_POST['r_password']) ) ||
            (!empty($_POST['r_password']) && empty($_POST['new_password']) )
    ) {
        die('[{"status": "3", "msg": "' . $LANGDATA['L_ERROR_NEW_BOTH_PASSWORD'] . '"}]');
    }

    if (!empty($_POST['new_password']) && !empty($_POST['r_password'])) {
        if ($_POST['new_password'] != $_POST['r_password']) {
            die('[{"status": "3", "msg": "' . $LANGDATA['L_ERROR_NEW_PASSWORD_NOTMATCH'] . '"}]');
        }
        if ((strlen($_POST['new_password']) < $config['sm_min_password'])) {
            die('[{"status": "3", "msg": "' . $LANGDATA['L_ERROR_NEWPASS_TOOSHORT'] . '"}]');
        }
        if (($new_password = S_POST_PASSWORD("new_password")) != false) {
            $new_password_encrypt = do_action("encrypt_password", $new_password);
            $q_set_ary['password'] = $new_password_encrypt;
        }
    }

    if (( $config['smbasic_can_change_username'] == 1)) {
        if (empty($_POST['username']) && $config['smbasic_need_username'] == 1) {
            die('[{"status": "4", "msg": "' . $LANGDATA['L_USERNAME_EMPTY'] . '"}]');
        } else if (empty($_POST['username']) && $config['smbasic_need_username'] == 0) {
            $q_set_ary['username'] = '';
        } else {
            if (strlen($_POST['username']) < $config['smbasic_min_username']) {
                die('[{"status": "4", "msg": "' . $LANGDATA['L_USERNAME_SHORT'] . '"}]');
            }
            if (strlen($_POST['username']) > $config['smbasic_max_username']) {
                die('[{"status": "4", "msg": "' . $LANGDATA['L_USERNAME_LONG'] . '"}]');
            }
            if (($username = S_POST_STRICT_CHARS("username", $config['smbasic_max_username'], $config['smbasic_min_username'])) == false) {
                die('[{"status": "4", "msg": "' . $LANGDATA['L_USERNAME_CHARS'] . '"}]');
            }
            if ($user['username'] != $username && !empty($username)) {
                $query = $db->select_all("users", array("username" => "$username"), "LIMIT 1");
                if ($db->num_rows($query) > 0) {
                    die('[{"status": "4", "msg": "' . $LANGDATA['L_ERROR_USERNAME_EXISTS'] . '"}]');
                } else {
                    $q_set_ary['username'] = $username;
                }
            }
        }
    }

    if (( $config['smbasic_can_change_email'] == 1)) {
        if (($email = S_POST_EMAIL("email")) == false) {
            die('[{"status": "4", "msg": "' . $LANGDATA['L_ERROR_EMAIL'] . '"}]');
        }
        if (strlen($email) > $config['smbasic_max_email']) {
            die('[{"status": "4", "msg": "' . $LANGDATA['L_EMAIL_LONG'] . '"}]');
        }
        if ($email != $user['email']) {
            $query = $db->select_all("users", array("email" => "$email"), "LIMIT 1");
            if ($db->num_rows($query) > 0) {
                die('[{"status": "5", "msg": "' . $LANGDATA['L_ERROR_EMAIL_EXISTS'] . '"}]');
            } else {
                $q_set_ary["email"] = $email;
            }
        }
    }

    do_action("SMBasic_ProfileChange", $q_set_ary);

    !empty($q_set_ary) ? $db->update("users", $q_set_ary, array("uid" => $user['uid']), "LIMIT 1") : false;

    die('[{"status": "ok", "msg": "' . $LANGDATA['L_UPDATE_SUCCESSFUL'] . '", "url": "' . S_SERVER_REQUEST_URI() . '"}]');
}

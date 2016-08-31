<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

 function SMBasic_Login() {
    global $config, $LANGDATA, $db, $sm;

    if ((($email = S_POST_EMAIL("email")) != false) &&
            ($email != null) &&
            ( ($password = S_POST_PASSWORD("password")) != false)
    ) {
        $password = do_action("encrypt_password", $password);
        if (empty($password)) {
            $msgbox['MSG'] = "L_E_INTERNAL";
            do_action("message_page", $msgbox);
            return false;
        }
        $query = $db->select_all("users", array("email" => "$email", "password" => "$password"), "LIMIT 1");
        if (($user = $db->fetch($query))) {
            if ($user['active'] == 0) {
                if ($user['disable'] == 1) {
                    die('[{"status": "error", "msg": "' . $LANGDATA['L_SM_E_DISABLE'] . '"}]');
                } else {
                    $sm->setSession($user);
                    if (($config['smbasic_session_persistence']) && !empty($_POST['rememberme'])) {
                        $sm->setCookies(S_SESSION_CHAR_AZNUM("sid", 32), S_SESSION_INT("uid", 11));
                    }
                    die('[{"status": "ok", "msg": "' . $config['WEB_URL'] . '"}]');
                }
            } else {
                if ($user['active'] > 0) { //-1 disable by admin not send email
                    $mail_msg = SMBasic_create_reg_mail($user['active']);
                    mail($user['email'], $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$config['EMAIL_SENDMAIL']} \r\n");
                }
                die('[{"status": "error", "msg": "' . $LANGDATA['L_ACCOUNT_INACTIVE'] . '"}]');
            }
        } else {
            die('[{"status": "error", "msg": "' . $LANGDATA['L_E_EMAILPASSWORD'] . '"}]');
        }
        $db->free($query);
    } else {
        die('[{"status": "error", "msg": "' . $LANGDATA['L_E_EMAILPASSWORD'] . '"}]');
    }
}

function SMBasic_user_activate_account() {
    global $db;

    if (($active = S_GET_INT("active", 12)) == false) {
        return false;
    }
    $query = $db->select_all("users", array("active" => "$active"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        return false;
    }
    
    $db->update("users", array("active" => 0), array("active" => $active));   

    return true;
}

function SMBasic_RequestResetOrActivation() {
    global $LANGDATA, $config, $db;

    if (($email = S_POST_EMAIL("email")) == false) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_E_EMAIL'] . '"}]');
        return false;
    }
    if (strlen($email) > $config['smbasic_max_email']) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_EMAIL_LONG'] . '"}]');
        return false;
    }
    $query = $db->select_all("users", array("email" => "$email"), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_E_EMAIL_NOEXISTS'] . '"}]');
    } else {
        $user = $db->fetch($query);
        if ($user['active'] > 1) {
            $mail_msg = SMBasic_create_reg_mail($user['active']);
            mail($email, $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$config['EMAIL_SENDMAIL']} \r\n");
            die('[{"status": "2", "msg": "' . $LANGDATA['L_ACTIVATION_EMAIL'] . '"}]');
        } else {
            $reset = mt_rand(11111111, 2147483647);
            $db->update("users", array("reset" => "$reset"), array("email" => "$email"));
            $URL = "{$config['WEB_URL']}" . "/login" . "&reset=$reset&email=$email";
            $msg = $LANGDATA['L_RESET_EMAIL_MSG'] . "\n" . "$URL";
            mail($email, $LANGDATA['L_RESET_EMAIL_SUBJECT'], $msg, "From: {$config['EMAIL_SENDMAIL']} \r\n");
            die('[{"status": "2", "msg": "' . $LANGDATA['L_RESET_EMAIL'] . '"}]');
        }
    }

    return false;
}

function SMBasic_user_reset_password() {
    global $config, $LANGDATA, $db;

    $reset = S_GET_INT('reset');
    $email = S_GET_EMAIL('email');
    if ($reset == false || $email == false) {
        return false;
    }
    $query = $db->select_all("users", array("email" => "$email", "reset" => "$reset"));
    if ($db->num_rows($query) > 0) {
        $user = $db->fetch($query);
        $password = SMBasic_randomPassword();
        $password_encrypted = do_action("encrypt_password", $password);
        $db->update("users", array("password" => "$password_encrypted", "reset" => "0"), array("uid" => "{$user['uid']}"));
        $URL = "{$config['WEB_URL']}" . "/{$config['WEB_LANG']}" . "/login";
        $msg = $LANGDATA['L_RESET_SEND_NEWMAIL_MSG'] . "\n" . "$password\n" . "$URL";
        mail($email, $LANGDATA['L_RESET_SEND_NEWMAIL_SUBJECT'], $msg, "From: {$config['EMAIL_SENDMAIL']} \r\n");
        echo $LANGDATA['L_RESET_PASSWORD_SUCCESS'];
        exit(0); // TODO MSG RESET OK
    } else {
        return false;
    }
}

function SMBasic_randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = [];
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

    return implode($pass);
}

function SMBasic_LoginScript() {
    global $tpl;

    $tpl->AddScriptFile("standard", "jquery.min", "TOP");
    $tpl->AddScriptFile("SMBasic", "login", "BOTTOM");
}

<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_RegisterScript() {
    global $tpl;
    $tpl->AddScriptFile("standard", "jquery", "TOP");
    $tpl->AddScriptFile("SMBasic", "register", "BOTTOM");
}

function SMBasic_Register() {
    global $config, $LANGDATA, $db;

    if (($email = S_POST_EMAIL("email")) == false) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_E_EMAIL'] . '"}]');
    }
    if (($config['smbasic_need_username'] == 1) &&
            (($username = S_POST_STRICT_CHARS("username", $config['smbasic_max_username'])) == false)) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_E_USERNAME'] . '"}]');
    }
    if (($config['smbasic_need_username'] == 1) &&
            (strlen($username) < $config['smbasic_min_username'])
    ) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_USERNAME_SHORT'] . '"}]');
    }
    if (($password = S_POST_PASSWORD("password")) == false) {
        die('[{"status": "3", "msg": "' . $LANGDATA['L_E_PASSWORD'] . '"}]');
    }
    if (strlen($_POST['password']) < $config['sm_min_password']) {
        die('[{"status": "3", "msg": "' . $LANGDATA['L_E_PASSWORD_MIN'] . '"}]');
    }

    $query = $db->select_all("users", array("username" => "$username"), "LIMIT 1");

    if (($db->num_rows($query)) > 0) {
        die('[{"status": "2", "msg": "' . $LANGDATA['L_E_USERNAME_EXISTS'] . '"}]');
    }

    $query = $db->select_all("users", array("email" => "$email"));
    if (($db->num_rows($query)) > 0) {
        die('[{"status": "1", "msg": "' . $LANGDATA['L_E_EMAIL_EXISTS'] . '"}]');
    }

    $db->free($query);

    $password = do_action("encrypt_password", $password);
    if ($config['smbasic_email_confirmation']) {
        $active = mt_rand(11111111, 2147483647); //Largest mysql init
        $register_message = $LANGDATA['L_REGISTER_OKMSG_CONFIRMATION'];
    } else {
        $active = 1;
        $register_message = $LANGDATA['L_REGISTER_OKMSG'];
    }
    $mail_msg = SMBasic_create_reg_mail($active);
    $query = $db->insert("users", array("username" => "$username", "password" => "$password", "email" => "$email", "active" => "$active"));

    if ($query) {
        mail($email, $LANGDATA['L_REG_EMAIL_SUBJECT'], $mail_msg, "From: {$config['EMAIL_SENDMAIL']} \r\n");
        die('[{"status": "ok", "msg": "' . $register_message . ', "url": "' . $config['WEB_URL'] . '"}]');
    } else {
        die('[{"status": "7", "msg": "' . $LANGDATA['L_REG_ERROR_WHILE_REG'] . '"}]');
    }

    return true;
}

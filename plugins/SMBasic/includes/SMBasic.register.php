<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_RegisterScript() {
    global $tpl;
    $tpl->AddScriptFile("standard", "jquery.min", "TOP");
    $tpl->AddScriptFile("SMBasic", "register", "BOTTOM");
}

function SMBasic_Register() {
    global $config, $LANGDATA, $db;

    if (($email = S_POST_EMAIL("email")) == false) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if (($config['smbasic_need_username'] == 1) &&
            (($username = S_POST_STRICT_CHARS("username", $config['smbasic_max_username'])) == false)) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_USERNAME']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if (($config['smbasic_need_username'] == 1) &&
            (strlen($username) < $config['smbasic_min_username'])
    ) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_USERNAME_SHORT']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if (($password = S_POST_PASSWORD("password")) == false) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_PASSWORD']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }
    if (strlen($_POST['password']) < $config['sm_min_password']) {
        $response[] = array("status" => "3", "msg" => $LANGDATA['L_ERROR_PASSWORD_MIN']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }

    $query = $db->select_all("users", array("username" => "$username"), "LIMIT 1");
    if (($db->num_rows($query)) > 0) {
        $response[] = array("status" => "2", "msg" => $LANGDATA['L_ERROR_USERNAME_EXISTS']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
    }

    $query = $db->select_all("users", array("email" => "$email"));
    if (($db->num_rows($query)) > 0) {
        $response[] = array("status" => "1", "msg" => $LANGDATA['L_ERROR_EMAIL_EXISTS']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        return false;
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
        $response[] = array("status" => "ok", "msg" => $register_message, "url" => $config['WEB_URL']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    } else {
        $response[] = array("status" => "7", "msg" => $LANGDATA['L_REG_ERROR_WHILE']);
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
    }

    return true;
}

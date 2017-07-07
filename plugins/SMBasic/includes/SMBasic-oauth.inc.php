<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMB_oauth_DoLogin() {
    global $cfg;

    if ($cfg['smbasic_oauth_facebook'] && $_GET['provider'] == "facebook") {
        SMB_oauth_DoLoginFB();
    }
}

function SMB_oauth_DoLoginFB() {
    global $sm;

    $fb = SMB_oauth_getFB_Handle();

    if (!($token = SMB_getFB_Token($fb) )) {
        return false;
    }

    if (!( $oauth_data = SMB_oauth_getFB_data($fb, $token))) {
        return false;
    }

    if (($response = SMB_oauth_checkif_register($fb, $oauth_data['id'])) != false) {
        $sm->setData("oauth_token", $token);
        $sm->setData("uid", $response);
        $sm->setData("session_ip", S_SERVER_REMOTE_ADDR());
        $sm->setData("session_user_agent", S_SERVER_USER_AGENT());

        header('Location: /');
        exit();
    } else {
        if (($reg_resp = SMB_oauth_FB_register($fb, $oauth_data))) {
            $sm->setData("oauth_token", $token);
            $sm->setData("uid", $reg_resp);
            $sm->setData("session_ip", S_SERVER_REMOTE_ADDR());
            $sm->setData("session_user_agent", S_SERVER_USER_AGENT());

            $msg['title'] = 'L_REGISTER_OKMSG';
            $msg['MSG'] = 'L_REGISTER_OKMSG';
            $msg['backlink'] = '/';
            do_action("message_page", $msg);
        } else {
            return false;
        }
    }

    return true;
}

function SMB_oauth_FB_register($fb, $oauth_user) {
    global $db;

    $picture = $oauth_user->getField('picture');
    if (!empty($picture)) {
        $picture = $db->escape_strip($picture['url']);
        $fb_userdata['picture'] = $picture;
    }

    $fb_userdata['oauth_provider'] = "facebook";
    if (empty($oauth_user['id'])) {
        $error['MSG'] = 'L_SM_OAUTH_E_ID';
        do_action("message_page", $error);
        return false;
    } else {
        $fb_userdata['oauth_uid'] = $oauth_user['id'];
    }

    if (empty($oauth_user['id'])) {
        $error['MSG'] = 'L_SM_OAUTH_E_ID';
        do_action("message_page", $error);
        return false;
    } else {
        $fb_userdata['oauth_uid'] = $oauth_user['id'];
    }

    if (empty($oauth_user['email'])) {
        $error['MSG'] = 'L_SM_OAUTH_E_EMAIL';
        do_action("message_page", $error);
        return false;
    } else {
        $fb_userdata['email'] = $oauth_user['email'];
    }
    !empty($oauth_user['first_name']) ? $fb_userdata['first_name'] = $db->escape_strip($oauth_user['first_name']) : false;
    !empty($oauth_user['last_name']) ? $fb_userdata['last_name'] = $db->escape_strip($oauth_user['last_name']) : false;
    !empty($oauth_user['gender']) ? $fb_userdata['gender'] = $db->escape_strip($oauth_user['gender']) : false;
    !empty($oauth_user['link']) ? $fb_userdata['link'] = $db->escape_strip($oauth_user['link']) : false;


    $query = $db->select_all("oauth_users", array("oauth_uid" => $oauth_user['id']), "LIMIT 1");
    if ($db->num_rows($query) > 0) {
        $error['MSG'] = 'L_SM_E_USER_EXISTS';
        do_action("message_page", $error);
        return false;
    }

    $query = $db->select_all("oauth_users", array("email" => $oauth_user['email']), "LIMIT 1");
    if ($db->num_rows($query) > 0) {
        $error['MSG'] = 'L_E_EMAIL_EXISTS';
        do_action("message_page", $error);
        return false;
    }

    $db->insert("oauth_users", $fb_userdata);

    /*
     * Normal user account exists linking
     */
    $query = $db->select_all("users", array("email" => $oauth_user['email']), "LIMIT 1");
    if ($db->num_rows($query) > 0) {
        $real_account = $db->fetch($query);
        $db->update("oauth_users", array("uid" => $real_account['uid']), array("oauth_provider" => "facebook", "oauth_uid" => $fb_userdata['oauth_uid']), "LIMIT 1");
        if ($real_account['avatar'] == null && !empty($picture)) {
            $db->update("users", array("avatar" => $picture), array("uid" => $real_account['uid']));
        }
        $msg['title'] = 'L_REGISTER_OKMSG';
        $msg['MSG'] = 'L_SM_OAUTH_LINKED';
        $msg['backlink'] = "/";
        do_action("message_page", $msg);
        return $real_account['uid'];
    }
    /*
     *  create the normal account
     */
    $random = rand(11, 32);
    $password = do_action("encrypt_password", $random);
    $normal_account = array(
        'password' => $password,
        'email' => $fb_userdata['email'],
        'active' => 0,
    );
    $picture ? $normal_account['avatar'] = $picture : false;

    //We need create a username use mail, if already exist use email first+last or error
    $user_pos = strpos($fb_userdata['email'], '@');
    $new_username = substr($fb_userdata['email'], 0, $user_pos);
    $new_username = $db->escape_strip($new_username);
    $query = $db->select_all("users", array("username" => $new_username), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        $normal_account['username'] = $new_username;
        $db->insert("users", $normal_account);
        $uid = $db->insert_id();
        $db->update("oauth_users", array("uid" => $uid), array("oauth_provider" => "facebook", "oauth_uid" => $fb_userdata['oauth_uid']), "LIMIT 1");
        return $uid;
    }
    $new_username = preg_replace("/\s+/", "", $fb_userdata['first_name'] . $fb_userdata['last_name']);
    $new_username = $db->escape_strip($new_username);
    $query = $db->select_all("users", array("username" => $new_username), "LIMIT 1");
    if ($db->num_rows($query) <= 0) {
        $normal_account['username'] = $new_username;
        $db->insert("users", $normal_account);
        $uid = $db->insert_id();
        $db->update("oauth_users", array("uid" => $uid), array("oauth_provider" => "facebook", "oauth_uid" => $fb_userdata['oauth_uid']), "LIMIT 1");
        return $uid;
    }
    $db->update("oauth_users", array("enabled" => 0), array("oauth_provider" => "facebook", "oauth_uid" => $fb_userdata['oauth_uid']), "LIMIT 1");

    $error['MSG'] = 'L_SM_OAUTH_USERNAME';
    do_action("message_page", $error);

    return false;
}

function SMB_oauth_checkif_register($fb, $oauth_id) {
    global $db, $sm;

    $query = $db->select_all("oauth_users", array("oauth_uid" => $oauth_id), "LIMIT 1");
    if (($row = $db->fetch($query))) {
        if (($real_account = $sm->getUserByID($row['uid']))) {
            return $real_account['uid'];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function SMB_oauth_getFB_data($fb, $token) {

    if (empty($token)) {
        return false;
    }
    if (($me = $fb->get('/me?fields=id,name,first_name,last_name,email,gender,name_format,picture.height(200),link,verified', $token))) {
        $oauth_user = $me->getGraphUser();
        return $oauth_user;
    } else {
        return false;
    }
}

function SMB_getFB_token($fb) {

    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        $error['MSG'] = 'L_SM_OAUTH_FB_ERROR_1';
        $error['XTRA_BOX_MSG'] = $e->getMessage();
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        $error['MSG'] = 'L_SM_OAUTH_FB_ERROR_2';
        $error['XTRA_BOX_MSG'] = $e->getMessage();
    }
    if (!empty($error)) {
        do_action("message_page", $error);
        return false;
    }

    if (!isset($accessToken)) {
        if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            $error['MSG'] = 'L_SM_OAUTH_OAUTH_ERROR';
            $error['XTRA_BOX_MSG'] = $helper->getError() . "\n" . $helper->getErrorCode() . "\n" .
                    $helper->getErrorReason() . "\n" . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            $error['MSG'] = 'L_SM_OAUTH_OAUTH_BADREQUEST';
        }
        do_action("message_page", $error);
        return false;
    }

    if (!$accessToken->isLongLived()) {
        try {
            $accessToken = $fb->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            $error['MSG'] = 'L_SM_OAUTH_FB_ERROR_3';
            $error['XTRA_BOX_MSG'] = $helper->getMessage();
            do_action("message_page", $error);
            return false;
        }
    }

    return (string) $accessToken;
}

function SMB_oauth_getLoginURL() {
    global $cfg;

    $login_url = "";
    if ($cfg['smbasic_oauth_facebook']) {
        $login_url .= SMB_oauth_FB_LoginURL();
    }

    return $login_url ? $login_url : false;
}

function SMB_oauth_FB_LoginURL() {
    global $cfg, $LNG;

    $fb = SMB_oauth_getFB_Handle();
    $reg_url = $cfg['WEB_URL'] . "login?provider=facebook";
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email'];
    $loginUrl = $helper->getLoginUrl($reg_url, $permissions);

    return '<a class="fblog" href="' . htmlspecialchars($loginUrl) . '">' . $LNG['L_SM_LOG_FB'] . '</a>';
}

function SMB_oauth_getFB_Handle() {
    global $cfg;

    require_once 'Facebook/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id' => $cfg['smbasic_fb_appid'],
        'app_secret' => $cfg['smbasic_fb_appSecret'],
        'default_graph_version' => 'v2.8',
    ]);

    return $fb;
}

<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function SMBasic_encrypt_password($password) {
    global $config;

    if ($config['smbasic_use_salt']) {
        return hash('sha512', md5($password . $config['smbasic_pw_salt']));
    } else {
        return hash('sha512', $password);
    }
}

function SMBasic_sessionDebugDetails() {
    global $db, $sm;

    if (!($user = $sm->getSessionUser())) {
        return false;
    }
    print_debug("<hr><br/><h2>Session Details</h2>", "SM_DEBUG");
    print_debug("Time Now: " . format_date(time(), true) . "", "SM_DEBUG");
    if (isset($_SESSION)) {
        if (!empty($sm->getData("uid"))) {
            print_debug("Session VAR ID:" . $sm->getData("uid"), "SM_DEBUG");
        }
    } else {
        print_debug("Session ins't set", "SM_DEBUG");
    }

    $query = $db->select_all("sessions", array("session_uid" => "{$user['uid']}"), "LIMIT 1");
    $session = $db->fetch($query);
    if ($session) {
        print_debug("Session DB IP: {$session['session_ip']}", "SM_DEBUG");
        print_debug("Session DB Browser: {$session['session_browser']}", "SM_DEBUG");
        print_debug("Session DB Create: {$session['session_created']}", "SM_DEBUG");
        print_debug("Session DB Expire:" . format_date("{$session['session_expire']}", true) . "", "SM_DEBUG");
        print_debug("Session DB Admin: {$session['session_admin']} ", "SM_DEBUG");
    }
    print_debug("PHP Session expire: " . ini_get('session.gc_maxlifetime'), "SM_DEBUG");
    print_debug("Cookies State:", "SM_DEBUG");
    if (isset($_COOKIE)) {
        print_debug(" is set", "SM_DEBUG");
        print_debug("Cookie Array:", "SM_DEBUG");
        foreach ($_COOKIE as $key => $val) {
            print_debug("Cookie $key -> $val", "SM_DEBUG");
        }
        print_debug("<hr>", "SM_DEBUG");
    } else {
        print_debug(" not set", "SM_DEBUG");
    }
}

function SMBasic_create_reg_mail($active) {
    global $LANGDATA, $config;

    if ($active > 1) {
        if ($config['FRIENDLY_URL']) {
            $URL = $config['WEB_URL'] . "login&active=$active";
        } else {
            $URL = $config['CON_FILE'] . "?module=SMBasic&page=login&active=$active";
        }
        $msg = $LANGDATA['L_REG_EMAIL_MSG_ACTIVE'] . "\n" . "$URL";
    } else {
        if ($config['FRIENDLY_URL']) {
            $URL = $config['WEB_URL'] . "login";
        } else {
            $URL = $config['CON_FILE'] . "?module=SMBasic&page=login";
        }
        $msg = $LANGDATA['L_REG_EMAIL_MSG_WELCOME'] . "\n" . "$URL";
    }
    return $msg;
}

function SMBasic_navLogReg() {
    global $config, $LANGDATA, $sm;

    $user = $sm->getSessionUser();

    $elements = "";
    if ($config['FRIENDLY_URL']) {
        $login_url = "/{$config['WEB_LANG']}/login";
        $register_url = "/{$config['WEB_LANG']}/register";
        $profile_url = "/{$config['WEB_LANG']}/profile";
        $logout_url = "/{$config['WEB_LANG']}/logout";
    } else {
        $login_url = "/{$config['CON_FILE']}?module=SMBasic&page=login&lang={$config['WEB_LANG']}";
        $register_url = "/{$config['CON_FILE']}?module=SMBasic&page=register&lang={$config['WEB_LANG']}";
        $profile_url = "/{$config['CON_FILE']}?module=SMBasic&page=profile&lang={$config['WEB_LANG']}'";
        $logout_url = "/{$config['CON_FILE']}?module=SMBasic&page=logout&lang={$config['WEB_LANG']}";
    }

    if ($user) {
        $elements .= "<li class='nav_right'><a href='$logout_url'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='$profile_url'>" . $user['username'] . "</a></li>\n";
        $elements .= "<li class='nav_right zero'><a href='$profile_url'><img src=" . $user['avatar'] . " /></a></li>";
    } else {
        $elements .= "<li class='nav_right'><a href='$login_url'>{$LANGDATA['L_LOGIN']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='$register_url'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

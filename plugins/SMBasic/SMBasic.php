 <?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * 
 * do_action("encrypt_password") // Override/set for change default one
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_Init() {
    global $config, $sm;
 
    print_debug("SMBasic initialice", "PLUGIN_LOAD");

    includePluginFiles("SMBasic");

    $sm = new SessionManager;

    if (action_isset("encrypt_password") == false) {
        register_uniq_action("encrypt_password", "SMBasic_encrypt_password");
    }

    if ( (S_SESSION_INT("uid") != false && S_SESSION_CHAR_AZNUM("sid") != false) ) {
        if(!SMBasic_checkSession()) {
            print_debug("Check session failed on SMBasic_Init destroy session", "SM_DEBUG");
            $sm->sessionDestroy();
        }
    } else {
        if($config['smbasic_session_persistence']) {
            print_debug("Checkcookies trigged", "SM_DEBUG");
            $sm->checkCookies();
        }
    }
    if( defined('SM_DEBUG') && !empty($_SESSION['isLogged'])) {
        SMBasic_sessionDebugDetails();
    }
    register_action("nav_element", "SMBasic_navLogReg");
}

function SMBasic_navLogReg() {
    global $config, $LANGDATA;

    $elements = "";
    if($config['FRIENDLY_URL']) {
        $login_url = "/{$config['WEB_LANG']}/login";
        $register_url = "/{$config['WEB_LANG']}/register";
        $profile_url = "/{$config['WEB_LANG']}/profile";
        $logout_url  = "/{$config['WEB_LANG']}/logout";        
    } else {
        $login_url = "/app.php?module=SMBasic&page=login&lang={$config['WEB_LANG']}";
        $register_url = "/app.php?module=SMBasic&page=register&lang={$config['WEB_LANG']}";
        $profile_url = "/app.php?module=SMBasic&page=profile&lang={$config['WEB_LANG']}'";
        $logout_url  = "/app.php?module=SMBasic&page=logout&lang={$config['WEB_LANG']}";
    }
    if( S_SESSION_INT("isLogged") == 1) {
        $elements .= "<li class='nav_right'><a href='$logout_url'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='$profile_url'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='$login_url'>{$LANGDATA['L_LOGIN']}</a></li>\n";
       $elements .= "<li class='nav_right'><a href='$register_url'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

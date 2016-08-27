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

    !isset($sm) ? $sm = new SessionManager : false;

    if (action_isset("encrypt_password") == false) {
        register_uniq_action("encrypt_password", "SMBasic_encrypt_password");
    }

    if ((S_SESSION_INT("uid") != false && S_SESSION_CHAR_AZNUM("sid") != false)) {
        if (!($sm->checkSession())) {
            print_debug("Check session failed on SMBasic_Init destroy session", "SM_DEBUG");
            $sm->destroy();
        }
    } else {
        if ($config['smbasic_session_persistence']) {
            print_debug("Checkcookies trigged", "SM_DEBUG");
            $sm->checkCookies();
        }
    }
    if (defined('SM_DEBUG') && !empty($_SESSION['isLogged'])) {
        SMBasic_sessionDebugDetails();
    }
    register_action("nav_element", "SMBasic_navLogReg");
}

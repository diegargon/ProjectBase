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
    register_uniq_action("login_page", "SMBasic_loginPage");
    register_uniq_action("register_page", "SMBasic_regPage");   
    register_uniq_action("logout_page", "SMBasic_logoutPage");
    register_uniq_action("profile_page", "SMBasic_profilePage");
}

function SMBasic_regPage() {
    global $config, $tpl;

    require_once("includes/SMBasic.register.php");
    
    if( S_SESSION_INT("isLogged") == 1) {
        $msgbox['MSG'] = "L_ERROR_ALREADY_LOGGED";
        do_action("message_page", $msgbox);
        return false;
    }
    
    if ((($config['smbasic_need_email'] == 1) && !isset($_POST['email'])  ||
        ($config['smbasic_need_username'] == 1) && !isset($_POST['username'])) &&
        !isset($_POST['password']) &&
        !isset($_POST['register'])
        ) {
        do_action("common_web_structure");       
        $tpl->getCSS_filePath("SMBasic");
        $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
        $tpl->addto_tplvar("SCRIPTS", SMBasic_RegisterScript());
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "register"));                                
    } else {
        SMBasic_Register();   
    }
}

function SMBasic_profilePage() {
    global $tpl, $sm;    
    require_once("includes/SMBasic.profile.php");
    
    if( S_SESSION_INT("isLogged") != 1) {
        $msgbox['MSG'] = "L_ERROR_NOT_LOGGED";
        do_action("message_page", $msgbox);
        return false;
    }

    if(isset($_POST['profile']) ) {          
        SMBasic_ProfileChange();       
    } else if (isset($_GET['viewprofile'])) {
        SMBasic_ViewProfile();
    } else {
        if( ($user = $sm->getSessionUser()) == false) {
            $sm->sessionDestroy();
            $msgbox['MSG'] = "L_SM_E_USER_NOT_EXISTS";
            do_action("message_page", $msgbox);
        } else {        
            do_action("common_web_structure");       
            $tpl->getCSS_filePath("SMBasic");
            $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");
            $tpl->addto_tplvar("SCRIPTS", SMBasic_ProfileScript()); 
            $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "profile", $user));
        }    
    }
}

function SMBasic_loginPage () {
    global $tpl;
    require_once("includes/SMBasic.login.php");
    
    if( S_SESSION_INT("isLogged") == 1) {
        $msgbox['MSG'] = "L_ERROR_ALREADY_LOGGED";
        do_action("message_page", $msgbox);
        return false;
    }    
    if (isset($_GET['active'])) {
       if(!SMBasic_user_activate_account()) {
           $msgbox['MSG'] = "L_SM_E_ACTIVATION";
           do_action("message_page", $msgbox);
       }       
    }
    if (isset($_GET['reset'])) {
       if(!SMBasic_user_reset_password()) {
            $msgbox['MSG'] = "L_SM_E_ACTIVATION";
            do_action("message_page", $msgbox);
       }
       
    }
    if ( isset($_POST['email']) && isset($_POST['password']) && isset($_POST['login']) ) {
        SMBasic_Login(); 
    } else if ( !empty($_POST['reset_password_chk']) ) {  
        SMBasic_RequestResetOrActivation();
    } else {        
        do_action("common_web_structure");       
        $tpl->getCSS_filePath("SMBasic");
        $tpl->getCSS_filePath("SMBasic", "SMBasic-mobile");             
        $tpl->addto_tplvar("SCRIPTS", SMBasic_LoginScript()); 
        $tpl->addto_tplvar("POST_ACTION_ADD_TO_BODY", $tpl->getTPL_file("SMBasic", "login"));       
    }
}

function SMBasic_navLogReg() {
    global $config, $LANGDATA;
    
    $elements = "";    
    if( S_SESSION_INT("isLogged") == 1) {
        $elements .= "<li class='nav_right'><a href='/logout.php?lang={$config['WEB_LANG']}'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='/profile.php?lang={$config['WEB_LANG']}'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='/login.php?lang={$config['WEB_LANG']}'>{$LANGDATA['L_LOGIN']}</a></li>\n";
       $elements .= "<li class='nav_right'><a href='/register.php?lang={$config['WEB_LANG']}'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

function SMBasic_logoutPage() {
    global $sm;
    $sm->sessionDestroy();
    header('Location: ./');
}

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
    if( defined('SM_DEBUG') && !empty($_SESSION['isLogged'])) { // && $_SESSION['isLogged'] == 1 ) {
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
        do_action("error_message_page", "L_ERROR_ALREADY_LOGGED");
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
        do_action("error_message_page","L_ERROR_NOT_LOGGED");
        return false;
    }

    if(isset($_POST['profile']) ) {          
        SMBasic_ProfileChange();       
    } else if (isset($_GET['viewprofile'])) {
        SMBasic_ViewProfile();
    } else {
        if( ($user = $sm->getSessionUser()) == false) {
            $sm->sessionDestroy();
            do_action("error_message_page", "L_SM_E_USER_NOT_EXISTS");   
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
        do_action("error_message_page","L_ERROR_ALREADY_LOGGED");
        return false;
    }    
    if (isset($_GET['active'])) {
       SMBasic_user_activate_account();
       //TODO error msg on return false; and confirmation on true
    }
    if (isset($_GET['reset'])) {
       SMBasic_user_reset_account();
       //TODO error msg on return false;
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
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/logout.php'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/profile.php'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/login.php'>{$LANGDATA['L_LOGIN']}</a></li>\n";
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/register.php'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

function SMBasic_logoutPage() {
    global $sm;
    $sm->sessionDestroy();
    header('Location: ./');
}

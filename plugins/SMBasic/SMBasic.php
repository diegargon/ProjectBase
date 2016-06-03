<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 * 
 * do_action("encrypt_password") // Override/set for change default one
 */
if (!defined('IN_WEB')) { exit; }

function SMBasic_Init() {
    global $config;   
    
    if (DEBUG_PLUGINS_LOAD) { print_debug("SMBasic initialice<br/>"); }

    includePluginFiles("SMBasic");

    if (action_isset("encrypt_password") == false) {
        register_uniq_action("encrypt_password", "SMBasic_encrypt_password");
    }

    session_start();
    
    if (
         (!empty($_SESSION['uid']) && !empty($_SESSION['sid'])) &&
          ($_SESSION['uid'] != 0) // use 0 for anon? if not remove
       )
        {
            if(!SMBasic_checkSession()) {
                if(SM_DEBUG) { print_debug("Check session failed on SMBasic_Init destroy session"); }
                SMBasic_sessionDestroy();
            }           
        } else {
           if($config['smbasic_session_persistence']) {
              if (SM_DEBUG) { print_debug("Checkcookies trigged");  }
              SMBasic_checkCookies();
            }
        }
    if(SM_DEBUG && !empty($_SESSION['isLogged']) && $_SESSION['isLogged'] == 1) {
        SMBasic_sessionDebugDetails();
    }

    register_action("nav_element", "SMBasic_navLogReg");
    register_uniq_action("login_page", "SMBasic_loginPage");
    register_uniq_action("register_page", "SMBasic_regPage");   
    register_uniq_action("logout_page", "SMBasic_logoutPage");
    register_uniq_action("profile_page", "SMBasic_profilePage");
}

function SMBasic_regPage() {
    global $config;

    require_once("includes/SMBasic.register.php");
    
    if( (!empty($_SESSION['isLogged'])) && ($_SESSION['isLogged'] == 1)) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_ALREADY_LOGGED'];                
        do_action("error_message_page");
        return false;
    }
    
    if ((($config['smbasic_need_email'] == 1) && !isset($_POST['email'])  ||
        ($config['smbasic_need_username'] == 1) && !isset($_POST['username'])) &&
        !isset($_POST['password']) &&
        !isset($_POST['register'])
        ) {
        do_action("common_web_structure");       
        getCSS_filePath("SMBasic");
        getCSS_filePath("SMBasic", "SMBasic-mobile");
        addto_tplvar("SCRIPTS", SMBasic_RegisterScript());
        addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("SMBasic", "register"));                                
    } else {
        SMBasic_Register();   
    }
}

function SMBasic_profilePage() {    
    require_once("includes/SMBasic.profile.php");
    
    if(empty($_SESSION['isLogged']) || $_SESSION['isLogged'] != 1) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_NOT_LOGGED'];
        do_action("error_message_page");
        return false;
    }

    if(isset($_POST['profile']) ) {          
        SMBasic_ProfileChange();       
    } 
    if (!isset($_POST['profile']) ) {
        if( ($user = SMBasic_getUserbyID($_SESSION['uid'])) == false ) {
            //TODO error manager
            echo "Error: 3242";
            exit(0);        
        } else {        
            do_action("common_web_structure");       
            getCSS_filePath("SMBasic");
            getCSS_filePath("SMBasic", "SMBasic-mobile");
            addto_tplvar("SCRIPTS", SMBasic_ProfileScript()); 
            addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("SMBasic", "profile", $user));
        }    
    }
}

function SMBasic_loginPage () {   
    require_once("includes/SMBasic.login.php");
    
    if( (!empty($_SESSION['isLogged'])) && ($_SESSION['isLogged'] == 1)) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_ALREADY_LOGGED'];        
        do_action("error_message_page");
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
        getCSS_filePath("SMBasic");
        getCSS_filePath("SMBasic", "SMBasic-mobile");             
        addto_tplvar("SCRIPTS", SMBasic_LoginScript()); 
        addto_tplvar("POST_ACTION_ADD_TO_BODY", getTPL_file("SMBasic", "login"));       
    }
}

function SMBasic_navLogReg() {
    global $config, $LANGDATA;
    
    $elements = "";
    if (!empty($_SESSION['username']) && !empty($_SESSION['uid'])) { //&& $_SESSION['username'] != "anonimo") { 
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/logout.php'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/profile.php'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/login.php'>{$LANGDATA['L_LOGIN']}</a></li>\n";
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/register.php'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}

function SMBasic_logoutPage() {
    SMBasic_sessionDestroy();
    header('Location: ./');
}

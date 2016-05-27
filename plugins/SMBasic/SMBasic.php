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

    register_action("add_nav_element", "SMBasic_navLogReg", "5");       
    register_uniq_action("login_page", "SMBasic_loginPage");
    register_uniq_action("register_page", "SMBasic_regPage");   
    register_uniq_action("logout_page", "SMBasic_logoutPage");
    register_uniq_action("profile_page", "SMBasic_profilePage");
}

function SMBasic_regPage() {
    global $config;

    if( (!empty($_SESSION['isLogged'])) && ($_SESSION['isLogged'] == 1)) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_ALREADY_LOGGED'];                
        do_action("error_message_page");
        return false;
    }
    
    if ( 
            (($config['smbasic_need_email'] == 1) && !isset($_POST['email1'])  ||
            ($config['smbasic_need_username'] == 1) && !isset($_POST['username1'])) &&
            !isset($_POST['password1']) &&
            !isset($_POST['register1'])
                    ) {
        do_action("common_web_structure"); 
        tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic");
        tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic", "SMBasic-mobile");       
        tpl_addto_var("SCRIPTS", "SMBasic_RegisterScript");
        tpl_addto_var("POST_ACTION_ADD_TO_BODY", "tpl_get_file", "tpl", "SMBasic", "register");
//        register_action("add_link", "SMBasic_CSS","5");
//        register_action("add_to_body", "SMBasic_get_register_page", "5");  
//        register_action("add_script", "SMBasic_RegisterScript", "5");                                 
    } else {
        SMBasic_Register();   
    }
}

function SMBasic_profilePage() {
    if(empty($_SESSION['isLogged']) || $_SESSION['isLogged'] != 1) {
        $GLOBALS['tpldata']['E_MSG'] = $GLOBALS['LANGDATA']['L_ERROR_NOT_LOGGED'];
        do_action("error_message_page");
        return false;
    }
    
    if(isset($_POST['profile1']) ) {          
        SMBasic_ProfileChange();       
    } 
    if (!isset($_POST['profile1']) ) {
        if( ($user = SMBasic_getUserbyID($_SESSION['uid'])) == false ) {
            //TODO error manager
            echo "Error: 3242";
            exit(0);        
        } else {        
            do_action("common_web_structure"); 
            tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic");
            tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic", "SMBasic-mobile");       
            tpl_addto_var("SCRIPTS", "SMBasic_ProfileScript");
            tpl_addto_var("POST_ACTION_ADD_TO_BODY", "tpl_get_file", "tpl", "SMBasic", "profile", $user);
//        register_action("add_link", "SMBasic_CSS","5");
//        register_action("add_to_body", "SMBasic_profile_page", "5"); 
//        register_action("add_script", "SMBasic_ProfileScript", "5");           
        }    
    }
}

function SMBasic_loginPage () {
    
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
    if (
        isset($_POST['email1']) && 
        isset($_POST['password1']) &&
        isset($_POST['login1'])
    ) {
        SMBasic_Login(); 
    } else if (!empty($_POST['reset1'])){  
        SMBasic_RequestResetOrActivation();
    } else {        
       do_action("common_web_structure");
       tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic");
       tpl_addto_var("LINK", "tpl_get_file", "css", "SMBasic", "SMBasic-mobile");       
       tpl_addto_var("SCRIPTS", "SMBasic_LoginScript");
       tpl_addto_var("POST_ACTION_ADD_TO_BODY", "tpl_get_file", "tpl", "SMBasic", "login");       
       //register_action("add_link", "SMBasic_CSS","5");
       //register_action("add_to_body", "SMBasic_get_login_page", "5");
       //register_action("add_script", "SMBasic_LoginScript", "5");          
    }
}

function SMBasic_navLogReg() {
    global $config;
    global $LANGDATA;
    
    $elements = "";
    if (!empty($_SESSION['username']) && !empty($_SESSION['uid'])) { //&& $_SESSION['username'] != "anononimo") { 
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/logout.php'>{$LANGDATA['L_LOGOUT']}</a></li>\n";
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/profile.php'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/login.php'>{$LANGDATA['L_LOGIN']}</a></li>\n";
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/register.php'>{$LANGDATA['L_REGISTER']}</a></li>\n";
    }
    return $elements;
}
/* NOW UNUSED
function SMBasic_get_login_page() {
    return tpl_get_file("tpl", "SMBasic", "login");
}


function SMBasic_get_register_page() {
    return tpl_get_file("tpl", "SMBasic", "register");
}


function SMBasic_profile_page() {
    global $config;
    //TODO: ACL/ ONLY REGISTER USERS 
    if( ($user = SMBasic_getUserbyID($_SESSION['uid'])) == false ) {
        //TODO error manager
        echo "Error: 3242";
        exit(0);        
    } else {
       return tpl_get_file("tpl", "SMBasic", "profile");
    }
}

function SMBasic_CSS() {
    $link  = "";
    $link .= tpl_get_file("css", "SMBasic");
    $link .= tpl_get_file("css", "SMBasic", "SMBasic-mobile");

    return $link;
}
*/

function SMBasic_LoginScript() {
    $script = "";
    
    if (!check_jsScript("jquery.min.js")) 
    {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }      
    $script .= "<script type=\"text/javascript\" src=\"plugins/SMBasic/js/login.js\"></script>\n";
           
    return $script;
}

function SMBasic_RegisterScript() {
    $script = "";
    
    if (!check_jsScript("jquery.min.js")) 
    {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }           
    $script .= "<script type=\"text/javascript\" src=\"plugins/SMBasic/js/register.js\"></script>\n";
    
    return $script;
}

function SMBasic_ProfileScript() {
    $script = "";
    if (!check_jsScript("jquery.min.js")) 
    {
        global $external_scripts;
        $external_scripts[] = "jquery.min.js";
        $script .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    }           
    $script .= "<script type=\"text/javascript\" src=\"plugins/SMBasic/js/profile.js\"></script>\n";
    
    return $script;
}

function SMBasic_logoutPage() {
    SMBasic_sessionDestroy();
    header('Location: ./');
}

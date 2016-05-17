<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

/*
 * do_action("encrypt_password") // Override/set for change default one
 */

function SMBasic_Init() {
    print_debug("SMBasic initialice<br/>");
    require_once("includes/SMBasic.inc.php");
    require_once("SMBasic.config.php");
    
    global $config;
    session_start();

    if (
         (isset($_SESSION['uid']) && isset($_SESSION['sid'])) &&
          ($_SESSION['uid'] != 0)
        ){
        if(SMBasic_checkSession() == false) {
            SMBasic_unset_session();

        }
    } else {
       if($config['smbasic_session_persistence']) {
           SMBasic_checkCookies();
       }    
    }
    
    register_action("add_nav_element", "SMBasic_navLogReg", "5");
    
    register_uniq_action("login_page", "SMBasic_loginPage");
    register_uniq_action("register_page", "SMBasic_regPage");   
    register_uniq_action("logout_page", "SMBasic_logoutPage");
    register_uniq_action("profile_page", "SMBasic_profilePage");
}


function SMBasic_regPage() {
    do_action("common_web_structure");    
    register_action("add_link", "SMBasic_CSS","5");
    register_action("add_to_body", "SMBasic_get_register_page", "5");  
}

function SMBasic_profilePage() {
    do_action("common_web_structure");    
    register_action("add_link", "SMBasic_CSS","5");
    register_action("add_to_body", "SMBasic_profile_page", "5");  
}

function SMBasic_loginPage () {

    if (isset($_POST['email1']) && isset($_POST['password1'])) {
            SMBasic_Login();        
    } else {
       do_action("common_web_structure");
       register_action("add_link", "SMBasic_CSS","5");
       register_action("add_to_body", "SMBasic_get_login_page", "5");
       register_action("add_script", "SMBasic_Script", "5");   
       
    }
}

function SMBasic_navLogReg() {
    global $config;
    $elements = "";
    if (isset($_SESSION['username']) && $_SESSION['username'] != "anononimo") {
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/logout.php'>Logout</a></li>\n";
        $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/profile.php'>". $_SESSION['username']. "</a></li>\n";
    } else {
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/login.php'>Login</a></li>\n";
       $elements .= "<li class='nav_right'><a href='/{$config['WEB_LANG']}/register.php'>Register</a></li>\n";
    }
    return $elements;
}
function SMBasic_get_login_page() {

    if ($TPLPATH = tpl_get_path("tpl", "SMBasic", "login")) {
        return codetovar($TPLPATH, "");
    }   
}

function SMBasic_get_register_page() {
    if ($TPLPATH = tpl_get_path("tpl", "SMBasic", "register")) {
        return codetovar($TPLPATH, "");
    }   
}

function SMBasic_profile_page() {
    if ($TPLPATH = tpl_get_path("tpl", "SMBasic", "profile")) {
        return codetovar($TPLPATH, "");
    }   
}


function SMBasic_CSS() {
    if($CSSPATH = tpl_get_path("css", "SMBasic", "")) {
        $link = "<link rel='stylesheet' href='$CSSPATH'>\n";
    }
    if($CSSPATH = tpl_get_path("css", "SMBasic", "SMBasic-mobile")) {
        $link .= "<link rel='stylesheet' href='$CSSPATH'>\n";
    }    
    return $link;
}


function SMBasic_Script() {
    //TODO: Plugin for provided common scripts "need_jquery() and asured its included only one time if its called from other modules";
    $script = "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    $script .= "<script type=\"text/javascript\" src=\"plugins/SMBasic/js/login.js\"></script>\n";
           
    
    return $script;
}

function SMBasic_logoutPage() {
    SMBasic_sessionDestroy();
    header('Location: ./');
}

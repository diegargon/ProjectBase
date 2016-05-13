<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

/*
 * do_action("encrypt_password") // Override/set for change default one
 */

function SMBasic_Init() {
    print_debug("SMBasic initialice<br/>");
    require("includes/SMBasic.inc.php");
    require("SMBasic.config.php");
    
    SMBasic_Start();
    
    register_action("add_nav_element", "SMBasic_navLogReg", "5");
    
    register_uniq_action("login_page", "SMBasic_loginPage");
    register_uniq_action("register_page", "SMBasic_regPage");   
    
}


function SMBasic_Start () {
    session_start();
    
    $session_id = session_id();
    require_once("SMBasic.config.php");
    //session_regenerate_id();
    //echo "$session_id";
    
}

function SMBasic_regPage() {
    do_action("common_web_structure");    
    register_action("add_link", "SMBasic_CSS","5");
    register_action("add_to_body", "get_register_page", "5");  
}
function SMBasic_loginPage () {

    if (isset($_POST['email1']) && isset($_POST['password1'])) {
            SMBasic_checkLogin();        
    } else {
       do_action("common_web_structure");
       register_action("add_link", "SMBasic_CSS","5");
       register_action("add_to_body", "get_login_page", "5");
       register_action("add_script", "SMBasic_Script", "5");   
       
    }
}

function SMBasic_checkLogin() {
    global $config;
    
    if ( 
        (($email = s_char($_POST['email1'], $config['smbasic_max_email'])) != false) && 
        (($password = s_char($_POST['password1'], $config['smbasic_max_password']))!= false))
    {
        if(action_isset("encrypt_password") == false) {           
           $password = SMBasic_encrypt_password($password);
        } else {
            $password = do_action("encrypt_password");
        }
        if(!isset($password)) {
            print "Internal Error password mechanism";
            exit(0);
        }
       $response = [];
       
        $q = "SELECT * FROM " . $config['DB_PREFIX'] . "users WHERE email = '$email' AND password = '$password'";
       
        $query = db_query($q);
        if ($result = db_fetch($query)) {
            $response[] = array("status" => "ok", "msg" => $config['WEBURL']);
        } else {
            $response[] = array("status" => "error", "msg" => "Email o contraseña incorrectos");
        }
        db_free_result($query);
    } else {
            $response[] = array("status" => "error", "msg" => "Email o contraseña incorrectos");
    }
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
}
function SMBasic_navLogReg() {
    $elements = "<li class=\"nav_right\"><a href=\"profile.php\">Anonimo</a></li>\n";
    $elements .= "<li class=\"nav_right\"><a href=\"login.php\">Login</a></li>\n";
    $elements .= "<li class=\"nav_right\"><a href=\"register.php\">Register</a></li>\n";
    return $elements;
}
function get_login_page() {

    if ($TPLPATH = tpl_get_path("tpl", "SMBasic", "login")) {
        return codetovar($TPLPATH, "");
    }   
}

function get_register_page() {
    if ($TPLPATH = tpl_get_path("tpl", "SMBasic", "register")) {
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
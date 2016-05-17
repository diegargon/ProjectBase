<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */


function Multilang_init(){
    global $config;
    print_debug("Multilang Inititated<br/>");
    
    $config['multilang'] = 1;

    $request_uri = $_SERVER['REQUEST_URI'];

    if (
       (isset($_GET['lang'])) &&
       (($lang = s_char($_GET['lang'], 2)) != false)
        ) {
            $config['WEB_URL'] = $config['WEB_URL'] . "$lang/";
            $config['WEB_LANG'] = $lang;
    } else {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);   
        if(isset($lang)) {
            $config['WEB_LANG'] = $lang;
        }
    }
    
    if ($request_uri == '/') {
        $request_uri = $config['WEB_URL'] . $config['WEB_LANG'] . $request_uri;        
        header('Location:' .$request_uri);
    }
   
    register_action("add_nav_element", "ML_nav", 6);
    
}

function ML_nav() {
    //TODO automatic  * better
    global $config;
    
    $mlnav = "<li class='nav_right'>"
    . "<form action='' method='post'>"
    . "<select>";
    if ($config['WEB_LANG'] == "es") {
        $mlnav .= "<option selected value='es'>Español</option>";
    } else {
        $mlnav .= "<option value='es'>Español</option>";
    }
    if ($config['WEB_LANG'] == "en") {
        $mlnav .= "<option selected value='en' >Ingles</option>";
    } else {
        $mlnav .= "<option value='en'>Ingles</option>";
    }


    $mlnav .= "</select>"
           . "</form>"
           . "</li>";
    return $mlnav;
}
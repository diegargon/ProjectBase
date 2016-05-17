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

    if( //TODO: check lang agains pb_lang
            isset($_POST['choose_lang']) &&
            (($choose_lang = s_char($_POST['choose_lang'], 3)) != false)
            ) {
            $request_uri = str_replace($config['WEB_LANG'], $choose_lang, $request_uri);
            header('Location:' .$request_uri);
    }

    register_action("add_nav_element", "ML_nav", 6);
    register_action("add_script", "ML_Script", 5);   
}

function ML_Script() {
    //TODO: Plugin for provided common scripts "need_jquery() and asured its included only one time if its called from other modules";
    //TODO: jquery ATM its duplicated on login.
    $script = "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js\"></script>\n";
    $script .= "<script type='text/javascript'>\n"
            . "jQuery(function() {\n"
            . "jQuery('#choose_lang').change(function() {\n"
            . "this.form.submit();\n"
            . "});\n"
            . "});\n"
            . "</script>\n";
           
    
    return $script;
}
function ML_nav() {
    //TODO automatic  * better
    global $config;
    
    $mlnav = "<li class='nav_right'>"
    . "<form action='' method='post'>"
    . "<select name='choose_lang' id='choose_lang'>";
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
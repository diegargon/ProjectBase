<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function tpl_build_page() {
    global $tpldata, $config;
    
    !isset($tpldata['ADD_TO_FOOTER']) ? $tpldata['ADD_TO_FOOTER'] = "" : false;
    !isset($tpldata['ADD_TO_BODY']) ? $tpldata['ADD_TO_BODY'] = "" : false;
    
    // BEGIN HEAD
 
    $web_head = do_action("get_head"); 
    //END HEAD
    
    //BEGIN BODY
    if ($config['NAV_MENU']) { //use do_action for order 
        !isset($tpldata['NAV_ELEMENT']) ? $tpldata['NAV_ELEMENT'] = "" : false;
        $tpldata['NAV_ELEMENT'] .= do_action("nav_element");       
    }   
    
    $tpldata['ADD_TO_BODY'] .= do_action("add_to_body");
    $web_body = do_action("get_body");
    //END BODY
    
    //BEGIN FOOTER 
    $tpldata['ADD_TO_FOOTER'] .= do_action("add_to_footer");
    $web_footer = do_action("get_footer");
    //END FOOTER
        
    echo $web_head . $web_body . $web_footer;
}

function getTPL_file($plugin, $filename = null, $data = null) {
    global $config;
    $tpl = "";
    if(empty($filename)) {
        $filename = $plugin;
    }        
    if(TPL_DEBUG) { print_debug("getTPL_file called by-> $plugin for get a $filename"); }

    $USER_PATH = "tpl/{$config['THEME']}/$filename.tpl.php";
    $DEFAULT_PATH = "plugins/$plugin/tpl/$filename.tpl.php";
    if (file_exists($USER_PATH)) {
        $tpl = codetovar($USER_PATH, $data);
    } else if (file_exists($DEFAULT_PATH)) {
        $tpl = codetovar($DEFAULT_PATH, $data);
    } 
    
    return $tpl;
}

function getCSS_filePath($plugin, $filename = null) {
    global $config;
    
    if(empty($filename)) {
        $filename = $plugin;
    }    
    if(TPL_DEBUG) { print_debug("Get CSS called by-> $plugin for get a $filename"); }

    $USER_PATH = "tpl/{$config['THEME']}/css/$filename.css";
    $DEFAULT_PATH = "plugins/$plugin/tpl/css/$filename.css";        
    if (file_exists($USER_PATH))  {
        $css = "<link rel='stylesheet' href='/$USER_PATH'>\n";
    } else if (file_exists($DEFAULT_PATH)) {
        $css =  "<link rel='stylesheet' href='/$DEFAULT_PATH'>\n";
    }          
    addto_tplvar("LINK", $css);
}

function addto_tplvar ($tplvar, $data, $priority = 5) {
    global $tpldata;
    if (!isset($tpldata[$tplvar])) {
        $tpldata[$tplvar] = $data;
    } else {
        $tpldata[$tplvar] .= $data;     
    }     
    
}
<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function tpl_build_page() {
    global $tpldata;
    
    !isset($tpldata['META']) ? $tpldata['META'] = "" : false;
    !isset($tpldata['LINK']) ? $tpldata['LINK'] = "" : false;
    !isset($tpldata['SCRIPTS']) ? $tpldata['SCRIPTS'] = "" : false;
    !isset($tpldata['NAV']) ? $tpldata['NAV'] = "" : false;   
    !isset($tpldata['NAV_ELEMENT']) ? $tpldata['NAV_ELEMENT'] = "" : false;
    !isset($tpldata['ADD_TO_FOOTER']) ? $tpldata['ADD_TO_FOOTER'] = "" : false;
    !isset($tpldata['ADD_TO_BODY']) ? $tpldata['ADD_TO_BODY'] = "" : false;
    
    // BEGIN HEAD
    $tpldata['META'] .= do_action("add_meta");  
    $tpldata['LINK'] .= do_action("add_link");  
    $tpldata['SCRIPTS'] .= do_action("add_script");  
    $web_head = do_action("get_head"); 
    //END HEAD
    
    //BEGIN BODY
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

    $USER_PATH = "tpl/$config[THEME]/$filename.tpl.php";
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

function addto_tplvar ($tplvar, $data) {
    global $tpldata;
    if (!isset($tpldata[$tplvar])) {
        $tpldata[$tplvar] = $data;
    } else {
        $tpldata[$tplvar] .= $data;            
    }     
    
}
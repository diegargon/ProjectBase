<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }
//PARA BORRAR
/*
function tpl_addto_var($tplvar, $func, $param1 = null, $param2 = null, $param3 = null, $param4 = null ) {
    global $tpldata;
    $return = call_user_func($func, $param1, $param2, $param3, $param4);
    if (!isset($tpldata[$tplvar])) {
        $tpldata[$tplvar] = $return;
    } else {
        $tpldata[$tplvar] .= $return;            
    }    
}
*/

function tpl_build_page() {
    global $tpldata;
    
    !isset($tpldata['META']) ? $tpldata['META'] = "" : false;
    !isset($tpldata['LINK']) ? $tpldata['LINK'] = "" : false;
    !isset($tpldata['SCRIPTS']) ? $tpldata['SCRIPTS'] = "" : false;
    !isset($tpldata['NAV']) ? $tpldata['NAV'] = "" : false;   
    !isset($tpldata['NAV_ELEMENT']) ? $tpldata['NAV_ELEMENT'] = "" : false;
    $tpldata['ADD_TO_FOOTER'] = "";
    $tpldata['ADD_TO_BODY'] = "";
    
    // BEGIN HEAD
    $tpldata['META'] .= do_action("add_meta");  
    $tpldata['LINK'] .= do_action("add_link");  
    $tpldata['SCRIPTS'] .= do_action("add_script");  
    $web_head = do_action("get_head");
    //END HEAD
    
    //BEGIN BODY
    
    $tpldata['NAV_ELEMENT'] .= do_action("add_nav_element");
    $tpldata['NAV'] .= do_action("add_nav");        
    $tpldata['ADD_TO_BODY'] .= do_action("add_to_body");
    $web_body = do_action("get_body");
    //END BODY
    
    //BEGIN FOOTER    
    $tpldata['ADD_TO_FOOTER'] .= do_action("add_to_footer");
    $web_footer = do_action("get_footer");
    //END FOOTER
        
    echo $web_head . $web_body . $web_footer;
}
/*
function tpl_get_file($type, $plugin, $page = null, $data = null) {
    global $config;
    if(empty($page)) {
        $page = $plugin;
    }
    if(TPL_DEBUG) { print_debug("tpl_get_file called by-> $plugin for get a $type:$page"); }
    if($type == "css") {
        $USER_PATH = "tpl/{$config['THEME']}/css/$page.css";
        $DEFAULT_PATH = "plugins/$plugin/tpl/css/$page.css";        
        if (file_exists($USER_PATH))  {
            return  "<link rel='stylesheet' href='/$USER_PATH'>\n";
        } else if (file_exists($DEFAULT_PATH)) {
            return  "<link rel='stylesheet' href='/$DEFAULT_PATH'>\n";
        } 
    }    
    
    if ($type == "tpl") {
        $USER_PATH = "tpl/$config[THEME]/$page.tpl.php";
        $DEFAULT_PATH = "plugins/$plugin/tpl/$page.tpl.php";
        if (file_exists($USER_PATH)) {
            return codetovar($USER_PATH, $data);
        } else if (file_exists($DEFAULT_PATH)) {
            return codetovar($DEFAULT_PATH, $data);
        }       
    }
}
 * 
 */
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
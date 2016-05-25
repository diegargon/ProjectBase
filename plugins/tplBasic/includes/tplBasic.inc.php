<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function tpl_build_page() {
    global $tpldata;
    $tpldata['META'] = ""; 
    $tpldata['LINK'] = "";
    $tpldata['SCRIPTS'] = "";
    $tpldata['NAV_ELEMENT'] = "";
    $tpldata['NAV'] = "";
    $tpldata['ADD_TO_BODY'] = "";
    $tpldata['ADD_TO_FOOTER'] = "";
    
    // BEGIN HEAD
    $tpldata['META'] .= do_action("add_meta");  
    $tpldata['LINK'] .= do_action("add_link");  
    $tpldata['SCRIPTS'] .= do_action("add_script");  
    echo do_action("get_head");
    //END HEAD
    
    //BEGIN BODY
    
    $tpldata['NAV_ELEMENT'] .= do_action("add_nav_element");
    $tpldata['NAV'] .= do_action("add_nav");        
    $tpldata['ADD_TO_BODY'] .= do_action("add_to_body");
    echo do_action("get_body");
    //END BODY
    
    //BEGIN FOOTER    
    $tpldata['ADD_TO_FOOTER'] .= do_action("add_to_footer");
    echo do_action("get_footer");
    //END FOOTER
    
}

function tpl_get_path($type, $plugin, $page) {
    global $config;
    if(empty($page)) {
        $page = $plugin;
    }
    if($type == "css") {
        $PATH = "tpl/$config[THEME]/css/$page.css";
        if (file_exists($PATH)) {
            return $PATH;
        }
        $PATH = "plugins/$plugin/tpl/css/$page.css";
        if (file_exists($PATH)) {
            return $PATH; 
        }
        return false;
    }
    
    if ($type == "tpl") {
        $PATH = "tpl/$config[THEME]/$page.tpl.php";
        if (file_exists($PATH)) {
            return $PATH;
        }
        $PATH = "plugins/$plugin/tpl/$page.tpl.php";
        
        if (file_exists($PATH)) {
            return $PATH;
        }
        return false;
    }
    
    return false;
}
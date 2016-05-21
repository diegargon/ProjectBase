<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
function DebugWindow_init() {
    if (DEBUG_PLUGINS_LOAD) { print_debug("Plugin: DebugWindow initiated<br/>"); }
    
    register_action("add_link", "add_dw_link","5");
    register_action("add_footer", "get_dw_tpl","5");
    
}

function add_dw_link() {

    if($CSSPATH = tpl_get_path("css", "DebugWindow", "")) {
        return "<link rel='stylesheet' href='$CSSPATH'>\n";
    }
}

function get_dw_tpl() {
    global $debug;
    if ($TPLPATH = tpl_get_path("tpl", "DebugWindow", "")) {
        return codetovar($TPLPATH, $debug);

    }       
}

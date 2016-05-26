<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function DebugWindow_init() {
    if (DEBUG_PLUGINS_LOAD) { print_debug("Plugin: DebugWindow initiated<br/>"); }
    
    register_action("add_link", "add_dw_link","5");
    register_action("add_to_footer", "get_dw_tpl","5");
    
}

function add_dw_link() {
    return  tpl_get_file("css", "DebugWindow", "");
}

function get_dw_tpl() {
    return tpl_get_file("tpl", "DebugWindow", "", $GLOBALS['debug']);   
}

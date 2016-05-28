<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function DebugWindow_init() {
    if (DEBUG_PLUGINS_LOAD) { print_debug("Plugin: DebugWindow initiated<br/>"); }
    
    getCSS_file("DebugWindow");
    register_action("add_to_footer", "get_dw_tpl","5");   
}

function get_dw_tpl() {
    return getTPL_file("DebugWindow", null, $GLOBALS['debug']);
}

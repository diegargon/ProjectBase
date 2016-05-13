<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */

function DebugWindow_init() {
    print_debug("DebugWindow initiated<br/>");
    register_action("add_link", "add_dw_link","5");
    register_action("add_footer", "get_dw_tpl","5");
    
}

function add_dw_link() {
    //global $tpldata;

    if($CSSPATH = tpl_get_path("css", "DebugWindow", "")) {
        //$tpldata['LINK'] .= "<link rel='stylesheet' href='$CSSPATH'>\n";
        return "<link rel='stylesheet' href='$CSSPATH'>\n";
    }
}

function get_dw_tpl() {
    if ($TPLPATH = tpl_get_path("tpl", "DebugWindow", "")) {
        return codetovar($TPLPATH, "");

    }       
}

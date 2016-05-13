<?php

/* 
 *  Copyright @ 2016 Diego Garcia
 */
global $tpldata;

function tplBasic_init(){
    
    global $tpldata;
    global $config;
    
    print_debug ("tplBasic initialized<br>");
    require_once("includes/tplBasic.inc.php");
/*
    $tpldata['TITLE'] = $config['TITLE'];
    $tpldata['WEBDESC'] = $config['WEBDESC'];
    $tpldata['CHARSET'] = $config['CHARSET'];
    $tpldata['WEBURL']  = $config['WEBURL'];
 * 
 */
    register_action("common_web_structure", "tplBasic_web_structure", "0");
    

//    echo "$tpldata[DEBUG]";
}

function tplBasic_web_structure() {
    register_uniq_action("get_head", "tpl_basic_head");
    register_uniq_action("get_body", "tpl_basic_body");
    register_uniq_action("get_footer", "tpl_basic_footer"); 
     
}
function tpl_basic_head() {
    if ($TPLPATH = tpl_get_path("tpl", "tplBasic", "basic_head")) {
        return codetovar($TPLPATH, "");
    }
}

function tpl_basic_body() {
    if ($TPLPATH = tpl_get_path("tpl", "tplBasic", "basic_body")) {
        return codetovar($TPLPATH, "");
    }
}

function tpl_basic_footer() {
    if ($TPLPATH = tpl_get_path("tpl", "tplBasic", "basic_footer")) {
        return codetovar($TPLPATH, "");
    }
}
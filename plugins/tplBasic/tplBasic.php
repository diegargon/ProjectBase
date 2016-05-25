<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $tpldata;

function tplBasic_init(){   
    if (DEBUG_PLUGINS_LOAD) { print_debug ("tplBasic initialized<br>"); }

    require_once("includes/tplBasic.inc.php");

    register_action("common_web_structure", "tplBasic_web_structure", "0");
    register_uniq_action("index_page", "tplBasic_index_page", "5");
    register_uniq_action("error_message", "tplBasic_error_page");
}

function tplBasic_web_structure() {
    register_uniq_action("get_head", "tpl_basic_head");
    register_uniq_action("get_body", "tpl_basic_body");
    register_uniq_action("get_footer", "tpl_basic_footer");     
}

function tplBasic_index_page() {
    do_action("common_web_structure");        
}

function tplBasic_error_page() {    

    register_action("add_to_body", "tpl_basic_error","5");
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

function tpl_basic_error() {
    if ($TPLPATH = tpl_get_path("tpl", "tplBasic", "basic_error")) {
        return codetovar($TPLPATH, "");
    }
}


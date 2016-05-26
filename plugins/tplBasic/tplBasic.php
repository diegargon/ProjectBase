<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

global $tpldata;

function tplBasic_init(){   
    if (DEBUG_PLUGINS_LOAD) { print_debug ("tplBasic initialized<br>"); }
    
    includePluginFiles("tplBasic");
    
    register_action("common_web_structure", "tplBasic_web_structure", "0");
    register_uniq_action("index_page", "tplBasic_index_page", "5");
    register_uniq_action("error_message_page", "tplBasic_error_page");
    register_uniq_action("error_message_box", "tplBasic_error_box");
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

 function tplBasic_error_box () {
    return tpl_basic_error();
 }

function tpl_basic_head() {
    return tpl_get_file("tpl", "tplBasic", "basic_head");
}

function tpl_basic_body() {
    return tpl_get_file("tpl", "tplBasic", "basic_body");
}

function tpl_basic_footer() {
    return tpl_get_file("tpl", "tplBasic", "basic_footer");
}

function tpl_basic_error() {
    return tpl_get_file("tpl", "tplBasic", "basic_error");
}
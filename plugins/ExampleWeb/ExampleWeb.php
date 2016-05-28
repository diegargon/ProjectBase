<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function ExampleWeb_init(){
        
    if (DEBUG_PLUGINS_LOAD) { print_debug ("ExampleWeb initialized<br>"); }
            
    includePluginFiles("ExampleWeb");
    
    register_uniq_action("index_page", "ex_index_page");
    register_uniq_action("news_page", "ex_news_page");    
    register_action("common_web_structure", "ex_common_web_structure", 5);
    register_uniq_action("error_message_page", "ex_error_page");
    register_uniq_action("error_message_box", "ex_error_box");
}

function ex_common_web_structure() {
    plugin_manual_start("DebugWindow"); 
    getCSS_filePath("ExampleWeb");
    getCSS_filePath("ExampleWeb", "ExampleWeb-mobile");
    register_action("add_to_body", "ex_header",4);    
    register_uniq_action("get_footer", "ex_footer");    
    register_action("add_nav", "ex_nav", 5);
    
}

function ex_index_page(){
    do_action("common_web_structure");   
    plugin_manual_start("Newspage");
    news_main_page();
}

function ex_news_page() {
    do_action("common_web_structure");
    plugin_manual_start("Newspage"); 
    news_page();   
}

function ex_error_page() {        
    global $tpldata, $LANGDATA;
    
    if(empty($tpldata['E_TITLE'])) {
        $tpldata['E_TITLE'] = $LANGDATA['L_E_ERROR'];
    }
    if(empty($tpldata['E_BACKLINK_TITLE'])) {
        $tpldata['E_BACKLINK_TITLE'] = $LANGDATA['L_E_BACKLINK_TITLE'];       
    }    
    do_action("common_web_structure");    
    register_action("add_to_body", "ex_basic_error",5);
 }

 function ex_error_box () {
    global $tpldata, $LANGDATA;
    
    if(empty($tpldata['E_TITLE'])) {
        $tpldata['E_TITLE'] = $LANGDATA['L_E_ERROR'];
    }
    if(empty($tpldata['E_BACKLINK_TITLE'])) {
        $tpldata['E_BACKLINK_TITLE'] = $LANGDATA['L_E_BACKLINK_TITLE'];       
    }   
    return ex_basic_error();
 }

function ex_header() {
    return getTPL_file("ExampleWeb", "ex_header");
}

function ex_nav() {    
    return getTPL_file("ExampleWeb", "ex_navigator");
}

function ex_footer() {
    return getTPL_file("ExampleWeb", "ex_footer");
}

function ex_basic_error() {    
    return getTPL_file("ExampleWeb", "ex_basic_error");    
}
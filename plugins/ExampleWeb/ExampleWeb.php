<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function ExampleWeb_init(){
        
    if (DEBUG_PLUGINS_LOAD) { print_debug ("ExampleWeb initialized<br>"); }
    
    includeLang("ExampleWeb");    
     
    register_uniq_action("index_page", "ex_index_page");
    register_uniq_action("news_page", "ex_news_page");    
    register_action("common_web_structure", "ex_common_web_structure", "5");
    register_uniq_action("error_message", "ex_error_page");
}

function ex_common_web_structure() {
    plugin_manual_start("DebugWindow");      
    register_action("add_to_body", "ex_header","4");
    register_action("add_link", "ex_main_link","5");    
    register_uniq_action("get_footer", "ex_footer");    
    register_action("add_nav", "ex_nav", "5");
    
}

function ex_news_page() {
    do_action("common_web_structure");
    plugin_manual_start("Newspage");
    register_action("add_link", "news_add_link", "5");
    
    news_show();   
}

function ex_index_page(){
    do_action("common_web_structure");
    
    plugin_manual_start("Newspage");
    register_action("add_link", "news_add_link", "5");

    news_body_switcher();
}

function ex_error_page() {
    do_action("common_web_structure");  
    register_action("add_to_body", "ex_basic_error","5");
 }

function ex_main_link (){ 
    $link = "";
    $link .= tpl_get_file("css", "ExampleWeb", "");
    $link .= tpl_get_file("css", "ExampleWeb", "ExampleWeb-mobile");           
    return $link;
}

function ex_footer() {    
   return  tpl_get_file("tpl", "ExampleWeb", "ex_footer");
}

function ex_header() {    
   return  tpl_get_file("tpl", "ExampleWeb", "ex_header");   
}

function ex_nav() {    
    return tpl_get_file("tpl", "ExampleWeb", "ex_navigator");
}

function ex_basic_error() {    
    return tpl_get_file("tpl", "ExampleWeb", "ex_basic_error");
}
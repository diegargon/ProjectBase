<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */

function ExampleWeb_init(){
        
    if (DEBUG_PLUGINS_LOAD) { print_debug ("ExampleWeb initialized<br>"); }
    
    includeLang("ExampleWeb");    
     
    register_uniq_action("index_page", "ex_index_page");
    register_uniq_action("news_page", "ex_news_page"); 
    register_action("common_web_structure", "ex_common_web_structure", "5");
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

function ex_main_link (){ 
    if($CSSPATH = tpl_get_path("css", "ExampleWeb", "")) {
        $link = "<link rel='stylesheet' href='/$CSSPATH'>\n";
    }
    if($CSSPATH = tpl_get_path("css", "ExampleWeb", "ExampleWeb-mobile")) {
        $link .= "<link rel='stylesheet' href='/$CSSPATH'>\n";
    }
    
    return $link;
}

function ex_footer() {    
    if ($TPLPATH = tpl_get_path("tpl", "ExampleWeb", "ex_footer")) {
        return codetovar($TPLPATH, "");
    }        
}
function ex_header() {    
    if ($TPLPATH = tpl_get_path("tpl", "ExampleWeb", "ex_header")) {
        return codetovar($TPLPATH, "");
    }        
}
function ex_nav() {    
    if ($TPLPATH = tpl_get_path("tpl", "ExampleWeb", "ex_navigator")) {
        return codetovar($TPLPATH, "");
    }        
}
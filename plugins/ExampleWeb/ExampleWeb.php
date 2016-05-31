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
}

function ex_common_web_structure() {
    plugin_manual_start("DebugWindow");    
}

function ex_index_page(){       
    plugin_manual_start("Newspage");
    news_index_page();
}

function ex_news_page() {
    do_action("common_web_structure");
    plugin_manual_start("Newspage"); 
    news_page();   
}

<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function ExampleWeb_init(){
        
    print_debug ("ExampleWeb initialized", "PLUGIN_LOAD");
            
    includePluginFiles("ExampleWeb");
    
    register_uniq_action("index_page", "ex_index_page");
    register_uniq_action("news_page", "ex_news_page");
    register_action("common_web_structure", "ex_common_web_structure", 5);
}

function ex_common_web_structure() {
    plugin_manual_start("DebugWindow");
}

function ex_index_page(){
    plugin_manual_start("NewsAds");
    plugin_manual_start("NewsMedia");
    plugin_manual_start("NewsMediaUploader");
    plugin_manual_start("Newspage");
    its_server_stressed() ? false : plugin_manual_start("NewsSearch");
    news_index_page();
}

function ex_news_page() {
    plugin_manual_start("NewsAds");
    its_server_stressed() ? false : plugin_manual_start("NewsVote");
    plugin_manual_start("NewsMedia");
    plugin_manual_start("NewsMediaUploader");
    its_server_stressed() ? false : plugin_manual_start("NewsSearch");
    its_server_stressed() ? false : plugin_manual_start("NewsComments");
    plugin_manual_start("Newspage");
    news_page();
}

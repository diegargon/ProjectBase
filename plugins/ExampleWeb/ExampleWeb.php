<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function ExampleWeb_init() {

    print_debug("ExampleWeb initialized", "PLUGIN_LOAD");

    includePluginFiles("ExampleWeb");

    register_uniq_action("index_page", "ex_index_page");
    register_action("common_web_structure", "ex_common_web_structure");
    register_action("preload_Newspage_submitnews", "ex_Newspage_submitnews");
    register_action("begin_newsedit", "ex_Newspage_submitnews");
    register_action("begin_newspage", "ex_Newspage_submitnews");
    register_action("preload_Newspage_news", "ex_Newspage_news");
}

function ex_common_web_structure() {
    plugin_start("DebugWindow");
}

function ex_index_page() {
    plugin_start("NewsAds");
    plugin_start("Newspage");
    its_server_stressed() ? false : plugin_start("NewsSearch");
    news_portal();
}

function ex_Newspage_submitnews() {
    plugin_start("NewsMediaUploader");
}

function ex_Newspage_news() {
    plugin_start("NewsAds");
    plugin_start("NewsUserExtra");
    its_server_stressed() ? false : plugin_start("NewsVote");
    its_server_stressed() ? false : plugin_start("NewsSearch");
    its_server_stressed() ? false : plugin_start("NewsComments");
    plugin_start("Newspage");
}

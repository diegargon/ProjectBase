<?php

/*
 *  Copyright @ 2016 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

function ExampleWeb_init() {

    print_debug("ExampleWeb initialized", "PLUGIN_LOAD");

    includePluginFiles("ExampleWeb");

    register_uniq_action("index_page", "ex_index_page");
    register_action("common_web_structure", "ex_common_web_structure");
    register_action("preload_Newspage_submitnews", "ex_Newspage_submitnews");
    register_action("begin_newsedit", "ex_Newspage_submitnews");
    register_action("begin_newspage", "ex_Newspage_submitnews");
    register_action("preload_Newspage_news", "ex_Newspage_news");
    register_action("preload_Newspage_section", "ex_Newspage_section");
    register_action("preload_SMBasic_profile", "ex_preload_Profile", 4); //4 for call before SMBEXtra
}

function ex_common_web_structure() {
    plugin_start("DebugWindow");
}

function ex_index_page() {
    plugin_start("Newspage");
    its_server_stressed() ? null : plugin_start("NewsSearch");
    news_portal();
    //require_once("plugins/Newspage/portal.php");
    //$startpage =  $config['CON_FILE'] . "?module=Newspage&page=portal";
    //header('Location: '.$startpage);
}

function ex_Newspage_submitnews() {
    plugin_start("NewsMediaUploader");
    plugin_start("NewsSearch");
}

function ex_Newspage_section() {
    plugin_start("NewsSearch");
}

function ex_Newspage_news() {
    plugin_start("NewsUserExtra");
    its_server_stressed() ? null : plugin_start("NewsVote");
    its_server_stressed() ? null : plugin_start("NewsSearch");
    its_server_stressed() ? null : plugin_start("NewsComments");
    plugin_start("Newspage");
}

function ex_preload_Profile() {
    plugin_start("NewsUserExtra");
}

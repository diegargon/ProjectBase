<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function Newspage_init(){
    global $tpl;
    print_debug("Newspage Inititated", "PLUGIN_LOAD");

    includePluginFiles("Newspage");
    $tpl->getCSS_filePath("Newspage");
    $tpl->getCSS_filePath("Newspage", "Newspage-mobile");

    if (news_check_display_submit()) {
        register_action("nav_element", "news_menu_submit_news");
    }
}

function news_index_page (){
    do_action("news_index_begin");
    require_once("includes/news.portal.php");
    do_action("common_web_structure");
    news_portal();
}
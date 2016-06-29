<?php
/* 
 *  Copyright @ 2016 Diego Garcia
 */
if (!defined('IN_WEB')) { exit; }

function NewsMedia_init() { 
    global $tpl, $config;
    print_debug("NewsMedia initiated", "PLUGIN_LOAD");
    includePluginFiles("NewsMedia");
    
    $tpl->getCSS_filePath("NewsMedia");
    $tpl->getCSS_filePath("NewsMedia", "NewsMedia-mobile");

    if ($config['NEWS_ADD_MAIN_MEDIA']) {
        register_main_media();
    }
    if ($config['NEWS_ADD_EXTRA_MEDIA']) {
        register_extra_media();
    }
}

function register_main_media() {
    required_once("NewsMedia-mainmedia.php");
    register_action("news_edit_form_add", "NewsEditFormMediaTpl");
    register_action("news_new_form_add", "NewsFormMediaTpl");
    register_action("news_newlang_form_add", "NewsEditFormMediaTpl");
    register_action("news_form_add_check", "NewsMediaCheck");
    register_action("news_create_new_insert", "NewsMediaInsertNew");
    register_action("news_form_update", "news_form_media_update");
    register_action("news_featured_mod", "news_media_featured_mod");
    register_action("news_get_news_mod", "news_media_getnews_mod");
    register_action("news_show_page", "news_media_page_mod");    
}

function register_extra_media() {
    required_once("NewsMedia-extramedia.php");
    
}

